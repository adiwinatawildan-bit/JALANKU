<?php

use App\Models\Report;
use App\Services\TopsisService;
use App\Services\YoloService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('reports:reanalyze-yolo {report_id?}', function (?string $report_id = null) {
    $yolo = app(YoloService::class);
    $reports = $report_id ? Report::where('id', $report_id)->get() : Report::all();
    
    foreach ($reports as $report) {
        $this->info("Reanalyzing report #{$report->id} ({$report->ticket_number})...");
        $res = $yolo->analyzeReport($report);
        $this->line("Result: " . json_encode($res));
    }
    
    app(TopsisService::class)->calculateAll();
    $this->info("Recalculated TOPSIS successfully.");
})->purpose('Reanalyze reports using YOLO model and recalculate TOPSIS');

