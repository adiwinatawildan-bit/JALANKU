<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Facility;
use App\Models\Opd;
use App\Models\PriorityCriterion;
use App\Models\PriorityWeight;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\TopsisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class SuperAdminController extends Controller
{
    protected TopsisService $topsisService;

    public function __construct(TopsisService $topsisService)
    {
        $this->topsisService = $topsisService;
    }

    public function dashboard()
    {
        // 28. DASHBOARD SUPER ADMIN
        $stats = [
            'total_user' => User::count(),
            'admin' => User::whereHas('role', fn($q) => $q->where('name', 'admin'))->count(),
            'opd' => Opd::count(),
            'petugas' => User::whereHas('role', fn($q) => $q->where('name', 'opd'))->count(),
            'masyarakat' => User::whereHas('role', fn($q) => $q->where('name', 'masyarakat'))->count(),
            'opd_aktif' => Opd::where('is_active', true)->count(),
        ];

        $recentUsers = User::with(['role', 'opd'])->latest()->take(6)->get();
        $recentAuditLogs = AuditLog::with('user')->latest()->take(8)->get();
        $criteria = PriorityCriterion::orderBy('code')->get();

        return view('superadmin.dashboard', compact(
            'stats',
            'recentUsers',
            'recentAuditLogs',
            'criteria'
        ));
    }

    public function users(Request $request)
    {
        $query = User::with(['role', 'opd']);

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $roles = Role::all();
        $opds = Opd::where('is_active', true)->get();

        return view('superadmin.users.index', compact('users', 'roles', 'opds'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role_id' => ['required', 'exists:roles,id'],
            'opd_id' => ['nullable', 'exists:opds,id'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'password' => ['required', Password::min(6)],
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role_id' => $validated['role_id'],
            'opd_id' => $validated['opd_id'] ?? null,
            'avatar_url' => $avatarPath,
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        AuditLog::record(
            activity: 'Pembuatan User Baru',
            targetType: 'User',
            targetId: $user->id,
            description: "Super Admin membuat user baru {$user->name} ({$user->role->display_name}).",
            userId: Auth::id()
        );

        return back()->with('success', "User {$user->name} berhasil ditambahkan.");
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'phone' => ['nullable', 'string', 'max:20'],
            'role_id' => ['required', 'exists:roles,id'],
            'opd_id' => ['nullable', 'exists:opds,id'],
            'is_active' => ['required', 'boolean'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'password' => ['nullable', Password::min(6)],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->role_id = $validated['role_id'];
        $user->opd_id = $validated['opd_id'] ?? null;
        $user->is_active = $validated['is_active'];

        if ($request->hasFile('avatar')) {
            if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                Storage::disk('public')->delete($user->avatar_url);
            }
            $user->avatar_url = $request->file('avatar')->store('avatars', 'public');
        } elseif ($request->boolean('remove_avatar')) {
            if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                Storage::disk('public')->delete($user->avatar_url);
            }
            $user->avatar_url = null;
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        AuditLog::record(
            activity: 'Update Informasi User',
            targetType: 'User',
            targetId: $user->id,
            description: "Super Admin memperbarui data user {$user->name}.",
            userId: Auth::id()
        );

        return back()->with('success', "Data user {$user->name} berhasil diperbarui.");
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun yang sedang Anda gunakan.');
        }

        $name = $user->name;

        // Hapus notifikasi user (custom table dengan user_id)
        \Illuminate\Support\Facades\DB::table('notifications')->where('user_id', $user->id)->delete();
        $user->delete();

        AuditLog::record(
            activity: 'Hapus User Pengguna',
            targetType: 'User',
            targetId: $id,
            description: "Super Admin menghapus akun user {$name}.",
            userId: Auth::id()
        );

        return back()->with('success', "User {$name} berhasil dihapus permanen dari sistem.");
    }

    public function roles()
    {
        $roles = Role::withCount('users')->get();
        return view('superadmin.roles.index', compact('roles'));
    }

    public function opds()
    {
        $opds = Opd::withCount(['users', 'reports'])->get();
        return view('superadmin.opds.index', compact('opds'));
    }

    public function storeOpd(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:opds'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
        ]);

        $opd = Opd::create($validated);

        AuditLog::record(
            activity: 'Tambah Master Data OPD',
            targetType: 'Opd',
            targetId: $opd->id,
            description: "OPD baru {$opd->name} ditambahkan.",
            userId: Auth::id()
        );

        return back()->with('success', "OPD {$opd->name} berhasil ditambahkan.");
    }

    public function updateOpd(Request $request, $id)
    {
        $opd = Opd::findOrFail($id);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:opds,code,' . $id],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $opd->update($validated);

        AuditLog::record(
            activity: 'Update Master Data OPD',
            targetType: 'Opd',
            targetId: $opd->id,
            description: "Data OPD {$opd->name} diperbarui.",
            userId: Auth::id()
        );

        return back()->with('success', "Data OPD {$opd->name} berhasil diperbarui.");
    }

    public function deleteOpd($id)
    {
        $opd = Opd::findOrFail($id);
        $name = $opd->name;

        // Disassociate related users and reports
        User::where('opd_id', $id)->update(['opd_id' => null]);
        Report::where('opd_id', $id)->update(['opd_id' => null]);

        $opd->delete();

        AuditLog::record(
            activity: 'Hapus Master Data OPD',
            targetType: 'Opd',
            targetId: $id,
            description: "Super Admin menghapus OPD {$name}.",
            userId: Auth::id()
        );

        return back()->with('success', "OPD {$name} berhasil dihapus permanen dari sistem.");
    }

    public function criteria()
    {
        $criteria = PriorityCriterion::orderBy('code')->get();
        $totalWeight = $criteria->sum('weight_percentage');

        return view('superadmin.criteria.index', compact('criteria', 'totalWeight'));
    }

    public function updateWeights(Request $request)
    {
        // 23. KRITERIA PRIORITAS (Bobot total harus 100%)
        $weights = $request->input('weights', []);

        $sum = array_sum(array_map('floatval', $weights));

        if (abs($sum - 100.0) > 0.01) {
            return back()->with('error', "Total akumulasi bobot harus tepat 100.00% (Saat ini: {$sum}%). Silakan sesuaikan kembali.")->withInput();
        }

        DB::transaction(function () use ($weights) {
            foreach ($weights as $code => $weightVal) {
                $criterion = PriorityCriterion::where('code', $code)->first();
                if ($criterion) {
                    $criterion->update(['weight_percentage' => (float) $weightVal]);

                    PriorityWeight::create([
                        'priority_criterion_id' => $criterion->id,
                        'weight_percentage' => (float) $weightVal,
                        'updated_by' => Auth::id(),
                    ]);
                }
            }

            // Recalculate TOPSIS scores with new weights
            $this->topsisService->calculateAll();

            AuditLog::record(
                activity: 'Penyesuaian Bobot Kriteria TOPSIS',
                targetType: 'PriorityCriterion',
                targetId: 'ALL',
                description: 'Super Admin memperbarui bobot 8 kriteria SPK TOPSIS.',
                userId: Auth::id()
            );
        });

        return back()->with('success', 'Bobot 8 kriteria TOPSIS berhasil diperbarui dan seluruh peringkat prioritas jalan telah dikalkulasi ulang.');
    }

    public function auditLogs(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('target_type')) {
            $query->where('target_type', $request->target_type);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('activity', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('target_id', 'like', "%{$q}%");
            });
        }

        $logs = $query->latest()->paginate(25)->withQueryString();
        $users = User::all();

        return view('superadmin.audit-logs.index', compact('logs', 'users'));
    }

    public function settings()
    {
        $settings = SystemSetting::all();
        return view('superadmin.settings.index', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'app_name' => ['nullable', 'string', 'max:100'],
            'app_slogan' => ['nullable', 'string', 'max:255'],
            'app_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'default_map_lat' => ['nullable', 'string'],
            'default_map_lng' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('app_logo')) {
            $currentLogo = SystemSetting::get('app_logo');
            if ($currentLogo && Storage::disk('public')->exists($currentLogo)) {
                Storage::disk('public')->delete($currentLogo);
            }
            $logoPath = $request->file('app_logo')->store('settings', 'public');
            SystemSetting::set('app_logo', $logoPath, 'branding', 'string');
        } elseif ($request->boolean('remove_logo')) {
            $currentLogo = SystemSetting::get('app_logo');
            if ($currentLogo && Storage::disk('public')->exists($currentLogo)) {
                Storage::disk('public')->delete($currentLogo);
            }
            SystemSetting::set('app_logo', '', 'branding', 'string');
        }

        $data = $request->except(['_token', 'app_logo', 'remove_logo']);

        foreach ($data as $key => $value) {
            if ($value !== null) {
                SystemSetting::set($key, $value);
            }
        }

        AuditLog::record(
            activity: 'Update Pengaturan Sistem',
            targetType: 'Setting',
            targetId: 'SYSTEM',
            description: 'Super Admin memperbarui konfigurasi & logo sistem.',
            userId: Auth::id()
        );

        return back()->with('success', 'Pengaturan dan logo sistem berhasil disimpan.');
    }
}
