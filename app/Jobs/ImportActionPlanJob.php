<?php

namespace App\Jobs;

use App\Models\ActionPlan;
use App\Models\ImportLog;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportActionPlanJob implements ShouldQueue
{
    use Queueable;
    public string $logUuid;
    public string $structureUuid;
    /**
     * Create a new job instance.
     */
    public function __construct(string $logUuid, string $structureUuid)
    {
        $this->logUuid = $logUuid;
        $this->structureUuid = $structureUuid;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $log = ImportLog::where('uuid', $this->logUuid)->firstOrFail();
        $log->update([
            'status' => 'processing',
            'message' => __('app/import.processing'),
        ]);

        try {
            $disk = Storage::disk('local');
            $relativePath = "uploads/imports/{$log->identifier}";

            if (!$disk->exists($relativePath)) {
                throw new \Exception(__('app/import.file_not_found') . " ({$relativePath})");
            }

            $filePath = $disk->path($relativePath);

            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            $expectedColumns = [
                'nom',
                'responsable',
                'description',
                'date_debut',
                'date_fin',
                'statut',
            ];

            $columns = [];

            foreach (range('A', 'F') as $col) {
                $value = strtolower(trim((string) $sheet->getCell($col . '1')->getValue()));
                $columns[] = $value;
            }

            if ($columns !== $expectedColumns) {
                throw new \Exception(__('app/import.invalid_columns'));
            }

            DB::beginTransaction();

            $row = 2; // Ligne 1 = Header

            while ($sheet->getCell("A{$row}")->getValue()) {

                $name = trim((string) $sheet->getCell("A{$row}")->getValue());
                $responsibleEmail = trim((string) $sheet->getCell("B{$row}")->getValue());
                $description = trim((string) $sheet->getCell("C{$row}")->getValue());
                $startRaw = trim((string) $sheet->getCell("D{$row}")->getFormattedValue());
                $endRaw = trim((string) $sheet->getCell("E{$row}")->getFormattedValue());

                if (empty($name)) {
                    $row++;
                    continue;
                }

                $existing = ActionPlan::where('structure_uuid', $this->structureUuid)
                    ->where('name', $name)
                    ->first();

                if ($existing) {
                    $row++;
                    continue;
                }

                $startDate = $this->parseDateFR($startRaw);
                $endDate   = $this->parseDateFR($endRaw);

                $status = 0;

                $responsibleUser = null;

                if (!empty($responsibleEmail)) {
                    $responsibleUser = User::where('email', $responsibleEmail)->whereHas('employee')->first();
                }

                ActionPlan::create([
                    'structure_uuid' => $this->structureUuid,
                    'name' => $name,
                    'responsible_uuid' => $responsibleUser?->uuid,
                    'description' => $description,
                    'start_date' => $startDate ?: null,
                    'end_date' => $endDate ?: null,
                    'status' => $status,
                    'created_by' => $log->created_by,
                    'updated_by' => $log->created_by,
                ]);

                $row++;
            }

            DB::commit();

            $log->update([
                'status' => 'success',
                'message' => __('app/import.success'),
            ]);
        } catch (Throwable $e) {
            DB::rollBack();

            $log->update([
                'status'  => 'error',
                'message' => __('app/import.error'),
            ]);
        }
    }

    private function parseDateFR(?string $date)
    {
        if (!$date || !str_contains($date, '/')) {
            return null;
        }

        [$d, $m, $y] = explode('/', $date);

        if (!checkdate((int)$m, (int)$d, (int)$y)) {
            return null;
        }

        return "{$y}-{$m}-{$d}";
    }
}
