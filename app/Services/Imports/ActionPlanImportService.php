<?php

namespace App\Services\Imports;

use App\Helpers\ReferenceGenerator;
use App\Models\ActionPlan;
use App\Models\Structure;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ActionPlanImportService
{
    protected array $errors = [];
    protected int $imported = 0;
    private const MAX_ERRORS = 3;

    /**
     * Import accounts from Excel / CSV file.
     */
    public function import(
        string $filePath,
        bool $updateActionPlanIfExists = false,
    ): array {
        $rows = $this->readFile($filePath);

        if (empty($rows)) {
            return [
                'success' => false,
                'errors' => [
                    [
                        'row' => 0,
                        'errors' => [__('app/action_plan.import.file_empty')],
                    ],
                ],
            ];
        }

        $errors = [];
        $imported = 0;

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $lineNumber = $index + 2;

                $validator = $this->validateRow($row);

                if ($validator->fails()) {
                    $field = array_key_first($validator->errors()->messages());

                    $message = $validator->errors()->first($field);

                    $errors[] = [
                        'row' => $lineNumber,
                        'errors' => [$message],
                    ];

                    if (count($errors) >= self::MAX_ERRORS) {
                        break;
                    }

                    continue;
                }

                $structure = Structure::where('abbreviation', $row['structure'])->where('type', 'OPERATIONAL')->first();

                if (!$structure) {
                    $errors[] = [
                        'row' => $lineNumber,
                        'errors' => [
                            __('app/action_plan.import.structure_not_found', [
                                'abbreviation' => $row['structure'],
                            ]),
                        ],
                    ];
                    continue;
                }

                $responsible = null;

                if (!empty($row['responsable_email'])) {
                    $responsible = User::whereHas('employee')->where('email', $row['responsable_email'])->first();

                    if (!$responsible && $responsible?->employee->structure_uuid === $structure->uuid) {
                        $errors[] = [
                            'row' => $lineNumber,
                            'errors' => [
                                __('app/action_plan.import.responsible_not_found', [
                                    'email' => $row['responsable_email'],
                                ]),
                            ],
                        ];
                        continue;
                    }
                }

                $existing = ActionPlan::where('structure_uuid', $structure->uuid)
                    ->where('name', $row['nom'])
                    ->first();

                if ($existing) {
                    if ($existing && !$updateActionPlanIfExists) {
                        $errors[] = [
                            'row' => $lineNumber,
                            'errors' => [
                                __('app/action_plan.import.already_exists', [
                                    'name' => $row['nom'],
                                    'structure' => $structure->abbreviation,
                                ]),
                            ],
                        ];
                        continue;
                    }

                    $existing->update([
                        'description' => $row['description'] ?? null,
                        'start_date' => $row['date_debut'] ?? null,
                        'end_date' => $row['date_fin'] ?? null,
                        'responsible_uuid' => $responsible?->uuid,
                        'updated_by' => Auth::user()?->uuid,
                    ]);
                } else {
                    $actionPlan = ActionPlan::create([
                        'structure_uuid' => $structure->uuid,
                        'responsible_uuid' => $responsible?->uuid,
                        'name' => $row['nom'],
                        'description' => $row['description'] ?? null,
                        'start_date' => $row['date_debut'] ?? null,
                        'end_date' => $row['date_fin'] ?? null,
                        'status' => false,
                        'created_by' => Auth::user()?->uuid,
                        'updated_by' => Auth::user()?->uuid,
                    ]);
                    $actionPlan->update([
                        'reference' => ReferenceGenerator::generateActionPlanReference($actionPlan->id),
                    ]);
                }

                $imported++;
            }

            if (!empty($errors)) {
                DB::rollBack();

                return [
                    'success' => false,
                    'errors' => $errors,
                ];
            }

            DB::commit();

            return [
                'success'  => true,
                'imported' => $imported,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            return [
                'success' => false,
                'errors' => [
                    [
                        'row' => 0,
                        'errors' => [
                            config('app.debug')
                                ? $e->getMessage()
                                : __('app/action_plan.import.unexpected_error'),
                        ],
                    ],
                ],
            ];
        }
    }

    /**
     * Read Excel / CSV file and return rows.
     */
    private function readFile(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (count($rows) < 2) {
            return [];
        }

        $header = array_map('trim', $rows[1]);
        unset($rows[1]);

        $data = [];

        foreach ($rows as $row) {
            $line = [];

            foreach ($header as $key => $column) {
                if ($column) {
                    $line[$column] = trim((string) ($row[$key] ?? ''));
                }
            }

            if (array_filter($line)) {
                $data[] = $line;
            }
        }

        return $data;
    }

    /**
     * Validate a single row.
     */
    private function validateRow(array $row)
    {
        return Validator::make($row, [
            'structure' => ['bail', 'required', 'max:20'],
            'nom' => ['bail', 'required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'date_debut' => ['nullable', 'date_format:Y-m-d'],
            'date_fin' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_debut'],
            'responsable_email' => ['nullable', 'email'],
        ], [], [
            'structure' => __('app/action_plan.request.structure'),
            'nom' => __('app/action_plan.request.name'),
            'date_debut' => __('app/action_plan.request.start_date'),
            'date_fin' => __('app/action_plan.request.end_date'),
            'responsable_email' => __('app/action_plan.request.responsible'),
        ]);
    }
}
