<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScholarshiApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
 public function toArray($request)
    {
        return [
            'id' => $this->id,
            'academic_stage' => $this->academic_stage,
            'school_name' => $this->school_name,
            'field_of_study' => $this->field_of_study,
            'academic_year' => $this->academic_year,
            'average' => $this->average,
            'placement_test' => (bool) $this->placement_test,
            'language_level' => $this->language_level,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // علاقة الطالب (User)
            'student' => [
                'id' => $this->user->id ?? null,
                'name' => $this->user->first_name . ' ' . $this->user->last_name,
                'email' => $this->user->email ?? null,
                'phone' => $this->user->phone_number,
            ],

            // علاقة المنحة (Scholarship)
            'scholarship' => [
                'id' => $this->scholarship->id ?? null,
                'name' => $this->scholarship->name ?? null,
                'description' => $this->scholarship->description ?? null,
            ],
        ];
    }
}
