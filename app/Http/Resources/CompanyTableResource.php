<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CompanyTableResource extends JsonResource
{
    public array $permissions = ['write' => false, 'logs' => false, 'delete' => false];
    public ?string $defaultLogo = null;

    public function toArray(Request $request): array
    {
        $fallbackLogo = $this->defaultLogo ?? asset('assets/media/default/default-image-placeholder.jpeg');
        $logoUrl = $this->logo ? Storage::disk('public')->url($this->logo) : $fallbackLogo;

        $addressParts = array_filter([
            $this->street_1,
            $this->street_2,
            $this->barangay,
            $this->city?->name,
            $this->state?->name,
            $this->country?->name,
        ]);

        $fullAddress = implode(', ', $addressParts);

        return [
            'id'                        => $this->id,
            'legal_name'                => $this->legal_name,
            'trade_name'                => $this->trade_name,
            'tin'                       => $this->tin,
            'branch_code'               => $this->branch_code,
            'rdo_code'                  => $this->rdo_code,
            'entity_type'               => $this->entity_type,
            'sec_dti_registration_no'   => $this->sec_dti_registration_no,
            'date_registered'           => $this->date_registered?->format('M d, Y') ?? '',
            'psic_code'                 => $this->psic_code,
            'line_of_business'          => $this->line_of_business,
            'vat_status'                => $this->vat_status,
            'address'                   => $fullAddress,
            'currency'                  => $this->currency?->name ?? '',
            'fiscal_year_start_month'   => $this->fiscal_year_start_month,
            'phone'                     => $this->phone,
            'email'                     => $this->email,
            'website'                   => $this->website,
            'contact_person'            => $this->contact_person,
            'logo'                      => $logoUrl,
            'created_at'                => $this->created_at?->format('M d, Y h:i:s a') ?? '',
            'permissions'               => [
                'can_write'   => (bool) ($this->permissions['write'] ?? false),
                'can_logs'    => (bool) ($this->permissions['logs'] ?? false),
                'can_delete'  => (bool) ($this->permissions['delete'] ?? false),
            ],
        ];
    }
}