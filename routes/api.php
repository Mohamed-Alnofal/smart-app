<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\NotificationController;

use App\Http\Controllers\ManagerController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseLevelController;
use App\Http\Controllers\ScholarshipController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/signup', [UserController::class, 'signUp']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/complete-profile', [UserController::class, 'completeProfile']);
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/update-profile', [UserController::class, 'updateProfile']); //
    Route::post('/logout', [UserController::class, 'logout']);
});
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::post('register' ,[UserController::class , 'register']);

// Route::post('register' , [UserController::class, 'register']);
// Route::post('login' , [UserController::class, 'login']);
// Route::post('/register', [UserController::class, 'register']);
// Route::middleware('auth:api')->group(function () {
//     Route::get('/profile', function () {
//         return response()->json(auth()->user());
//     });
// });
// 
// Route::post('/register', [UserController::class, 'register']);
// // Route::post('/register/admin', [UserController::class, 'registerAdmin']);
// // Route::post('/register/manager', [UserController::class, 'registerManager']);
// // Route::post('/register/student', [UserController::class, 'registerStudent']);
// Route::post('/login', [UserController::class, 'login']);

// Route::middleware('auth:api')->group(function () {
//     Route::get('/profile', [UserController::class, 'profile']);
//     Route::post('/logout', [UserController::class, 'logout']);
// });

// Route::post('/register/admin', [AdminController::class, 'registerAdmin']);
// Route::post('/login/admin', [AdminController::class, 'adminLogin']);
// // Route::get('/profile/admin', [AdminController::class, 'adminProfile']);


// Route::post('/register/manager', [ManagerController::class, 'registerManager']);
// Route::post('/login/manager', [ManagerController::class, 'managerLogin']);

// Route::post('/register/student', [StudentController::class, 'registerStudent']);
// Route::post('/login/student', [StudentController::class, 'studentLogin']);

// Route::middleware('auth:api')->group(function () {
//     //Admin
//     Route::get('/profile/admin', [AdminController::class, 'adminProfile']);
//     Route::post('/logout/admin', [AdminController::class, 'adminlogout']);
//     //Manager
//     Route::get('/profile/manager', [ManagerController::class, 'managerProfile']);
//     Route::post('/logout/manager', [ManagerController::class, 'managerlogout']);
//     //Student
//     Route::get('/profile/student', [StudentController::class, 'studentProfile']);
//     Route::post('/logout/student', [StudentController::class, 'studentlogout']);
// });

Route::post('/password/requestCode', [PasswordResetController::class, 'requestCode']);
Route::post('/password/verifyAccount', [PasswordResetController::class, 'verifyAccount']);
Route::post('/password/reset-with-code', [PasswordResetController::class, 'resetWithCode']);
// Route::middleware(['auth:api', 'role:manager'])->get('/manager/profile', [ManagerController::class, 'managerProfile']);
// use App\Http\Controllers\CourseController;
// use App\Http\Controllers\CourseLevelController;

// Route::middleware(['auth:api', 'role:admin,manager'])->group(function () {
//     Route::post('/courses', [CourseController::class, 'store']);
// });



// Courses
Route::middleware('auth:api')->group(function () {

    // مسموح للجميع (admin, manager, student)
    // Courses
    Route::get('/courses', [CourseLevelController::class, 'indexCourses']);
    Route::get('/courses/{id}', [CourseLevelController::class, 'showCourse']);
    Route::get('/courses/{id}/image', [CourseLevelController::class, 'showCourseImage']);

    // فقط للمدير أو الأدمن
    Route::middleware('role:admin,manager')->group(function () {
        Route::post('/courses', [CourseLevelController::class, 'storeCourse']);
        Route::post('/updateCourse/{id}', [CourseLevelController::class, 'updateCourse']);
        Route::delete('/courses/{id}', [CourseLevelController::class, 'destroyCourse']);
    });
});

// Levels
use App\Http\Controllers\LevelController;

Route::middleware('auth:api')->group(function () {
    // المستويات (Levels)
    Route::get('/courses/{courseId}/levels', [CourseLevelController::class, 'showLevels']);
    Route::get('/levels/{id}', [CourseLevelController::class, 'showLevel']);
    Route::post('/levels/{levelId}/enroll', [CourseLevelController::class, 'enrollInLevel']);
    Route::get('/levels/{level_id}/my-enrollments', [CourseLevelController::class, 'myEnrollments']);


    Route::middleware('role:admin,manager')->group(function () {

        Route::get('/enrollments', [CourseLevelController::class, 'allEnrollments']);
        Route::post('/courses/{courseId}/levels', [CourseLevelController::class, 'storeLevel']);
        Route::post('/levels/{id}', [CourseLevelController::class, 'updateLevel']);
        Route::delete('/levels/{id}', [CourseLevelController::class, 'destroyLevel']);
        Route::put('/enrollments/{id}/status', [CourseLevelController::class, 'updateEnrollmentStatus']);
        Route::get('/enrollments/pending', [CourseLevelController::class, 'allPendingEnrollments']);


    });
});


// scholarships
Route::middleware('auth:api')->group(function () {

    Route::get('/scholarships', [ScholarshipController::class, 'index']);
    Route::get('/scholarships/{id}', [ScholarshipController::class, 'show']);
    Route::get('/user/{userId}/scholarships', [ScholarshipController::class, 'userScholarships']);
    // Route::post('/applyScholarship', [ScholarshipController::class, 'applyScholarship']);
    // الطالب يقدم على منحة
    Route::post('/scholarships/{id}/apply', [ScholarshipController::class, 'apply']);
    // الطالب يشوف طلباته
    Route::get('/my-scholarship-applications', [ScholarshipController::class, 'myApplications']);


    Route::middleware('role:admin,manager')->group(function () {
        Route::post('/scholarships', [ScholarshipController::class, 'store']);
        Route::post('/scholarships/{id}', [ScholarshipController::class, 'update']);
        Route::delete('/scholarships/{id}', [ScholarshipController::class, 'destroy']);
        Route::get('/scholarship-applications', [ScholarshipController::class, 'allApplications']);
        // Route::post('/scholarship-applications/{id}/accept', [ScholarshipController::class, 'accept']);
        // Route::post('/scholarship-applications/{id}/reject', [ScholarshipController::class, 'reject']);

        Route::put('/scholarships/applications/{id}/status', [ScholarshipController::class, 'updateScholarshipStatus']);

    });
});

// Notification
Route::middleware('auth:api')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'getNotifications']);
    Route::get('/notifications/unread', [NotificationController::class, 'getUnreadNotifications']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markNotificationAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
});

// Homepage
use App\Http\Controllers\UserCourseController;

Route::middleware('auth:api')->group(function () {
    // عرض الكورسات المسجلة للطالب
    Route::get('/homepage', [UserCourseController::class, 'homepage']);
});


use App\Http\Controllers\PostController;

// Posts
Route::middleware('auth:api')->group(function () {

    Route::get('/posts', [PostController::class, 'index']);

    Route::post('/posts/{id}/like', [PostController::class, 'like']);
    Route::post('/posts/{id}/dislike', [PostController::class, 'dislike']);

    Route::middleware('role:admin,manager')->group(function () {
        Route::post('/posts', [PostController::class, 'store']);
        Route::put('/posts/{id}', [PostController::class, 'update']);
        Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    });
});
