<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class EmployeeTableResource extends JsonResource
{
    public array $permissions = ['write' => false, 'logs' => false, 'delete' => false];

    public function toArray(Request $request): array
    {
        $detail = $this->employmentDetail;

        $fallbackEmployeeImage = $this->defaultEmployeeImage ?? asset('assets/media/default/default-avatar.jpg');
        $employeImageUrl = $this->logo ? Storage::disk('public')->url($this->employee_image) : $fallbackEmployeeImage;

        return [
            'id'                => $this->id,
            'employee_image'    => $employeImageUrl ?? '',
            'file_as'           => $this->file_as,
            'company'           => $detail?->company?->legal_name ?? '',
            'job_position'      => $detail?->jobPosition?->name ?? '',
            'department'        => $detail?->department?->name ?? '',
            'employment_status' => $detail?->employmentStatus?->name ?? '',
            'onboard_date'      => $detail?->onboard_date?->format('M d, Y') ?? '',
            'offboard_date'     => $detail?->offboard_date?->format('M d, Y') ?? '',
            'created_at'        => $this->created_at?->format('M d, Y h:i:s a') ?? '',
            'permissions'   => [
                'can_write'  => (bool) ($this->permissions['write'] ?? false),
                'can_logs'   => (bool) ($this->permissions['logs'] ?? false),
                'can_delete' => (bool) ($this->permissions['delete'] ?? false),
            ],
        ];
    }
}
