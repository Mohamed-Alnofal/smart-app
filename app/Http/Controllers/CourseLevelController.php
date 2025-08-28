<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Events\CourseCreated;
use App\Events\CourseUpdated;
use App\Events\CourseDeleted;
use App\Events\LevelCreated;
use App\Events\LevelUpdated;
use App\Events\LevelDeleted;
use App\Events\EnrollmentCreated;

class CourseLevelController extends Controller
{
      // ==========================
    // 🔸 COURSES CRUD
    // ==========================

    // Get all courses with user and levels
// public function indexCourses()
// {
//     return Course::all(); // فقط الكورسات بدون علاقات
// }

public function indexCourses()
{
    $courses = Course::all();

    // إضافة رابط الصورة لكل كورس
    $courses->transform(function ($course) {
        if ($course->image) {
            $course->image_url = asset('storage/' . $course->image);
        } else {
            $course->image_url = null; // إذا لم توجد صورة
        }
        return $course;
    });

    return response()->json($courses);
}


    // Get single course
// public function showCourse($id)
// {
//     return Course::with(['levels' => function ($query) {
//         $query->select('id', 'course_id', 'name', 'description');
//     }])->findOrFail($id);
// }

public function showCourse($id)
{
    $course = Course::with(['levels' => function ($query) {
        $query->select('id', 'course_id', 'name', 'description');
    }])->findOrFail($id);

    // إضافة رابط الصورة
    if ($course->image) {
        $course->image_url = asset('storage/' . $course->image);
    } else {
        $course->image_url = null;
    }

    return response()->json($course);
}


    // Create course (attached to auth user)
    // public function storeCourse(Request $request)
    // {
    //     $validated = $request->validate([
    //         'course_name' => 'required|string|max:255',
    //         'course_details' => 'nullable|string',
    //         'certificate' => 'nullable|string',
    //         'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    //     ]);

    //     if ($request->hasFile('image')) {
    //         $validated['image'] = $request->file('image')->store('courses', 'public');
    //     }

    //     $validated['user_id'] = auth()->id(); // Attach to logged-in user

    //     $course = Course::create($validated);
    //         // إطلاق الحدث
    //     event(new CourseCreated($course));

    //     return response()->json($course, 201);
    // }

