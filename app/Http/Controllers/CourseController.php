<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class CourseController extends Controller
{
    //  public function index()
    // {
    //     return response()->json(Course::with('user')->get());
    // }


    // CourseController.php
public function index()
{
    $courses = Course::latest()->get();

    return response()->json([
        'courses' => $courses->map(function ($course) {
            return [
                'id' => $course->id,
                'name' => $course->name,
                'description' => $course->description,
                'information' => $course->information,
                'image_url' => $course->image_url,
            ];
        }),
    ]);
}

    public function show($id)
    {
        $course = Course::with('user')->findOrFail($id);
        return response()->json($course);
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string',
    //         'description' => 'required|string',
    //         'information' => 'nullable|string',
    //         'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    //     ]);

    //     $imagePath = $request->hasFile('image') 
    //         ? $request->file('image')->store('courses', 'public') 
    //         : null;

    //     $course = Course::create([
    //         'user_id' => auth()->id(),
    //         'name' => $request->name,
    //         'description' => $request->description,
    //         'information' => $request->information,
    //         'image' => $imagePath,
    //     ]);

    //     return response()->json(['message' => 'تم إنشاء الكورس', 'course' => $course], 201);
    // }


    // app/Http/Controllers/CourseController.php
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'information' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $data = $request->only(['name', 'description', 'information']);
    $data['user_id'] = auth()->id();

    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('courses', 'public');
        $data['image'] = $path;
    }

    $course = Course::create($data);

    return response()->json([
        'message' => 'تم إنشاء الكورس بنجاح',
        'course' => [
            'id' => $course->id,
            'name' => $course->name,
            'description' => $course->description,
            'information' => $course->information,
            'image_url' => $course->image_url,
        ]
    ]);
}

    // public function update(Request $request, $id)
    // {
    //     $course = Course::findOrFail($id);

    //     $request->validate([
    //         'name' => 'sometimes|string',
    //         'description' => 'sometimes|string',
    //         'information' => 'nullable|string',
    //         'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    //     ]);

    //     if ($request->hasFile('image')) {
    //         if ($course->image) Storage::disk('public')->delete($course->image);
    //         $course->image = $request->file('image')->store('courses', 'public');
    //     }

    //     $course->update($request->only(['name', 'description', 'information']));
    //     $course->save();

    //     return response()->json(['message' => 'تم التحديث بنجاح', 'course' => $course]);
    // }
public function update(Request $request, $id)
{
    $course = Course::findOrFail($id);

    // تحقق من وجود صورة جديدة ورفعها
    if ($request->hasFile('image')) {
        // حذف الصورة القديمة لو موجودة
        if ($course->image) {
            Storage::disk('public')->delete($course->image);
        }
        $imagePath = $request->file('image')->store('courses', 'public');
        $course->image = $imagePath;
    }

    $course->name = $request->name;
    $course->description = $request->description;
    $course->information = $request->information;
    $course->save();

    return response()->json([
        'message' => 'تم تحديث الكورس بنجاح',
        'course' => [
            'id' => $course->id,
            'name' => $course->name,
            'description' => $course->description,
            'information' => $course->information,
            'image_url' => $course->image ? url('storage/' . $course->image) : null,  // <== هنا رابط كامل للصورة
        ],
    ]);
}


    // public function destroy($id)
    // {
    //     $course = Course::findOrFail($id);

    //     if ($course->image) Storage::disk('public')->delete($course->image);
    //     $course->delete();

    //     return response()->json(['message' => 'تم الحذف بنجاح']);
    // }
    public function destroy($id)
{
    $course = Course::findOrFail($id);

    if ($course->image && Storage::disk('public')->exists($course->image)) {
        Storage::disk('public')->delete($course->image);
    }

    $course->delete();

    return response()->json(['message' => 'تم حذف الكورس بنجاح']);
}

}
