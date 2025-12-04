<?php

namespace App\Repositories;

use App\Http\Resources\CapabilityDomainStatusResource;
use App\Models\CapabilityDomainStatus as ModelsCapabilityDomainStatus;
use App\Models\CapabilityDomain;
use App\Support\CapabilityDomainStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CapabilityDomainStatusRepository
{
    /**
     *  List all statuses for a given capability domain.
     */
    public function index($capabilityDomainId)
    {
        $query = ModelsCapabilityDomainStatus::where('capability_domain_id', $capabilityDomainId)
            ->orderByDesc('created_at');

        return CapabilityDomainStatusResource::collection($query->get());
    }

    /**
     * Retrieve available capability domain statuses with localized labels.
     */
    public function requirements(CapabilityDomain $capabilityDomain)
    {
        $current = $capabilityDomain->status;
        $next = CapabilityDomainStatus::next($current);

        return [
            'statuses' => collect($next)->map(function ($code) {
                $status = CapabilityDomainStatus::get($code, app()->getLocale());
                return [
                    'code'  => $status->code,
                    'name'  => $status->label,
                    'color' => $status->color,
                ];
            })->values(),
        ];
    }

    /**
     * Create (record) a new capability domain status.
     */
    public function store(Request $request, CapabilityDomain $capabilityDomain)
    {
        DB::beginTransaction();
        try {
            $statusCode = $request->input('status');

            $status = ModelsCapabilityDomainStatus::create([
                'capability_domain_uuid' => $capabilityDomain->uuid,
                'capability_domain_id' => $capabilityDomain->id,
                'status_code' => $statusCode,
                'status_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $capabilityDomain->update([
                'status' => $status->status_code,
                'status_changed_at' => $status->status_date,
                'status_changed_by' => $status->created_by,
            ]);

            DB::commit();

            return new CapabilityDomainStatusResource($status);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * Delete multiple capability domain statuses.
     */
    public function destroy(Request $request, CapabilityDomain $capabilityDomain)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = $capabilityDomain->statuses()->whereIn('id', $ids)->delete();

            if ($deleted === 0) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            $lastStatus = $capabilityDomain->statuses()->latest('created_at')->first();

            if ($lastStatus) {
                $capabilityDomain->update([
                    'status' => $lastStatus->status_code,
                    'status_changed_at' => $lastStatus->status_date,
                    'status_changed_by' => $lastStatus->updated_by ?? $lastStatus->created_by,
                ]);
            } else {
                $capabilityDomain->update([
                    'status' => null,
                    'status_changed_at' => null,
                    'status_changed_by' => null,
                ]);
            }

            DB::commit();

            return new CapabilityDomainStatusResource($lastStatus);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
