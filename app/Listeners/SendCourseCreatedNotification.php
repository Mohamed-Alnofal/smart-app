<?php

namespace App\Listeners;

use App\Events\CourseCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\GlobalAppNotification;
use App\Models\User;

class SendCourseCreatedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
     public function handle(CourseCreated $event)
    {
        $users = User::all(); // إشعار لكل المستخدمين

        foreach ($users as $user) {
            $user->notify(new GlobalAppNotification(
                ' كورس جديد',
                'تمت إضافة الكورس: ' . $event->course->course_name,
                ['course_id' => $event->course->id]
            ));
        }
    }
}
