<?php

namespace App\Services\Imports;

use App\Helpers\ReferenceGenerator;
use App\Models\Action;
use App\Models\ActionDomain;
use App\Models\ActionPlan;
use App\Models\ActionStatus;
use App\Models\CapabilityDomain;
use App\Support\Currency;
use App\Models\DelegatedProjectOwner;
use App\Models\Department;
use App\Models\ElementaryLevel;
use App\Models\Municipality;
use App\Models\ProjectOwner;
use App\Models\Region;
use App\Models\StrategicDomain;
use App\Models\Structure;
use App\Models\User;
use App\Support\ChartType;
use App\Support\GenerateDocumentTypes;
use App\Support\PriorityLevel;
use App\Support\RiskLevel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ActionImportService
{
    protected array $errors = [];
    protected int $imported = 0;
    private const MAX_ERRORS = 3;

    private function getAllowedStructureUuids(Structure $structure): array
    {
        $structures = [$structure->uuid];

        $collectParents = function ($s) use (&$structures, &$collectParents) {
            if ($s->parent && $s->parent->status) {
                $structures[] = $s->parent->uuid;
                $collectParents($s->parent);
            }
        };

        $structure->load('parent');
        $collectParents($structure);

        return array_unique($structures);
    }

    private function getDescendantStructureUuids(Structure $structure): array
    {
        $uuids = [];

        $collect = function ($s) use (&$uuids, &$collect) {
            foreach ($s->children as $child) {
                if ($child->status) {
                    $uuids[] = $child->uuid;
                    $collect($child);
                }
            }
        };

        $structure->load('children');
        $collect($structure);

        return $uuids;
    }

    /**
     * Import accounts from Excel / CSV file.
     */
    public function import(string $filePath): array
    {
        $rows = $this->readFile($filePath);

        if (empty($rows)) {
            return [
                'success' => false,
                'errors' => [
                    [
                        'row' => 0,
                        'errors' => [__('app/action.import.file_empty')],
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
                    'errors' => [__('app/action.import.structure_missing')],
                ]],
            ];
        }

        if ($structure->type !== 'OPERATIONAL') {
            return [
                'success' => false,
                'errors' => [[
                    'row' => 0,
                    'errors' => [__('app/action.import.structure_not_operational')],
                ]],
            ];
        }

        $structure->load(['parent', 'children']);

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

                $responsibleStructureUuid = null;
                if (!empty($row['structure_pilote'])) {
                    $allowed = $this->getDescendantStructureUuids($structure);

                    $responsibleStructure = Structure::where('abbreviation', $row['structure_pilote'])
                        ->whereIn('uuid', $allowed)
                        ->first();

                    if (!$responsibleStructure) {
                        $errors[] = [
                            'row' => $lineNumber,
                            'errors' => [__(
                                'app/action.import.responsible_structure_invalid',
                                [
                                    'abbreviation' => $row['structure_pilote'],
                                ]
                            )],
                        ];

                        if (count($errors) >= self::MAX_ERRORS) {
                            break;
                        }
                        continue;
                    }

                    $responsibleStructureUuid = $responsibleStructure->uuid;
                }

                $responsibleUuid = null;
                if (!empty($row['responsable'])) {
                    if (!$responsibleStructureUuid) {
                        $errors[] = [
                            'row' => $lineNumber,
                            'errors' => [__('app/action.import.pilot_structure_required')],
                        ];
                        if (count($errors) >= self::MAX_ERRORS) {
                            break;
                        }
                        continue;
                    }

                    $responsible = User::where('email', $row['responsable'])
                        ->whereHas('employee', function ($q) use ($responsibleStructureUuid) {
                            $q->where('structure_uuid', $responsibleStructureUuid);
                        })
                        ->first();

                    if (!$responsible) {
                        $errors[] = [
                            'row' => $lineNumber,
                            'errors' => [__('app/action.import.responsible_not_found', [
                                'abbreviation' => $row['structure_pilote'],
                            ])],
                        ];
                        if (count($errors) >= self::MAX_ERRORS) {
                            break;
                        }
                        continue;
                    }

                    $responsibleUuid = $responsible->uuid;
                }


                $actionPlan = ActionPlan::where('reference', $row['plan_action'])
                    ->where('structure_uuid', $structure->uuid)
                    ->first();

                if (!$actionPlan) {
                    $errors[] = [
                        'row' => $lineNumber,
                        'errors' => [
                            __('app/action.import.action_plan_not_found', [
                                'name' => $row['plan_action'],
                                'structure' => $structure->abbreviation,
                            ]),
                        ],
                    ];
                    if (count($errors) >= self::MAX_ERRORS) {
                        break;
                    }
                    continue;
                }


                $priorityInput = $row['priorite'] ?? null;
                $priority = PriorityLevel::fromLabel($priorityInput);

                if (!$priority) {
                    $errors[] = [
                        'row' => $lineNumber,
                        'errors' => [
                            __('app/action.import.priority_invalid', [
                                'value' => $priorityInput,
                            ]),
                        ],
                    ];
                    if (count($errors) >= self::MAX_ERRORS) {
                        break;
                    }
                    continue;
                }
                $priorityCode = $priority->code;

                $riskInput = $row['risque'] ?? null;
                $risk = RiskLevel::fromLabel($riskInput);

                if (!$risk) {
                    $errors[] = [
                        'row' => $lineNumber,
                        'errors' => [
                            __('app/action.import.risk_level_invalid', [
                                'value' => $riskInput,
                            ]),
                        ],
                    ];
                    if (count($errors) >= self::MAX_ERRORS) {
                        break;
                    }
                    continue;
                }
                $riskLevelCode = $risk->code;

                $planTypeInput = $row['type_plan'] ?? null;
                $planType = GenerateDocumentTypes::fromCode($planTypeInput);

                if (!$planType) {
                    $errors[] = [
                        'row' => $lineNumber,
                        'errors' => [
                            __('app/action.import.plan_type_invalid', [
                                'value' => $planTypeInput,
                            ]),
                        ],
                    ];
                    if (count($errors) >= self::MAX_ERRORS) {
                        break;
                    }
                    continue;
                }
                $planTypeCode = $planType->code;

                $chartTypeInput = $row['type_graphique'] ?? null;
                $chartType = ChartType::fromLabel($chartTypeInput);

                if (!$chartType) {
                    $errors[] = [
                        'row' => $lineNumber,
                        'errors' => [
                            __('app/action.import.chart_type_invalid', [
                                'value' => $chartTypeInput,
                            ]),
                        ],
                    ];
                    if (count($errors) >= self::MAX_ERRORS) {
                        break;
                    }
                    continue;
                }

                $chartTypeCode = $chartType->code;

                $allowedStructureUuids = $this->getAllowedStructureUuids($structure);
                $projectOwner = ProjectOwner::where('name', $row['maitre_ouvrage'])
                    ->where(function ($q) use ($allowedStructureUuids) {
                        $q->whereNull('structure_uuid')
                            ->orWhereIn('structure_uuid', $allowedStructureUuids);
                    })
                    ->first();

                if (!$projectOwner) {
                    $errors[] = [
                        'row' => $lineNumber,
                        'errors' => [
                            __('app/action.import.project_owner_not_found', [
                                'name' => $row['maitre_ouvrage'],
                            ]),
                        ],
                    ];
                    if (count($errors) >= self::MAX_ERRORS) {
                        break;
                    }
                    continue;
                }

                $delegatedProjectOwner = DelegatedProjectOwner::where('name', $row['maitre_ouvrage_delegue'])
                    ->where('project_owner_uuid', $projectOwner->uuid)
                    ->first();

                if (!$delegatedProjectOwner) {
                    $errors[] = [
                        'row' => $lineNumber,
                        'errors' => [
                            __('app/action.import.delegated_project_owner_not_found', [
                                'name' => $row['maitre_ouvrage_delegue'],
                                'project_owner' => $row['maitre_ouvrage'],
                            ]),
                        ],
                    ];
                    if (count($errors) >= self::MAX_ERRORS) {
                        break;
                    }
                    continue;
                }

                $currencyCode = Currency::getDefault()['code'];

                $actionDomainUuid = null;
                $strategicDomainUuid = null;
                $capabilityDomainUuid = null;
                $elementaryLevelUuid = null;

                if (!empty($row['domaine_action'])) {
                    $actionDomain = ActionDomain::where('name', $row['domaine_action'])->whereNotIn('status', ['closed', 'stopped'])->first();

                    if (!$actionDomain) {
                        $errors[] = [
                            'row' => $lineNumber,
                            'errors' => [__('app/action.import.action_domain_not_found')],
                        ];
                        if (count($errors) >= self::MAX_ERRORS) {
                            break;
                        }
                        continue;
                    }

                    $actionDomainUuid = $actionDomain->uuid;
                }

                if (!empty($row['domaine_strategique'])) {
                    if (!$actionDomainUuid) {
                        $errors[] = [
                            'row' => $lineNumber,
                            'errors' => [__('app/action.import.action_domain_required')],
                        ];
                        if (count($errors) >= self::MAX_ERRORS) {
                            break;
                        }
                        continue;
                    }

                    $strategicDomain = StrategicDomain::where('name', $row['domaine_strategique'])
                        ->where('action_domain_uuid', $actionDomainUuid)
                        ->whereNotIn('status', ['closed', 'stopped'])
                        ->first();

                    if (!$strategicDomain) {
                        $errors[] = [
                            'row' => $lineNumber,
                            'errors' => [__('app/action.import.strategic_domain_not_found')],
                        ];
                        if (count($errors) >= self::MAX_ERRORS) {
                            break;
                        }
                        continue;
                    }

                    $strategicDomainUuid = $strategicDomain->uuid;
                }

                if (!empty($row['domaine_capacitaire'])) {
                    if (!$strategicDomainUuid) {
                        $errors[] = [
                            'row' => $lineNumber,
                            'errors' => [__('app/action.import.strategic_domain_required')],
                        ];
                        if (count($errors) >= self::MAX_ERRORS) {
                            break;
                        }
                        continue;
                    }

                    $capabilityDomain = CapabilityDomain::where('name', $row['domaine_capacitaire'])
                        ->where('strategic_domain_uuid', $strategicDomainUuid)
                        ->whereNotIn('status', ['closed', 'stopped'])
                        ->first();

                    if (!$capabilityDomain) {
                        $errors[] = [
                            'row' => $lineNumber,
                            'errors' => [__('app/action.import.capability_domain_not_found')],
                        ];
                        if (count($errors) >= self::MAX_ERRORS) {
                            break;
                        }
                        continue;
                    }

                    $capabilityDomainUuid = $capabilityDomain->uuid;
                }

                if (!empty($row['niveau_elementaire'])) {
                    if (!$capabilityDomainUuid) {
                        $errors[] = [
                            'row' => $lineNumber,
                            'errors' => [__('app/action.import.capability_domain_required')],
                        ];
                        if (count($errors) >= self::MAX_ERRORS) {
                            break;
                        }
                        continue;
                    }

                    $elementaryLevel = ElementaryLevel::where('name', $row['niveau_elementaire'])
                        ->where('capability_domain_uuid', $capabilityDomainUuid)
                        ->whereNotIn('status', ['closed', 'stopped'])
                        ->first();

                    if (!$elementaryLevel) {
                        $errors[] = [
                            'row' => $lineNumber,
                            'errors' => [__('app/action.import.elementary_level_not_found')],
                        ];
                        if (count($errors) >= self::MAX_ERRORS) {
                            break;
                        }
                        continue;
                    }

                    $elementaryLevelUuid = $elementaryLevel->uuid;
                }

                $regionUuid = null;
                $departmentUuid = null;
                $municipalityUuid = null;

                if (!empty($row['region'])) {
                    $region = Region::where('name', $row['region'])
                        ->where('status', true)
                        ->first();

                    if (!$region) {
                        $errors[] = [
                            'row' => $lineNumber,
                            'errors' => [__('app/action.import.region_not_found')],
                        ];
                        if (count($errors) >= self::MAX_ERRORS) {
                            break;
                        }
                        continue;
                    }

                    $regionUuid = $region->uuid;
                }

                if (!empty($row['departement'])) {
                    if (!$regionUuid) {
                        $errors[] = [
                            'row' => $lineNumber,
                            'errors' => [__('app/action.import.region_required')],
                        ];
                        if (count($errors) >= self::MAX_ERRORS) {
                            break;
                        }
                        continue;
                    }

                    $department = Department::where('name', $row['departement'])
                        ->where('region_uuid', $regionUuid)
                        ->where('status', true)
                        ->first();

                    if (!$department) {
                        $errors[] = [
                            'row' => $lineNumber,
                            'errors' => [__('app/action.import.department_not_found')],
                        ];
                        if (count($errors) >= self::MAX_ERRORS) {
                            break;
                        }
                        continue;
                    }

                    $departmentUuid = $department->uuid;
                }

                if (!empty($row['commune'])) {
                    if (!$departmentUuid) {
                        $errors[] = [
                            'row' => $lineNumber,
                            'errors' => [__('app/action.import.department_required')],
                        ];
                        if (count($errors) >= self::MAX_ERRORS) {
                            break;
                        }
                        continue;
                    }

                    $municipality = Municipality::where('name', $row['commune'])
                        ->where('department_uuid', $departmentUuid)
                        ->where('status', true)
                        ->first();

                    if (!$municipality) {
                        $errors[] = [
                            'row' => $lineNumber,
                            'errors' => [__('app/action.import.municipality_not_found')],
                        ];
                        if (count($errors) >= self::MAX_ERRORS) {
                            break;
                        }
                        continue;
                    }

                    $municipalityUuid = $municipality->uuid;
                }

                $existingQuery = Action::where('structure_uuid', $structure->uuid)
                    ->where('name', $row['nom_action']);

                if ($responsibleStructureUuid) {
                    $existingQuery->where('responsible_structure_uuid', $responsibleStructureUuid);
                } else {
                    $existingQuery->whereNull('responsible_structure_uuid');
                }

                if ($responsibleUuid) {
                    $existingQuery->where('responsible_uuid', $responsibleUuid);
                } else {
                    $existingQuery->whereNull('responsible_uuid');
                }

                $existing = $existingQuery->first();

                if ($existing) {
                    continue;
                }

                $action = Action::create([
                    'name' => $row['nom_action'],
                    'priority' => $priorityCode,
                    'risk_level' => $riskLevelCode,
                    'description' => $row['description'] ?? null,
                    'prerequisites' => $row['conditions_prealables'] ?? null,
                    'impacts' => $row['impacts_attendus'] ?? null,
                    'risks' => $row['risques_identifies'] ?? null,
                    'chart_type' => $chartTypeCode,
                    'generate_document_type' => $planTypeCode,
                    'structure_uuid' => $structure->uuid,
                    'action_plan_uuid' => $actionPlan->uuid,
                    'project_owner_uuid' => $projectOwner->uuid,
                    'delegated_project_owner_uuid' => $delegatedProjectOwner->uuid,
                    'currency' => $currencyCode,
                    'region_uuid' => $regionUuid,
                    'department_uuid' => $departmentUuid,
                    'municipality_uuid' => $municipalityUuid,
                    'action_domain_uuid' => $actionDomainUuid,
                    'strategic_domain_uuid' => $strategicDomainUuid,
                    'capability_domain_uuid' => $capabilityDomainUuid,
                    'elementary_level_uuid' => $elementaryLevelUuid,
                    'responsible_structure_uuid' => $responsibleStructureUuid,
                    'responsible_uuid' => $responsibleUuid,
                    'status' => 'draft',
                    'created_by' => Auth::user()?->uuid,
                    'updated_by' => Auth::user()?->uuid,
                ]);
                $action->refresh();

                $status = ActionStatus::create([
                    'action_uuid' => $action->uuid,
                    'action_id' => $action->id,
                    'status_code' => $action->status,
                    'status_date' => now(),
                    'created_by' => Auth::user()?->uuid,
                    'updated_by' => Auth::user()?->uuid,
                ]);

                $abbStructure = $structure->abbreviation;
                $action->update([
                    'reference' => ReferenceGenerator::generateActionReference($action->id, $abbStructure),
                    'status' => $status->status_code,
                    'status_changed_at' => $status->status_date,
                    'status_changed_by' => $status->created_by,
                ]);

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
            'nom_action' => ['bail', 'required', 'string', 'max:100'],
            'description' => ['bail', 'nullable', 'string', 'max:1000'],
            'conditions_prealables' => ['bail', 'nullable', 'string', 'max:1000'],
            'impacts_attendus' => ['bail', 'nullable', 'string', 'max:1000'],
            'risques_identifies' => ['bail', 'nullable', 'string', 'max:1000'],

            'structure_pilote' => ['bail', 'nullable', 'max:20'],
            'responsable' => ['nullable', 'email'],

            'plan_action' => ['bail', 'required', 'max:100'],
            'priorite' => ['bail', 'required'],
            'risque' => ['bail', 'required'],
            'type_plan' => ['bail', 'required'],
            'type_graphique' => ['bail', 'required'],

            'maitre_ouvrage' => ['bail', 'required', 'string', 'max:100'],
            'maitre_ouvrage_delegue' => ['bail', 'required', 'string', 'max:100'],

            'domaine_action' => ['bail', 'nullable', 'string', 'max:100'],
            'domaine_strategique' => ['bail', 'nullable', 'string', 'max:100'],
            'domaine_capacitaire' => ['bail', 'nullable', 'string', 'max:100'],
            'niveau_elementaire' => ['bail', 'nullable', 'string', 'max:100'],

            'region' => ['bail', 'nullable', 'string', 'max:50'],
            'departement' => ['bail', 'nullable', 'string', 'max:50'],
            'commune' => ['bail', 'nullable', 'string', 'max:50'],

        ], [], [
            'nom_action' => __('app/action.request.name'),
            'description' => __('app/action.request.description'),
            'conditions_prealables' => __('app/action.request.prerequisites'),
            'impacts_attendus' => __('app/action.request.impacts'),
            'risques_identifies' => __('app/action.request.risks'),
            'structure_pilote' => __('app/action.request.structure_pilot'),
            'responsable' => __('app/action.request.responsible'),
            'plan_action' => __('app/action.request.action_plan'),
            'priorite' => __('app/action.request.priority'),
            'risque' => __('app/action.request.risk_level'),
            'type_plan' => __('app/action.request.generate_document_type'),
            'type_graphique' => __('app/action.request.chart_type'),

            'maitre_ouvrage' => __('app/action.request.project_owner'),
            'maitre_ouvrage_delegue' => __('app/action.request.delegated_project_owner'),

            'domaine_action' => __('app/action.request.action_domain'),
            'domaine_strategique' => __('app/action.request.strategic_domain'),
            'domaine_capacitaire' => __('app/action.request.capability_domain'),
            'niveau_elementaire' => __('app/action.request.elementary_level'),

            'region' => __('app/action.request.region'),
            'departement' => __('app/action.request.department'),
            'commune' => __('app/action.request.municipality'),
        ]);
    }
}
