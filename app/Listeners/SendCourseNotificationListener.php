<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\CourseNotification;

class SendCourseNotificationListener
{

        use InteractsWithQueue;

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
    public function handle($event)
    {
        $title = '';
        $message = '';
        $data = [];

        switch (get_class($event)) {
            case \App\Events\CourseCreated::class:
                $title = '📚 كورس جديد';
                $message = 'تمت إضافة الكورس: ' . $event->course->course_name;
                $data = ['course_id' => $event->course->id];
                break;

            case \App\Events\CourseUpdated::class:
                $title = '✏️ تعديل كورس';
                $message = 'تم تعديل الكورس: ' . $event->course->course_name;
                $data = ['course_id' => $event->course->id];
                break;

            case \App\Events\CourseDeleted::class:
                $title = '🗑️ حذف كورس';
                $message = 'تم حذف الكورس: ' . $event->course->course_name;
                $data = ['course_id' => $event->course->id];
                break;

            case \App\Events\LevelCreated::class:
                $title = '➕ مستوى جديد';
                $message = 'تمت إضافة مستوى جديد للكورس: ' . $event->level->course->course_name;
                $data = ['level_id' => $event->level->id, 'course_id' => $event->level->course->id];
                break;

            case \App\Events\LevelUpdated::class:
                $title = '✏️ تعديل مستوى';
                $message = 'تم تعديل المستوى: ' . $event->level->level_name;
                $data = ['level_id' => $event->level->id, 'course_id' => $event->level->course->id];
                break;

            case \App\Events\LevelDeleted::class:
                $title = '🗑️ حذف مستوى';
                $message = 'تم حذف المستوى: ' . $event->level->level_name;
                $data = ['level_id' => $event->level->id, 'course_id' => $event->level->course->id];
                break;

            case \App\Events\EnrollmentCreated::class:
                $title = '✅ تسجيل طالب';
                $message = 'تم تسجيل الطالب: ' . $event->user->name 
                        . ' في المستوى: ' . $event->level->name;

                $data = [
                    'level_id' => $event->level->id,
                    'user_id'  => $event->user->id,
                ];
                break;

            case \App\Events\CourseClosed::class:
                $title = '🔒 إغلاق كورس';
                $message = 'تم إغلاق تسجيل الكورس: ' . $event->course->course_name;
                $data = ['course_id' => $event->course->id];
                break;

            case \App\Events\CourseOpened::class:
                $title = '🔓 فتح كورس';
                $message = 'تم فتح تسجيل الكورس: ' . $event->course->course_name;
                $data = ['course_id' => $event->course->id];
                break;
        }

        // إشعار جميع المستخدمين أو الطلاب المشتركين
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            $user->notify(new CourseNotification($title, $message, $data));
        }
    }
}
