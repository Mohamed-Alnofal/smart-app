<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LevelController extends Controller
{
     // 🟩 عرض كل المستويات المرتبطة بكورس معين
    public function index(Course $course)
    {
        return response()->json([
            'course' => $course->name,
            'levels' => $course->levels
        ]);
    }

    // 🟦 عرض مستوى معين
    public function show($id)
    {
        $level = Level::findOrFail($id);
        return response()->json($level);
    }

    // 🟧 إنشاء مستوى جديد
    // public function store(Request $request, Course $course)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:255',
    //         'instructor_name' => 'required|string|max:255',
    //         'status' => 'required|in:open,closed',
    //         'description' => 'nullable|string',
    //         'seats' => 'required|integer|min:1',
    //         'course_time' => 'required|date'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     $level = $course->levels()->create($validator->validated());

    //     return response()->json([
    //         'message' => 'تم إنشاء المستوى بنجاح',
    //         'level' => $level
    //     ], 201);
    // }
public function store(Request $request, $courseId)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'instructor_name' => 'required|string|max:255',//*
        'status' => 'required|string',
        'description' => 'nullable|string',
        'seats' => 'required|integer',
        'course_time' => 'required|time',
        'start_date' => 'required|date'
    ]);

    // قم بإضافة course_id إلى البيانات
    $validated['course_id'] = $courseId;

    $level = Level::create($validated);

    return response()->json($level, 201);
}
    // 🟨 تعديل مستوى
    public function update(Request $request, $id)
    {
        $level = Level::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'instructor_name' => 'sometimes|required|string|max:255',//*
            'status' => 'sometimes|required|in:open,closed',
            'description' => 'nullable|string',
            'seats' => 'sometimes|required|integer|min:1',
            'course_time' => 'sometimes|required|date',
            'start_date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $level->update($validator->validated());

        return response()->json([
            'message' => 'تم تحديث المستوى بنجاح',
            'level' => $level
        ]);
    }

    // 🟥 حذف مستوى
    public function destroy($id)
    {
        $level = Level::findOrFail($id);
        $level->delete();

        return response()->json(['message' => 'تم حذف المستوى بنجاح']);
    }
}
