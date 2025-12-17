<?php

namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return match ($request->mode) {
            'list' => $this->forList(),
            'edit' => $this->forEdit(),
            default => $this->forView(),
        };
    }

    protected function forList(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'company_name' => $this->company_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'name' => $this->name,
            'tax_number' => $this->tax_number,
            'note' => $this->note,
            'status' => (bool) $this->status,
        ];
    }

    protected function forEdit(): array
    {

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'company_name' => $this->company_name,
            'tax_number' => $this->tax_number,
            'register_number' => $this->register_number,
            'establishment_year' => $this->establishment_year,
            'capital' => $this->capital,
            'annual_turnover' => $this->annual_turnover,
            'employees_count' => $this->employees_count,
            'note' => $this->note,

            'name' => $this->name,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'email' => $this->email,
            'address' => $this->address,
            'status' => (bool) $this->status,
        ];
    }

    protected function forView(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'company_name' => $this->company_name,
            'tax_number' => $this->tax_number,
            'register_number' => $this->register_number,
            'establishment_year' => $this->establishment_year,
            'capital' => $this->capital,
            'annual_turnover' => $this->annual_turnover,
            'employees_count' => $this->employees_count,
            'note' => $this->note,

            'name' => $this->name,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'email' => $this->email,
            'address' => $this->address,
            'status' => (bool) $this->status,
        ];
    }
}
