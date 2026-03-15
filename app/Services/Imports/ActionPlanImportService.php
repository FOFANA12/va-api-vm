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

        $structure = Auth::user()?->employee?->structure;
        if (!$structure) {
            return [
                'success' => false,
                'errors' => [[
                    'row' => 0,
                    'errors' => [__('app/action_plan.import.structure_missing')],
                ]],
            ];
        }

        if ($structure->type !== 'OPERATIONAL') {
            return [
                'success' => false,
                'errors' => [[
                    'row' => 0,
                    'errors' => [__('app/action_plan.import.structure_not_operational')],
                ]],
            ];
        }

        $errors = [];
        $imported = 0;

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $lineNumber = $index + 2;

                $row['date_debut'] = $this->normalizeDate($row['date_debut'] ?? null);
                $row['date_fin']   = $this->normalizeDate($row['date_fin'] ?? null);

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

                $responsible = null;

                if (!empty($row['responsable'])) {
                    $responsible = User::where('email', $row['responsable'])
                        ->whereHas('employee', function ($q) use ($structure) {
                            $q->where('structure_uuid', $structure->uuid);
                        })
                        ->first();

                    if (!$responsible) {
                        $errors[] = [
                            'row' => $lineNumber,
                            'errors' => [
                                __('app/action_plan.import.responsible_not_found', [
                                    'email' => $row['responsable'],
                                ]),
                            ],
                        ];
                        if (count($errors) >= self::MAX_ERRORS) {
                            break;
                        }
                        continue;
                    }
                }

                $existing = ActionPlan::where('structure_uuid', $structure->uuid)
                    ->where('name', $row['nom'])
                    ->first();

                if ($existing) {
                    if (!$updateActionPlanIfExists) {
                        $errors[] = [
                            'row' => $lineNumber,
                            'errors' => [
                                __('app/action_plan.import.already_exists', [
                                    'name' => $row['nom'],
                                    'structure' => $structure->abbreviation,
                                ]),
                            ],
                        ];
                        if (count($errors) >= self::MAX_ERRORS) {
                            break;
                        }
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
            'nom' => ['bail', 'required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'date_debut' => ['nullable', 'date_format:Y-m-d'],
            'date_fin' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_debut'],
            'responsable' => ['nullable', 'email'],
        ], [], [
            'nom' => __('app/action_plan.request.name'),
            'date_debut' => __('app/action_plan.request.start_date'),
            'date_fin' => __('app/action_plan.request.end_date'),
            'responsable' => __('app/action_plan.request.responsible'),
        ]);
    }

    private function normalizeDate(?string $date): ?string
    {
        if (!$date) {
            return null;
        }

        $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y'];

        foreach ($formats as $format) {
            try {
                return \Carbon\Carbon::createFromFormat($format, $date)->format('Y-m-d');
            } catch (\Exception $e) {
            }
        }

        return $date;
    }
}
