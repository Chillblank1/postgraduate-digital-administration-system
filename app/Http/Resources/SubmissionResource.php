<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Submission */
class SubmissionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'supervisor_id' => $this->supervisor_id,
            'co_supervisor_id' => $this->co_supervisor_id,
            'type' => $this->type?->value,
            'title' => $this->title,
            'academic_level' => $this->academic_level,
            'status' => $this->status?->value,
            'supervisor_feedback' => $this->supervisor_feedback,
            'supervisor_decision' => $this->supervisor_decision,
            'supervisor_signed_at' => $this->supervisor_signed_at?->toISOString(),
            'submitted_at' => $this->submitted_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'student' => UserResource::make($this->whenLoaded('student')),
            'supervisor' => UserResource::make($this->whenLoaded('supervisor')),
        ];
    }
}
