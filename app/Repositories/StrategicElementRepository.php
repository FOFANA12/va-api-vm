<?php

namespace App\Repositories;

use App\Http\Requests\StrategicElementRequest;
use App\Http\Resources\StrategicElementResource;
use App\Models\StrategicElement;
use App\Models\Structure;
use App\Services\StructureAccessService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use RuntimeException;

class StrategicElementRepository
{
    /**
     * Injected service for structure visibility.
     */
    protected StructureAccessService $structureAccess;

    public function __construct(StructureAccessService $structureAccess)
    {
        $this->structureAccess = $structureAccess;
    }

    /**
     * List structure strategic element with pagination, filters, sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['name', 'abbreviation', 'strategic_map', 'structure', 'parent'];
        $sortable = ['name', 'abbreviation', 'status', 'strategic_map', 'structure', 'order', 'parent'];

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');
        $type = strtoupper($request->input('type', 'AXIS'));

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = StrategicElement::select(
            'strategic_elements.id',
            'strategic_elements.uuid',
            'strategic_elements.order',
            'strategic_elements.type',
            'strategic_elements.abbreviation',
            'strategic_elements.name',
            'strategic_elements.description',
            'strategic_elements.status',
            'strategic_maps.name as strategic_map',
            'structures.name as structure',
            'stEl.name as parent',
        )
            ->join('strategic_maps', 'strategic_elements.strategic_map_uuid', '=', 'strategic_maps.uuid')
            ->join('structures', 'strategic_elements.structure_uuid', '=', 'structures.uuid')
            ->leftJoin('strategic_elements as stEl', 'strategic_elements.parent_element_uuid', '=', 'stEl.uuid')
            ->where('strategic_elements.type', $type);

        $allowed = $this->structureAccess->getAccessibleStructureUuids(Auth::user());
        if ($allowed !== null) {
            $query->whereIn('strategic_elements.structure_uuid', $allowed);
        }

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'strategic_map') {
                        $q->orWhere('strategic_maps.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } elseif ($column === 'structure') {
                        $q->orWhere('structures.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } elseif ($column === 'parent') {
                        $q->orWhere('stEl.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else {
                        $q->orWhere("strategic_elements.$column", 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                }
            });
        }

        if ($sortBy === 'strategic_map') {
            $query->orderBy('strategic_maps.name', $sortOrder);
        } elseif ($sortBy === 'structure') {
            $query->orderBy('structures.name', $sortOrder);
        } elseif ($sortBy === 'parent') {
            $query->orderBy('stEl.name', $sortOrder);
        } else {
            $query->orderBy("strategic_elements.$sortBy", $sortOrder);
        }

        return $perPage && (int) $perPage > 0
            ? $query->paginate((int) $perPage)
            : $query->get();
    }

    /**
     * Load requirements data
     */
    public function requirements()
    {
        $structures = Structure::where('status', true)
            ->with(['strategicMaps' => function ($query) {
                $query->where('status', true)
                    ->select('uuid', 'structure_uuid', 'name')
                    ->with([
                        'elements' => function ($subQuery) {
                            $subQuery->where('status', true)
                                ->select(
                                    'uuid',
                                    'strategic_map_uuid',
                                    'parent_element_uuid',
                                    'type',
                                    'order',
                                    'name',
                                    'abbreviation',
                                    'status'
                                )
                                ->orderBy('order', 'asc');
                        },
                    ]);
            }])
            ->orderBy('id', 'desc')
            ->select('uuid', 'name', 'abbreviation', 'type')
            ->get();

        return [
            'structures' => $structures,
        ];
    }

    /**
     * Create a new strategic element.
     */
    public function store(StrategicElementRequest $request)
    {
        $data = [
            'strategic_map_uuid' => $request->input('strategic_map'),
            'structure_uuid' => $request->input('structure'),
            'order' => $request->input('order'),
            'name' => $request->input('name'),
            'abbreviation' => $request->input('abbreviation'),
            'description' => $request->input('description'),
            'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            'type' => strtoupper($request->input('type', 'AXIS')),
            'created_by' => Auth::user()?->uuid,
            'updated_by' => Auth::user()?->uuid,
        ];

        if ($data['type'] === 'AXIS') {
            $data['parent_element_uuid'] = $request->input('parent_element');
            $data['parent_structure_uuid'] = $request->input('parent_structure');
            $data['parent_map_uuid'] = $request->input('parent_map');
        } else {
            $data['parent_element_uuid'] = null;
            $data['parent_structure_uuid'] = null;
            $data['parent_map_uuid'] = null;
        }

        $strategicElement = StrategicElement::create($data);

        $strategicElement->loadMissing([
            'strategicMap',
            'structure',
            'parent',
            'parentStructure',
            'parentMap',
        ]);

        return (new StrategicElementResource($strategicElement))->additional([
            'mode' => $request->input('mode', 'view')
        ]);
    }

    /**
     * Show a specific strategic element.
     */
    public function show(StrategicElement $strategicElement)
    {
        $strategicElement->loadMissing([
            'strategicMap',
            'structure',
            'parent',
            'parentStructure',
            'parentMap',
        ]);

        return ['strategic_element' => new StrategicElementResource($strategicElement)];
    }

    /**
     * Update a strategic element.
     */
    public function update(StrategicElementRequest $request, StrategicElement $strategicElement)
    {
        $data = [
            'strategic_map_uuid' => $request->input('strategic_map'),
            'structure_uuid' => $request->input('structure'),
            'order' => $request->input('order'),
            'name' => $request->input('name'),
            'abbreviation' => $request->input('abbreviation'),
            'description' => $request->input('description'),
            'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            'updated_by' => Auth::user()?->uuid,
        ];

        $strategicElement->fill($data)->save();

        $strategicElement->loadMissing([
            'strategicMap',
            'structure',
            'parent',
            'parentStructure',
            'parentMap'
        ]);

        return (new StrategicElementResource($strategicElement))->additional([
            'mode' => $request->input('mode', 'edit')
        ]);
    }

    /**
     * Delete multiple structure strategic element.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = StrategicElement::whereIn('id', $ids)->delete();
            if ($deleted === 0) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }


            DB::commit();
        } catch (RuntimeException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($e->getCode() === "23000") {
                throw new \Exception(__('app/common.repository.foreignKey'));
            }

            throw new \Exception(__('app/common.repository.error'));
        }
    }
}
