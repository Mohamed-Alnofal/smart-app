<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseLevelController extends Controller
{
      // ==========================
    // 🔸 COURSES CRUD
    // ==========================

    // Get all courses with user and levels
public function indexCourses()
{
    return Course::all(); // فقط الكورسات بدون علاقات
}


    // Get single course
public function showCourse($id)
{
    return Course::with(['levels' => function ($query) {
        $query->select('id', 'course_id', 'name', 'description');
    }])->findOrFail($id);
}


    // Create course (attached to auth user)
    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            'course_name' => 'required|string|max:255',
            'course_details' => 'nullable|string',
            'certificate' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('courses', 'public');
        }

        $validated['user_id'] = auth()->id(); // Attach to logged-in user

        $course = Course::create($validated);
        return response()->json($course, 201);
    }

    // Update course
    public function updateCourse(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        if ($course->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'course_name' => 'sometimes|required|string|max:255',
            'course_details' => 'nullable|string',
            'certificate' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($course->image) {
                Storage::disk('public')->delete($course->image);
            }
            $validated['image'] = $request->file('image')->store('courses', 'public');
        }

        $course->update($validated);
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

        $course->delete();
        return response()->json(['message' => 'Course deleted successfully']);
    }

    // level crud

    public function showLevels($courseId)
{
    $course = Course::with(['levels' => function($query) {
        $query->select('id', 'course_id', 'name', 'description');
    }])->findOrFail($courseId);

    return response()->json(
        $course->levels->map(function ($level) {
            return [
                'name' => $level->name,
                'description' => $level->description
            ];
        })
    );
}

//     public function showLevels($courseId)
// {
//     $course = Course::with('levels')->findOrFail($courseId);
//     return response()->json($course->levels);
// }

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
        'day' => 'required|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
        'start_time' => 'required|date_format:H:i',
        'start_date' => 'required|date',
        'description' => 'nullable|string',
    ]);

    $validated['course_id'] = $courseId;

    $level = Level::create($validated);
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
        'day' => 'sometimes|required|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
        'start_time' => 'sometimes|required|date_format:H:i',
        'start_date' => 'sometimes|required|date',
        'description' => 'nullable|string',
    ]);

    $level->update($validated);

    return response()->json([
        'id' => $level->id,
        'name' => $level->name,
        'teacher' => $level->teacher,
        'seats_number' => $level->seats_number,
        'status' => $level->status,
        'day' => $level->day,
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
    return response()->json(['message' => 'Level deleted successfully']);
}
// التسجيل في كورس
public function enrollInLevel(Request $request, $levelId)
{
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

        auth()->user()->notify(new \App\Notifications\LevelEnrolledBroadcast($level->name));

    return response()->json([
        'message' => 'Enrolled successfully',
        'data' => $enrollment
    ]);
}


}
