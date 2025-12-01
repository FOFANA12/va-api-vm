<?php

namespace App\Http\Controllers\Export;

use App\Helpers\DateTimeFormatter;
use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Structure;
use App\Support\StrategicState;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpWord\TemplateProcessor;

class StructureController extends Controller
{
    public function exportActionPlanToExcel(Structure $structure)
    {
        $spreadsheet = IOFactory::load(public_path('storage/templates/strcuture-ap.xlsx'));
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setTitle(\Illuminate\Support\Str::limit($structure->abbreviation, 25));

        $collectStructures = function ($structure, &$result) use (&$collectStructures) {
            foreach ($structure->children as $child) {
                $result[] = $child;
                $collectStructures($child, $result);
            }
        };

        $allStructures = [];
        $collectStructures($structure, $allStructures);

        $styleArray = [
            'font' => ['bold' => false, 'size' => 11, 'name' => 'Calibri'],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrap'       => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
        ];

        $structureName  = $structure->name . ' (' . $structure->abbreviation . ')';
        $worksheet->setCellValue('A2', $structureName);

        $startRow = 5;
        $rowIndex  = 1;

        foreach ($allStructures as $child) {
            $actionPlan = $child->actionPlans()->where('status', true)->first();
            if (!$actionPlan) {
                continue;
            }

            foreach ($actionPlan->actions as $action) {
                $worksheet->setCellValue("A{$startRow}", $rowIndex);
                $worksheet->getStyle("A{$startRow}")->applyFromArray($styleArray);

                $currentStructureName = $child->name . ' (' . $child->abbreviation . ')';
                $worksheet->setCellValue("B{$startRow}", $currentStructureName);
                $worksheet->getStyle("B{$startRow}")->applyFromArray($styleArray);

                $worksheet->setCellValue("C{$startRow}", $action->reference);
                $worksheet->getStyle("C{$startRow}")->applyFromArray($styleArray);

                $worksheet->setCellValue("D{$startRow}", $action->name);
                $worksheet->getStyle("D{$startRow}")->applyFromArray($styleArray);

                $worksheet->setCellValue("E{$startRow}", $action->description);
                $worksheet->getStyle("E{$startRow}")->applyFromArray($styleArray);

                $worksheet->setCellValue("F{$startRow}", DateTimeFormatter::formatDate($action->start_date));
                $worksheet->getStyle("F{$startRow}")->applyFromArray($styleArray);

                $worksheet->setCellValue("G{$startRow}", DateTimeFormatter::formatDate($action->end_date));
                $worksheet->getStyle("G{$startRow}")->applyFromArray($styleArray);

                $worksheet->setCellValue("H{$startRow}", $action->program?->name);
                $worksheet->getStyle("H{$startRow}")->applyFromArray($styleArray);

                $worksheet->setCellValue("I{$startRow}", $action->project?->name);
                $worksheet->getStyle("I{$startRow}")->applyFromArray($styleArray);

                $worksheet->setCellValue("J{$startRow}", $action->activity?->name);
                $worksheet->getStyle("J{$startRow}")->applyFromArray($styleArray);

                $objectives = $action->objectives->pluck('name')->implode(', ');
                $worksheet->setCellValue("K{$startRow}", $objectives);
                $worksheet->getStyle("K{$startRow}")->applyFromArray($styleArray);
                $worksheet->getStyle("K{$startRow}")->getAlignment()->setWrapText(true);

                $stakeholders = $action->stakeholders->pluck('name')->implode(",\n");
                $worksheet->setCellValue("L{$startRow}", $stakeholders);
                $worksheet->getStyle("L{$startRow}")->applyFromArray($styleArray);

                $currencyCode = $action->currency?->code ?? '';
                $budgetValue  = $action->total_budget ?? 0;
                $worksheet->setCellValue("M{$startRow}", $budgetValue);
                $worksheet->getStyle("M{$startRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0 [$' . $currencyCode . ']');

                $fundingSources = $action->fundingSources->pluck('name')->implode(",\n");
                $worksheet->setCellValue("N{$startRow}", $fundingSources);
                $worksheet->getStyle("N{$startRow}")->applyFromArray($styleArray);

                $localisation = '';
                if ($action->region || $action->department || $action->municipality) {
                    $localisation .= 'Région : ' . ($action->region?->name ?? '');
                    $localisation .= "\nDépartement : " . ($action->department?->name ?? '');
                    $localisation .= "\nCommune : " . ($action->municipality?->name ?? '');
                }
                $worksheet->setCellValue("O{$startRow}", $localisation);
                $worksheet->getStyle("O{$startRow}")->applyFromArray($styleArray);
                $worksheet->getStyle("O{$startRow}")->getAlignment()->setWrapText(true);

                $beneficiaries = $action->beneficiaries->pluck('name')->implode(",\n");
                $worksheet->setCellValue("P{$startRow}", $beneficiaries);
                $worksheet->getStyle("P{$startRow}")->applyFromArray($styleArray);

                $worksheet->setCellValue("Q{$startRow}", $action->actual_progress_percent);
                $worksheet->getStyle("Q{$startRow}")->applyFromArray($styleArray);

                $startRow++;
                $rowIndex++;
            }
        }

        foreach (range('A', 'Q') as $col) {
            $worksheet->getStyle($col)->getAlignment()->setWrapText(true);
        }

        $spreadsheet->setActiveSheetIndex(0);
        $exportDir  = public_path("storage/templates/export");
        $fileSystem = new Filesystem();

        if (!$fileSystem->exists($exportDir)) {
            $fileSystem->makeDirectory($exportDir, 0755, true);
        } else {
            $fileSystem->cleanDirectory($exportDir);
        }

        $filePath = "{$exportDir}/PA-{$structure->abbreviation}.xlsx";
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filePath);

        return response()->download($filePath);
    }

