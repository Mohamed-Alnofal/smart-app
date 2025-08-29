<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
 public function toArray(Request $request): array
    {
        return [
            // 'id' => $this->id,
            'status' => $this->status,
            'academic_stage' => $this->academic_stage,
            'language_level' => $this->language_level,
            'time' => $this->time,
            'days' => $this->days,
            'learning_method' => $this->learning_method,
            'student' => [
                // 'id' => $this->user->id,
                'name' => $this->user->first_name . ' ' . $this->user->last_name,
                'email' => $this->user->email,
                'phone' => $this->user->phone_number,
            ],
            'level' => [
                // 'id' => $this->level->id,
                'name' => $this->level->name,
                // 'teacher' => $this->level->teacher,
                // 'start_time' => $this->level->start_time,
                // 'start_date' => $this->level->start_date,
                // 'days' => $this->level->days,
            ],
            'course' => [
                // 'id' => $this->level->course->id,
                'name' => $this->level->course->course_name,
                // 'image' => $this->level->course->image,
            ],
        ];
    }
}
