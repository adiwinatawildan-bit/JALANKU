<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\ReportPhoto;
use App\Models\ProgressPhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageService
{
    protected string $bucket;
    protected ?string $supabaseUrl;
    protected ?string $supabaseKey;

    public function __construct()
    {
        $this->bucket = env('SUPABASE_BUCKET', 'road-reports');
        $this->supabaseUrl = env('SUPABASE_URL');
        $this->supabaseKey = env('SUPABASE_KEY');
    }

    /**
     * Upload an initial report photo.
     * Path: road-reports/reports/{report_id}/initial/foto-{index}.{ext}
     */
    public function uploadInitialPhoto(UploadedFile $file, int $reportId, int $photoIndex = 1): array
    {
        $ext = $file->getClientOriginalExtension() ?: 'jpg';
        $fileName = "foto-{$photoIndex}.{$ext}";
        $relativePath = "reports/{$reportId}/initial/{$fileName}";

        return $this->storeFile($file, $relativePath, $fileName);
    }

    /**
     * Upload a survey photo.
     * Path: road-reports/reports/{report_id}/survey/foto-{index}.{ext}
     */
    public function uploadSurveyPhoto(UploadedFile $file, int $reportId, int $photoIndex = 1): array
    {
        $ext = $file->getClientOriginalExtension() ?: 'jpg';
        $fileName = "foto-{$photoIndex}.{$ext}";
        $relativePath = "reports/{$reportId}/survey/{$fileName}";

        return $this->storeFile($file, $relativePath, $fileName);
    }

    /**
     * Upload a weekly progress photo.
     * Path: road-reports/reports/{report_id}/progress/week-{week}/foto-{index}.{ext}
     */
    public function uploadProgressPhoto(UploadedFile $file, int $reportId, int $weekNumber, int $photoIndex = 1): array
    {
        $ext = $file->getClientOriginalExtension() ?: 'jpg';
        $fileName = "foto-{$photoIndex}.{$ext}";
        $relativePath = "reports/{$reportId}/progress/week-{$weekNumber}/{$fileName}";

        return $this->storeFile($file, $relativePath, $fileName);
    }

    /**
     * Internal store handler to Supabase or Local Public disk
     */
    protected function storeFile(UploadedFile $file, string $relativePath, string $fileName): array
    {
        $fullPath = "{$this->bucket}/{$relativePath}";

        if ($this->hasSupabaseConfig()) {
            try {
                $endpoint = rtrim($this->supabaseUrl, '/') . "/storage/v1/object/{$this->bucket}/{$relativePath}";
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->supabaseKey,
                    'apikey' => $this->supabaseKey,
                ])->withBody(file_get_contents($file->getRealPath()), $file->getMimeType())
                  ->post($endpoint);

                if ($response->successful()) {
                    $publicUrl = rtrim($this->supabaseUrl, '/') . "/storage/v1/object/public/{$this->bucket}/{$relativePath}";
                    return [
                        'success' => true,
                        'file_name' => $fileName,
                        'file_path' => $fullPath,
                        'file_url' => $publicUrl,
                        'storage_type' => 'supabase',
                    ];
                }
                Log::warning('Supabase storage upload returned status ' . $response->status() . ': ' . $response->body() . '. Falling back to local storage.');
            } catch (\Exception $e) {
                Log::warning('Supabase storage error: ' . $e->getMessage() . '. Falling back to local storage.');
            }
        }

        // Local Storage Fallback
        $localPath = Storage::disk('public')->putFileAs("road-reports/" . dirname($relativePath), $file, $fileName);
        $publicUrl = asset('storage/' . $localPath);

        return [
            'success' => true,
            'file_name' => $fileName,
            'file_path' => $fullPath,
            'file_url' => $publicUrl,
            'storage_type' => 'local',
        ];
    }

    /**
     * Safe delete for ReportPhoto (Deletes from Storage first, then MySQL if success)
     */
    public function deleteReportPhoto(ReportPhoto $photo, ?int $userId = null): bool
    {
        $storageDeleted = $this->deleteStorageFile($photo->file_path);

        if (!$storageDeleted) {
            Log::error("Failed to delete storage file: {$photo->file_path}");
            return false;
        }

        AuditLog::record(
            activity: 'Menghapus foto laporan #' . $photo->id,
            targetType: 'ReportPhoto',
            targetId: $photo->id,
            description: "Foto {$photo->file_name} pada laporan #{$photo->report_id} berhasil dihapus.",
            userId: $userId
        );

        $photo->delete();
        return true;
    }

    /**
     * Safe delete for ProgressPhoto (Deletes from Storage first, then MySQL if success)
     */
    public function deleteProgressPhoto(ProgressPhoto $photo, ?int $userId = null): bool
    {
        $storageDeleted = $this->deleteStorageFile($photo->file_path);

        if (!$storageDeleted) {
            Log::error("Failed to delete storage file: {$photo->file_path}");
            return false;
        }

        AuditLog::record(
            activity: 'Menghapus foto progress #' . $photo->id,
            targetType: 'ProgressPhoto',
            targetId: $photo->id,
            description: "Foto progress {$photo->file_name} berhasil dihapus.",
            userId: $userId
        );

        $photo->delete();
        return true;
    }

    /**
     * Delete file from Supabase / Local storage
     */
    public function deleteStorageFile(string $filePath): bool
    {
        // Strip bucket name prefix if present
        $cleanPath = str_starts_with($filePath, "{$this->bucket}/")
            ? substr($filePath, strlen("{$this->bucket}/"))
            : $filePath;

        if ($this->hasSupabaseConfig()) {
            try {
                $endpoint = rtrim($this->supabaseUrl, '/') . "/storage/v1/object/{$this->bucket}/{$cleanPath}";
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->supabaseKey,
                    'apikey' => $this->supabaseKey,
                ])->delete($endpoint);

                if ($response->successful()) {
                    return true;
                }
            } catch (\Exception $e) {
                Log::warning('Supabase storage delete failed: ' . $e->getMessage());
            }
        }

        // Local deletion
        $localRelative = 'road-reports/' . $cleanPath;
        if (Storage::disk('public')->exists($localRelative)) {
            return Storage::disk('public')->delete($localRelative);
        }

        // If file doesn't exist on disk, allow deletion to proceed cleanly
        return true;
    }

    protected function hasSupabaseConfig(): bool
    {
        return !empty($this->supabaseUrl) && !empty($this->supabaseKey);
    }
}