    public function exportBilanToExcel(Structure $structure)
    {
        $spreadsheet = IOFactory::load(public_path('storage/templates/bilan.xlsx'));
        $worksheet = $spreadsheet->getSheet(0);

        // Main title
        $worksheet->setTitle(\Illuminate\Support\Str::limit("BILAN - $structure->abbreviation", 25));
        $structureLabel = $structure->name . ' (' . $structure->abbreviation . ')';
        $worksheet->setCellValue('A3', "BILAN CONSOLIDÉ : {$structureLabel}");
        $worksheet->getStyle("A3")->getFont()->setBold(true)->setSize(16);
        $worksheet->mergeCells("A3:O3");
        $worksheet->getStyle("A3")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $worksheet->getRowDimension(3)->setRowHeight(30);

        // Collect child structures recursively
        $collectStructures = function ($structure, &$result) use (&$collectStructures) {
            foreach ($structure->children as $child) {
                $result[] = $child;
                $collectStructures($child, $result);
            }
        };
        $allStructures = [];
        $collectStructures($structure, $allStructures);

        // Styles
        $styleValues = [
            'font' => ['bold' => false, 'size' => 12, 'name' => 'Calibri'],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $styleHeaders = [
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FF8000']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '333333'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $startRow = 5;

        foreach ($allStructures as $child) {
            // Structure title
            $childLabel = mb_strtoupper($child->name . ' (' . $child->abbreviation . ')');
            $worksheet->setCellValue("A{$startRow}", "BILAN : {$childLabel}");
            $worksheet->mergeCells("A{$startRow}:O{$startRow}");
            $worksheet->getStyle("A{$startRow}")->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('FF8000');
            $worksheet->getRowDimension($startRow)->setRowHeight(28);
            $worksheet->getStyle("A{$startRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $startRow++;

            // --- First summary table ---
            $headers = [
                "A" => "Nbr Prog/Progr",
                "B" => "Nbr d'actions",
                "C" => "Nbr d'objectifs",
                "D" => "Taux de réalisation",
                "E" => "Indice de réalisation",
                "F" => "Taux de décaissement",
                "G" => "Budget prévisionnel",
                "H" => "Budget acquis",
                "I" => "Budget consommé",
                "J" => "Budget disponible",
                "K" => "Budget à mobiliser",
            ];
            foreach ($headers as $col => $label) {
                $worksheet->setCellValue("{$col}{$startRow}", $label);
                $worksheet->getStyle("{$col}{$startRow}")->applyFromArray($styleHeaders);
            }
            $worksheet->getRowDimension($startRow)->setRowHeight(25);
            $startRow++;

            // Data row
            $actionPlan = $child->actionPlans()->where('status', true)->first();
            if ($actionPlan) {
                $actionIds = $actionPlan->actions->pluck('id');

                $totalActions = $actionIds->count();
                $totalPrograms = $actionPlan->actions->pluck('program_uuid')->filter()->unique()->count();

                $metricsQuery = DB::table('action_metrics')->whereIn('action_id', $actionIds);
                $totalObjectives = $metricsQuery->avg('aligned_objectives_count');
                $realizationRate = $metricsQuery->avg('realization_rate');
                $realizationIndex = $metricsQuery->avg('realization_index');

                $budgetPlanned = $actionPlan->actions->sum('total_budget');
                $budgetReceived = $actionPlan->actions->sum('total_receipt_fund');
                $budgetSpent = $actionPlan->actions->sum('total_disbursement_fund');
                $budgetToMobilize = $budgetPlanned - $budgetReceived;
                $budgetAvailable = $budgetReceived - $budgetSpent;
                $disbursementRate = $budgetPlanned > 0 ? ($budgetSpent / $budgetPlanned) * 100 : 0;

                $worksheet->setCellValue("A{$startRow}", $totalPrograms);
                $worksheet->setCellValue("B{$startRow}", $totalActions);
                $worksheet->setCellValue("C{$startRow}", round($totalObjectives, 2));
                $worksheet->setCellValue("D{$startRow}", round($realizationRate, 2));
                $worksheet->setCellValue("E{$startRow}", round($realizationIndex, 2));
                $worksheet->setCellValue("F{$startRow}", round($disbursementRate, 2));
                $worksheet->setCellValue("G{$startRow}", $budgetPlanned);
                $worksheet->setCellValue("H{$startRow}", $budgetReceived);
                $worksheet->setCellValue("I{$startRow}", $budgetSpent);
                $worksheet->setCellValue("J{$startRow}", $budgetAvailable);
                $worksheet->setCellValue("K{$startRow}", $budgetToMobilize);
            }
            $worksheet->getStyle("A{$startRow}:K{$startRow}")->applyFromArray($styleValues);
            $worksheet->getRowDimension($startRow)->setRowHeight(22);

            $startRow += 3;

            // --- Second detailed table ---
            $worksheet->setCellValue("A{$startRow}", "Pilier TaahoudatY");
            $worksheet->setCellValue("B{$startRow}", "Axe Département");
            $worksheet->setCellValue("C{$startRow}", "Objectifs");
            $worksheet->setCellValue("D{$startRow}", "Actions programmées");
            $worksheet->setCellValue("E{$startRow}", "Référence");
            $worksheet->setCellValue("F{$startRow}", "Programme");
            $worksheet->setCellValue("G{$startRow}", "Parties prenantes");

            // Merge 2 cols for Chronogramme
            $worksheet->mergeCells("H{$startRow}:I{$startRow}");
            $worksheet->setCellValue("H{$startRow}", "Chronogramme de mise en oeuvre");
            $worksheet->setCellValue("H" . ($startRow + 1), "Début");
            $worksheet->setCellValue("I" . ($startRow + 1), "Fin");

            $worksheet->setCellValue("J{$startRow}", "Zone d'intervention");
            $worksheet->setCellValue("K{$startRow}", "Source de financement");

            // Merge 2 cols for Budget alloué
            $worksheet->mergeCells("L{$startRow}:M{$startRow}");
            $worksheet->setCellValue("L{$startRow}", "Budget alloué en MRU");
            $worksheet->setCellValue("L" . ($startRow + 1), "Disponible");
            $worksheet->setCellValue("M" . ($startRow + 1), "Prévisionnel");

            $worksheet->setCellValue("N{$startRow}", "% Avancement");
            $worksheet->setCellValue("O{$startRow}", "Description");

            $colsToMerge = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'J', 'K', 'N', 'O'];
            foreach ($colsToMerge as $col) {
                $worksheet->mergeCells("{$col}{$startRow}:{$col}" . ($startRow + 1));
            }

            // Apply styles to header row
            $worksheet->getStyle("A{$startRow}:O{$startRow}")->applyFromArray($styleHeaders);
            $worksheet->getStyle("A" . ($startRow + 1) . ":O" . ($startRow + 1))->applyFromArray($styleHeaders);

            $worksheet->getRowDimension($startRow)->setRowHeight(25);
            $worksheet->getRowDimension($startRow + 1)->setRowHeight(25);

            $startRow += 2;
            $strategicMap = $child->strategicMaps()->where('status', true)->first();
            if ($strategicMap) {
                foreach ($strategicMap->objectives as $objective) {
                    $alignedActionUuids = DB::table('action_objective_alignments')
                        ->where('objective_uuid', $objective->uuid)
                        ->pluck('action_uuid');

                    $actions = Action::whereIn('uuid', $alignedActionUuids)->get();

                    foreach ($actions as $action) {
                        $currencyCode = $action->currency?->code ?? '';

                        $worksheet->setCellValue("A{$startRow}", '');
                        $worksheet->setCellValue("B{$startRow}", $objective->strategicElement?->name ?? '');
                        $worksheet->setCellValue("C{$startRow}", $objective->name);
                        $worksheet->setCellValue("D{$startRow}", $action->name);
                        $worksheet->setCellValue("E{$startRow}", $action->reference);
                        $worksheet->setCellValue("F{$startRow}", $action->program?->name ?? '');

                        $stakeholders = $action->stakeholders->pluck('name')->implode(",\n");
                        $worksheet->setCellValue("G{$startRow}", $stakeholders);

                        $worksheet->setCellValue("H{$startRow}", DateTimeFormatter::formatDate($action->start_date));
                        $worksheet->setCellValue("I{$startRow}", DateTimeFormatter::formatDate($action->end_date));

                        $localisation = '';
                        if ($action->region || $action->department || $action->municipality) {
                            $localisation .= 'Région : ' . ($action->region?->name ?? '');
                            $localisation .= "\nDépartement : " . ($action->department?->name ?? '');
                            $localisation .= "\nCommune : " . ($action->municipality?->name ?? '');
                        }

                        $worksheet->setCellValue("J{$startRow}", $localisation);

                        $fundingSources = $action->fundingSources->pluck('name')->implode(",\n");
                        $worksheet->setCellValue("K{$startRow}", $fundingSources);

                        $worksheet->setCellValue("L{$startRow}", $action->total_receipt_fund - $action->total_disbursement_fund);
                        $worksheet->getStyle("L{$startRow}")
                            ->getNumberFormat()
                            ->setFormatCode('#,##0 [$' . $currencyCode . ']');

                        $worksheet->setCellValue("M{$startRow}", $action->total_budget);
                        $worksheet->getStyle("M{$startRow}")
                            ->getNumberFormat()
                            ->setFormatCode('#,##0 [$' . $currencyCode . ']');

                        $worksheet->setCellValue("N{$startRow}", $action->actual_progress_percent);
                        $worksheet->setCellValue("O{$startRow}", $action->description ?? '');

                        $worksheet->getStyle("A{$startRow}:O{$startRow}")->applyFromArray($styleValues);

                        $textCols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'J', 'K', 'O'];
                        $numericCols = ['H', 'I', 'L', 'M', 'N'];

                        foreach ($textCols as $col) {
                            $worksheet->getStyle("{$col}{$startRow}")->getAlignment()
                                ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                                ->setVertical(Alignment::VERTICAL_TOP)
                                ->setWrapText(true);
                            $worksheet->getColumnDimension($col)->setWidth(25);
                        }

                        foreach ($numericCols as $col) {
                            $worksheet->getStyle("{$col}{$startRow}")->getAlignment()
                                ->setHorizontal(Alignment::HORIZONTAL_RIGHT)
                                ->setVertical(Alignment::VERTICAL_CENTER);
                            $worksheet->getColumnDimension($col)->setWidth(18);
                        }

                        $startRow++;
                    }
                }
            } else {
                // Always create an empty row if no actions
                $worksheet->getStyle("A{$startRow}:O{$startRow}")->applyFromArray($styleValues);
                $worksheet->getRowDimension($startRow)->setRowHeight(22);
                $startRow++;
            }

            $startRow += 3;
        }

        $worksheet->setSelectedCell('A1');

        // Export
        $spreadsheet->setActiveSheetIndex(0);
        $exportDir = public_path("storage/templates/export");
        $fileSystem = new Filesystem();

        if (!$fileSystem->exists($exportDir)) {
            $fileSystem->makeDirectory($exportDir, 0755, true);
        } else {
            $fileSystem->cleanDirectory($exportDir);
        }

        $filePath = "{$exportDir}/Bilan-{$structure->abbreviation}.xlsx";
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filePath);

        return response()->download($filePath);
    }

