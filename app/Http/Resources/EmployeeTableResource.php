<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeTableResource extends JsonResource
{
    public array $permissions = ['write' => false, 'logs' => false, 'delete' => false];

    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'file_as'           => $this->file_as,
            'nickname'          => $this->nickname,
            'phone'             => $this->phone,
            'email'             => $this->email,
            'birthday'          => $this->birthday?->format('M d, Y') ?? '',
            'place_of_birth'    => $this->place_of_birth,
            'marital_status'    => $this->maritalStatus?->name ?? '',
            'gender'            => $this->gender?->name ?? '',
            'religion'          => $this->religion?->name ?? '',
            'blood_type'        => $this->bloodType?->name ?? '',
            'height'            => $this->height,
            'weight'            => $this->weight,
            'badge_id'          => $this->badge_id,
            'company'           => $this->company->legal_name ?? '',
            'employment_type'   => $this->employmentType->name ?? '',
            'department'        => $this->department->name ?? '',
            'job_position'      => $this->jobPosition->name ?? '',
            'manager'           => '',
            'employment_status' => $this->employmentStatus->name ?? '',
            'onboard_date'      => $this->onboard_date?->format('M d, Y') ?? '',
            'offboard_date'     => $this->offboard_date?->format('M d, Y') ?? '',
            'created_at'        => $this->created_at?->format('M d, Y h:i:s a') ?? '',
            'permissions'   => [
                'can_write'  => (bool) ($this->permissions['write'] ?? false),
                'can_logs'   => (bool) ($this->permissions['logs'] ?? false),
                'can_delete' => (bool) ($this->permissions['delete'] ?? false),
            ],
        ];
    }
}