    public function storeCourse(Request $request)
{
    $validated = $request->validate([
        'course_name' => 'required|string|max:255',
        'course_details' => 'nullable|string',
        'certificate' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // التعامل مع رفع الصورة
    if ($request->hasFile('image')) {
        // تخزين الصورة في storage/app/public/courses
        $path = $request->file('image')->store('courses', 'public');
        $validated['image'] = $path; // حفظ المسار في قاعدة البيانات
    }

    $validated['user_id'] = auth()->id(); // ربط الكورس بالمستخدم المسجل

    $course = Course::create($validated);

    // إطلاق الحدث
    event(new CourseCreated($course));

    // إضافة رابط عام للعرض
    $course->image_url = $course->image ? asset('storage/' . $course->image) : null;

    return response()->json($course, 201);
}

    // CourseController.php
public function showCourseImage($id)
{
    $course = Course::findOrFail($id);

    if (!$course->image || !Storage::disk('public')->exists($course->image)) {
        return response()->json(['error' => 'Image not found'], 404);
    }

    return response()->file(storage_path('app/public/' . $course->image));
}

    // // Update course
    // public function updateCourse(Request $request, $id)
    // {
    //     $course = Course::findOrFail($id);

    //     if ($course->user_id !== auth()->id()) {
    //         return response()->json(['error' => 'Unauthorized'], 403);
    //     }

    //     $validated = $request->validate([
    //         'course_name' => 'sometimes|required|string|max:255',
    //         'course_details' => 'nullable|string',
    //         'certificate' => 'nullable|string',
    //         'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    //     ]);

    //     if ($request->hasFile('image')) {
    //         if ($course->image) {
    //             Storage::disk('public')->delete($course->image);
    //         }
    //         $validated['image'] = $request->file('image')->store('courses', 'public');
    //     }

    //     $course->update($validated);

    //         // إطلاق الـ Event
    //     event(new CourseUpdated($course));

    //     return response()->json($course);
    // }
// Update course
public function updateCourse(Request $request, $id)
{
    $course = Course::findOrFail($id);

    // التأكد من أن المستخدم المحدث هو صاحب الكورس
    if ($course->user_id !== auth()->id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $validated = $request->validate([
        'course_name' => 'sometimes|required|string|max:255',
        'course_details' => 'nullable|string',
        'certificate' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // معالجة الصورة إذا تم رفعها
    if ($request->hasFile('image')) {
        // حذف الصورة القديمة إذا موجودة
        if ($course->image) {
            Storage::disk('public')->delete($course->image);
        }
        $validated['image'] = $request->file('image')->store('courses', 'public');
    }

    $course->update($validated);

    // إضافة رابط الصورة للعرض مباشرة
    if ($course->image) {
        $course->image_url = asset('storage/' . $course->image);
    }

    // إطلاق الحدث
    event(new CourseUpdated($course));

    return response()->json($course);
}

    // Delete course
    public function destroyCourse($id)
    {
        $course = Course::findOrFail($id);

        if ($course->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($course->image) {
            Storage::disk('public')->delete($course->image);
        }

        event(new CourseDeleted($course));
        $course->delete();

            // إطلاق الـ Event

        return response()->json(['message' => 'Course deleted successfully']);
    }

    // level crud

//     public function showLevels($courseId)
// {
//     $course = Course::with(['levels' => function($query) {
//         $query->select('id', 'course_id', 'name', 'description');
//     }])->findOrFail($courseId);

//     return response()->json(
//         $course->levels->map(function ($level) {
//             return [
//                 'name' => $level->name,
//                 'description' => $level->description
//             ];
//         })
//     );
// }

    public function showLevels($courseId)
{
    $course = Course::with('levels')->findOrFail($courseId);
    return response()->json($course->levels);
}

public function showLevel($id)
{
    $level = Level::findOrFail($id);

    return response()->json([
        'name' => $level->name,
        'description' => $level->description,
        'start_date' => $level->start_date,
        'start_time' => $level->start_time,
        'seats_number' => $level->seats_number,
        'status' => $level->status,
    ]);
}

    public function storeLevel(Request $request, $courseId)
{
    $course = Course::findOrFail($courseId);

    if ($course->user_id !== auth()->id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'teacher' => 'required|string|max:255',
        'seats_number' => 'required|integer|min:0',
        'status' => 'required|in:full,starting_soon,coming_soon',
        'days' => 'required|array|min:1',
        'days.*' => 'in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
        // 'day' => 'required|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
        'start_time' => 'required|date_format:H:i',
        'start_date' => 'required|date',
        'description' => 'nullable|string',
    ]);

    $validated['course_id'] = $courseId;

    $level = Level::create($validated);

        // إطلاق الـ Event
    //event(new LevelCreated($level));

    return response()->json($level, 201);
}


// public function updateLevel(Request $request, $id)
// {
//     $level = Level::findOrFail($id);
//     $course = $level->course;

//     if ($course->user_id !== auth()->id()) {
//         return response()->json(['error' => 'Unauthorized'], 403);
//     }

//     $validated = $request->validate([
//         'name' => 'sometimes|required|string|max:255',
//         'teacher' => 'sometimes|required|string|max:255',
//         'seats_number' => 'sometimes|required|integer|min:0',
//         'status' => 'sometimes|required|in:full,starting_soon,coming_soon',
//         'day' => 'sometimes|required|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
//         'start_time' => 'sometimes|required|date_format:H:i',
//         'start_date' => 'sometimes|required|date',
//         'description' => 'nullable|string',
//     ]);

//     $level->update($validated);
//     return response()->json($level);
// }


public function updateLevel(Request $request, $id)
{
    $level = Level::findOrFail($id);
    $course = $level->course;

    if ($course->user_id !== auth()->id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $validated = $request->validate([
        'name' => 'sometimes|required|string|max:255',
        'teacher' => 'sometimes|required|string|max:255',
        'seats_number' => 'sometimes|required|integer|min:0',
        'status' => 'sometimes|required|in:full,starting_soon,coming_soon',
        'days' => 'required|array|min:1',
        'days.*' => 'in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
        // 'day' => 'sometimes|required|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
        'start_time' => 'sometimes|required|date_format:H:i',
        'start_date' => 'sometimes|required|date',
        'description' => 'nullable|string',
    ]);

    $level->update($validated);

        // إطلاق الـ Event
    // event(new LevelUpdated($level));

    return response()->json([
        'id' => $level->id,
        'name' => $level->name,
        'teacher' => $level->teacher,
        'seats_number' => $level->seats_number,
        'status' => $level->status,
        'days' => $level->days,
        'start_time' => $level->start_time,
        'start_date' => $level->start_date,
        'description' => $level->description,
    ]);
}


public function destroyLevel($id)
{
    $level = Level::findOrFail($id);
    $course = $level->course;

    if ($course->user_id !== auth()->id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $level->delete();
        // إطلاق الـ Event
    // event(new LevelDeleted($level));

    return response()->json(['message' => 'Level deleted successfully']);
}
// التسجيل في كورس
public function enrollInLevel(Request $request, $levelId)
{
    // جلب الطالب (مثلاً المستخدم الحالي)
    $user = auth()->user();

    $validated = $request->validate([
        'academic_stage' => 'required|in:pre-secondary,secondary,institute,university,masters,phd',
        'language_level' => 'required|in:beginner,weak-elementary,pre-intermediate,intermediate,advanced-upper-intermediate,i-cant-decide',
        'time' => 'required|in:18:00,15:00,20:00',
        'days' => 'required|in:tue-thu-wed,sat-sun-mon',
        'learning_method' => 'required|in:at-smart-foundation,online'
    ]);

    $level = Level::findOrFail($levelId);

    // منع التسجيل المكرر
    $exists = Enrollment::where('user_id', auth()->id())
        ->where('level_id', $levelId)
        ->exists();

    if ($exists) {
        return response()->json(['error' => 'You are already enrolled in this level'], 400);
    }

    $enrollment = Enrollment::create([
        'user_id' => auth()->id(),
        'level_id' => $levelId,
        'academic_stage' => $validated['academic_stage'],
        'language_level' => $validated['language_level'],
        'time' => $validated['time'],
        'days' => $validated['days'],
        'learning_method' => $validated['learning_method']
    ]);

    // event(new EnrollmentCreated($user, $level));
    auth()->user()->notify(new \App\Notifications\LevelEnrolledBroadcast($level->name));
        // إطلاق الـ Event
// إطلاق الحدث
    return response()->json([
        'message' => 'Enrolled successfully',
        'data' => $enrollment
    ]);
}


// 🟢 للطالب: عرض طلباته على مستوى معين
public function myEnrollments($level_id)
{
    $user = auth()->user();

    $enrollments = Enrollment::where('user_id', $user->id)
        ->where('level_id', $level_id)
        ->get();

    return response()->json([
        'message' => 'Your enrollments for this level',
        'enrollments' => $enrollments
    ]);
}

// 🟢 للادمن/المدير: عرض كل الطلبات المعلقة من جميع الطلاب لجميع المستويات
public function allPendingEnrollments()
{
    $enrollments = Enrollment::with(['user', 'level.course'])
        ->where('status', 'pending')
        ->get();

    return response()->json([
        'message' => 'All pending enrollments',
        'enrollments' => \App\Http\Resources\EnrollmentResource::collection($enrollments),
    ]);
}
// public function allPendingEnrollments()
// {
//     $user = auth()->user();

//     $pending = Enrollment::with(['user', 'level.course']) // يجيب بيانات الطالب والمستوى والكورس
//         ->where('status', 'pending')
//         ->get();

//     return response()->json([
//         'message' => 'All pending enrollments',
//         'enrollments' => $pending
//     ]);
// }
 // جميع الطلبات

    public function allEnrollments()
    {
        $user = auth()->user();
        if (!in_array($user->role->name, ['admin', 'manager'])) {
            return response()->json(['message' => 'This action is unauthorized.'], 403);
        }

        return response()->json(Enrollment::where('status', 'pending')->get());
    }


    // 🟢 تحديث حالة طلب تسجيل (قبول/رفض)
public function updateEnrollmentStatus(Request $request, $id)
{
    $user = auth()->user();

    // فقط admin أو manager عندهم صلاحية
    if (!in_array($user->role->name, ['admin', 'manager'])) {
        return response()->json(['message' => 'This action is unauthorized.'], 403);
    }

    $request->validate([
        'status' => 'required|in:accepted,rejected', // enum
    ]);

    $enrollment = Enrollment::findOrFail($id);
    $enrollment->status = $request->status;
    $enrollment->save();

    return response()->json([
        'message' => "Enrollment request {$request->status}",
        'enrollment' => $enrollment
    ]);
}

}