    public function exportProcurementPlanToWord(Structure $structure, $generateDocumentType)
    {
        $country = 'REPUBLIQUE ISLAMIQUE DE MAURITANIE';

        // Collecter toutes les sous-structures avec plans actifs
        $structures = [];
        $collect = function ($s) use (&$structures, &$collect) {
            if ($s->actionPlans()->where('status', true)->exists()) {
                $structures[] = $s;
            }
            foreach ($s->children as $child) {
                $collect($child);
            }
        };
        $collect($structure);

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection([
            'orientation' => 'landscape',
            'marginLeft' => 400,
            'marginRight' => 400,
            'marginTop' => 400,
            'marginBottom' => 400,
        ]);
        $header = ['size' => 14, 'bold' => true, 'name' => 'Times New Roman'];

        $section->addText(htmlspecialchars($country), $header, ['align' => 'center']);
        $section->addLine(['weight' => 1, 'width' => 800, 'height' => 0, 'marginLeft' => 0]);
        $section->addTextBreak(1);

        $docTitle = ($generateDocumentType == 'paa')
            ? 'PLAN ANNUEL DES ACHATS CONSOLIDÉ'
            : 'PLAN DE PASSATION DES MARCHÉS CONSOLIDÉ';
        $section->addText($docTitle, ['size' => 13, 'bold' => true, 'name' => 'Times New Roman'], ['align' => 'center']);
        $section->addTextBreak(1);

        $structureLabel = $structure->name . ' (' . $structure->abbreviation . ')';
        $section->addText(htmlspecialchars($structureLabel), $header, ['align' => 'center']);
        $section->addTextBreak(1);

        // Parcourir toutes les sous-structures
        foreach ($structures as $s) {
            $sLabel = $s->name . ' (' . $s->abbreviation . ')';
            $section->addText($sLabel, ['size' => 12, 'bold' => true, 'name' => 'Times New Roman'], ['align' => 'center']);
            $section->addTextBreak(1);

            $styleTable = ['borderSize' => 6, 'cellMargin' => 80];
            $styleCell = ['valign' => 'center'];
            $fontStyle = ['bold' => true, 'size' => 10, 'name' => 'Times New Roman'];

            $phpWord->addTableStyle('PlanTable', $styleTable);
            $table = $section->addTable('PlanTable');

            if ($generateDocumentType === 'paa') {
                $cols = [
                    'Ref.',
                    'Réalisations envisagées',
                    'Imputation budgétaire',
                    'Type de contrat',
                    'Mode de sélection',
                    'Coût estimatif',
                    'Date prévisionnelle de lancement',
                    'Date prévisionnelle d\'attribution',
                ];
            } else {
                $cols = [
                    'Ref.',
                    'Réalisations envisagées',
                    'Source de financement',
                    'Coût estimatif',
                    'Type de marché',
                    'Mode de passation',
                    'Date prévue lancement',
                    'Date prévue attribution',
                    'Date prévue démarrage',
                    'Date prévue achèvement',
                ];
            }

            $table->addRow();
            foreach ($cols as $col) {
                $table->addCell(2000, $styleCell)->addText($col, $fontStyle, ['align' => 'center']);
            }

            $planAction = $s->actionPlans()->where('status', true)->first();
            if ($planAction) {
                $actions = $planAction->actions->where('generate_document_type', $generateDocumentType);

                foreach ($actions as $action) {
                    $table->addRow();
                    $attributionDate = Carbon::parse($action->start_date)->addDays($action->procurementMode->duration);

                    if ($generateDocumentType === 'paa') {
                        $table->addCell(1600)->addText($action->reference, null, ['align' => 'left']);
                        $table->addCell(2400)->addText($action->name, null, ['align' => 'left']);
                        $table->addCell(2000)->addText('', null, ['align' => 'left']);
                        $table->addCell(1600)->addText($action->contractType?->name ?? '', null, ['align' => 'left']);
                        $table->addCell(1600)->addText($action->procurementMode?->name ?? '', null, ['align' => 'left']);
                        $table->addCell(2000)->addText($action->total_budget, null, ['align' => 'right']);
                        $table->addCell(1600)->addText(DateTimeFormatter::formatDate($action->start_date), null, ['align' => 'right']);
                        $table->addCell(1600)->addText(DateTimeFormatter::formatDate($attributionDate), null, ['align' => 'right']);
                    } else {
                        $startServiceDate = Carbon::parse($attributionDate)->addDays(7);

                        $table->addCell(1600)->addText($action->reference, null, ['align' => 'left']);
                        $table->addCell(2400)->addText($action->name, null, ['align' => 'left']);
                        $table->addCell(2000)->addText($action->fundingSources->pluck('name')->implode(', '), null, ['align' => 'left']);
                        $table->addCell(2000)->addText($action->total_budget, null, ['align' => 'right']);
                        $table->addCell(1600)->addText($action->contractType?->name ?? '', null, ['align' => 'left']);
                        $table->addCell(1600)->addText($action->procurementMode?->name ?? '', null, ['align' => 'left']);
                        $table->addCell(1600)->addText(DateTimeFormatter::formatDate($action->start_date), null, ['align' => 'right']);
                        $table->addCell(1600)->addText(DateTimeFormatter::formatDate($attributionDate), null, ['align' => 'right']);
                        $table->addCell(1600)->addText(DateTimeFormatter::formatDate($startServiceDate), null, ['align' => 'right']);
                        $table->addCell(1600)->addText(DateTimeFormatter::formatDate($action->end_date), null, ['align' => 'right']);
                    }
                }
            } else {
                $table->addRow();
                $table->addCell(2000 * count($cols), ['gridSpan' => count($cols), 'valign' => 'center'])
                    ->addText('Aucune donnée', null, ['align' => 'center']);
            }

            $section->addTextBreak(2);
        }

        $phpWord->getCompatibility()->setOoxmlVersion(15);
        $type = mb_strtoupper($generateDocumentType);
        $filePath = public_path("storage/templates/export/{$type}-{$structure->abbreviation}.docx");
        $phpWord->save($filePath);

        return response()->download($filePath);
    }


