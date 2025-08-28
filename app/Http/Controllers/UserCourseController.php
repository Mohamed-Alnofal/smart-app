<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserCourseController extends Controller
{
    //

    public function homepage(Request $request)
{
    $user = $request->user();

    // جلب الكورسات عبر جدول user_courses
    $courses = $user->userCourses()->with('course')->get()->map(function($uc) {
        return $uc->course;
    });

    return response()->json([
        'message' => 'الكورسات المسجلة للطالب',
        'courses' => $courses
    ]);
}

}
