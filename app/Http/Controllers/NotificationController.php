<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // كل الإشعارات
    public function getNotifications()
    {
        return auth()->user()->notifications;
    }

    // الإشعارات غير المقروءة
    public function getUnreadNotifications()
    {
        return auth()->user()->unreadNotifications;
    }

    // تعليم إشعار كمقروء
    public function markNotificationAsRead($id)
    {
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['message' => 'تم تعليم الإشعار كمقروء']);
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'تم تعليم جميع الإشعارات كمقروءة']);
    }

}