    public function exportObjectiveToWord(Structure $structure)
    {
        $template = new TemplateProcessor(public_path('storage/templates/objective.docx'));

        $structures = [];
        $collect = function ($s) use (&$structures, &$collect) {
            if ($s->strategicMaps()->where('status', true)->exists()) {
                $structures[] = $s;
            }
            foreach ($s->children as $child) {
                $collect($child);
            }
        };
        $collect($structure);
        $template->setValue('structure', $structure->name . ' (' . $structure->abbreviation . ')');

        $rows = [];

        foreach ($structures as $s) {
            $strategicMap = $s->strategicMaps()->where('status', true)->first();
            if (!$strategicMap) {
                continue;
            }

            foreach ($strategicMap->objectives as $objective) {
                $startDate = $objective->start_date;
                $endDate = $objective->end_date;
                $leadStructure = $objective->leadStructure->abbreviation;

                $actions = $objective->actions->count() > 0
                    ? $objective->actions->pluck('reference')->implode(', ')
                    : '-';

                $indicators = $objective->indicators->count() > 0
                    ? $objective->indicators->pluck('reference')->implode(', ')
                    : '-';

                $rows[] = [
                    'reference' => $objective->reference,
                    'name' => $objective->name,
                    'startDate' => DateTimeFormatter::formatDate($startDate),
                    'endDate' => DateTimeFormatter::formatDate($endDate),
                    'state' => StrategicState::name($objective->state, app()->getLocale()),
                    'leadStructure' => $leadStructure,
                    'actions' => $actions,
                    'indicators' => $indicators,
                ];
            }
        }

        $template->cloneRowAndSetValues('reference', $rows);

        $exportDir = public_path("storage/templates/export");
        $fileSystem = new Filesystem();

        if (!$fileSystem->exists($exportDir)) {
            $fileSystem->makeDirectory($exportDir, 0755, true);
        } else {
            $fileSystem->cleanDirectory($exportDir);
        }

        $filePath = public_path("storage/templates/export/Objectifs-{$structure->abbreviation}.docx");
        $template->saveAs($filePath);

        return response()->download($filePath);
    }

