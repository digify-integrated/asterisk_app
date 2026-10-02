<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class CompanyDetailsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'legal_name'                => $this->legal_name,
            'trade_name'                => $this->trade_name,
            'tin'                       => $this->tin,
            'branch_code'               => $this->branch_code,
            'rdo_code'                  => $this->rdo_code,
            'entity_type'               => $this->entity_type,
            'sec_dti_registration_no'   => $this->sec_dti_registration_no,
            'date_registered'           =>$this->date_registered ? Carbon::parse($this->date_registered)->format('M d, Y') : '',
            'psic_code'                 => $this->psic_code,
            'line_of_business'          => $this->line_of_business,
            'vat_status'                => $this->vat_status,
            'street_1'                  => $this->street_1,
            'street_2'                  => $this->street_2,
            'barangay'                  => $this->barangay,
            'city_id'                   => $this->city_id,
            'state_id'                  => $this->state_id,
            'country_id'                => $this->country_id,
            'currency_id'               => $this->currency_id,
            'fiscal_year_start_month'   => $this->fiscal_year_start_month,
            'phone'                     => $this->phone,
            'email'                     => $this->email,
            'website'                   => $this->website,
            'contact_person'            => $this->contact_person,
        ];
    }
}
