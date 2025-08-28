<?php

namespace App\Listeners;

use App\Events\ScholarshipCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Notification;
use App\Notifications\ScholarshipNotification;

class SendScholarshipNotificationListener implements ShouldQueue
{
    public function handle($event)
    {
        switch (get_class($event)) {
            case \App\Events\ScholarshipCreated::class:
                $title = 'منحة جديدة';
                $message = 'تم إضافة منحة جديدة: ' . $event->scholarship->name;
                $users = \App\Models\User::all(); // أو الطلاب فقط
                break;

            case \App\Events\ScholarshipUpdated::class:
                $title = 'تحديث على منحة';
                $message = 'تم تعديل بيانات المنحة: ' . $event->scholarship->name;
                $users = $event->scholarship->students; // الطلاب المسجلين فقط
                break;

            case \App\Events\ScholarshipDeleted::class:
                $title = 'حذف منحة';
                $message = 'تم حذف المنحة: ' . $event->scholarship->name;
                $users = $event->scholarship->students;
                break;

            case \App\Events\ScholarshipEnrolled::class:
                $title = 'تم التسجيل على منحة';
                $message = $event->user->name . ' تم تسجيله على المنحة: ' . $event->scholarship->name;
                $users = [$event->user];
                break;

            default:
                return;
        }
        // إشعار جميع المستخدمين أو الطلاب المشتركين
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            $user->notify(new ScholarshipNotification($title, $message));
        }
    }
}