    public function exportObjectiveDecisionToWord(Structure $structure)
    {
        $template = new TemplateProcessor(public_path('storage/templates/objective-decision.docx'));

        $structures = [];
        $collect = function ($s) use (&$structures, &$collect) {
            if ($s->strategicMaps()->where('status', true)->exists()) {
                $structures[] = $s;
            }
            foreach ($s->children as $child) {
                $collect($child);
            }
        };
        $collect($structure);
        $template->setValue('structure', $structure->name . ' (' . $structure->abbreviation . ')');

        $rows = [];

        foreach ($structures as $s) {
            $strategicMap = $s->strategicMaps()->where('status', true)->first();
            if (!$strategicMap) {
                continue;
            }

            foreach ($strategicMap->objectives as $objective) {
                foreach ($objective->decisions as $decision) {
                    $author = $decision->author['name'] ?? null;
                    $rows[] = [
                        'reference' => $decision->reference,
                        'title' => $decision->title,
                        'date' => DateTimeFormatter::formatDate($decision->decision_date),
                        'actor' => $author,
                        'leadStructure' => $objective->leadStructure?->abbreviation,
                        'objective' => $objective->name,
                    ];
                }
            }
        }

        $template->cloneRowAndSetValues('reference', $rows);

        $exportDir = public_path("storage/templates/export");
        $fileSystem = new Filesystem();

        if (!$fileSystem->exists($exportDir)) {
            $fileSystem->makeDirectory($exportDir, 0755, true);
        } else {
            $fileSystem->cleanDirectory($exportDir);
        }

        $filePath = public_path("storage/templates/export/Decisions-{$structure->abbreviation}.docx");
        $template->saveAs($filePath);

        return response()->download($filePath);
    }
}
