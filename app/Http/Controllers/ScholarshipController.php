<?php

namespace App\Http\Controllers;

use App\Http\Resources\ScholarshiApplicationResource;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ScholarshipController extends Controller
{
    //
     // عرض جميع المنح
    public function index()
    {
        return response()->json(Scholarship::all());
    }

    // عرض منحة واحدة
    public function show($id)
    {
        $scholarship = Scholarship::findOrFail($id);
        return response()->json($scholarship);
    }

    // إنشاء منحة جديدة

    // public function store(Request $request)
    // {
        
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'type_of_financing' => 'required|string|max:255',
    //         'funding_agency' => 'required|string|max:255',
    //         'achieved_certificate' => 'required|string|max:255',
    //         'required_documents' => 'nullable|string',
    //         'advantages' => 'nullable|string',
    //         'required_certificates' => 'nullable|string',
    //         'university' => 'required|string|max:255',
    //         'country' => 'required|string|max:255',
    //         'specialization' => 'nullable|string|max:255',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    //     ]);

    //     if ($request->hasFile('image')) {
    //         $validated['image'] = $request->file('image')->store('scholarships', 'public');
    //     }

    //     $scholarship = Scholarship::create($validated);
    //     return response()->json($scholarship, 201);
    // }

 public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'type_of_financing' => 'required|string|max:255',
        'funding_agency' => 'required|string|max:255',
        'achieved_certificate' => 'required|string|max:255',
        'required_documents' => 'nullable|string',
        'advantages' => 'nullable|string',
        'required_certificates' => 'nullable|string',
        'university' => 'required|string|max:255',
        'country' => 'required|string|max:255',
        'specialization' => 'nullable|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    // ربط المنحة بالمستخدم الحالي
    $validated['user_id'] = auth()->id();

    // معالجة رفع الصورة
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('scholarships', 'public');
        $validated['image'] = $path;
    }

    $scholarship = Scholarship::create($validated);

    // إضافة رابط مباشر للصورة
    $scholarship->image_url = $scholarship->image 
        ? asset('storage/' . $scholarship->image) 
        : null;

    // إطلاق الحدث
    event(new \App\Events\ScholarshipCreated($scholarship));

    return response()->json($scholarship, 201);
}
// تعديل منحة
public function update(Request $request, $id)
{
    $scholarship = Scholarship::findOrFail($id);

    $validated = $request->validate([
        'name' => 'sometimes|required|string|max:255',
        'description' => 'nullable|string',
        'type_of_financing' => 'sometimes|required|string|max:255',
        'funding_agency' => 'sometimes|required|string|max:255',
        'achieved_certificate' => 'sometimes|required|string|max:255',
        'required_documents' => 'nullable|string',
        'advantages' => 'nullable|string',
        'required_certificates' => 'nullable|string',
        'university' => 'sometimes|required|string|max:255',
        'country' => 'sometimes|required|string|max:255',
        'specialization' => 'nullable|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    // معالجة الصورة إذا تم رفع واحدة جديدة
    if ($request->hasFile('image')) {
        // حذف الصورة القديمة إن وجدت
        if ($scholarship->image) {
            Storage::disk('public')->delete($scholarship->image);
        }

        // تخزين الصورة الجديدة
        $validated['image'] = $request->file('image')->store('scholarships', 'public');
    }

    $scholarship->update($validated);

    // إضافة رابط مباشر للصورة
    $scholarship->image_url = $scholarship->image 
        ? asset('storage/' . $scholarship->image) 
        : null;

    // إطلاق الحدث
    event(new \App\Events\ScholarshipUpdated($scholarship));

    return response()->json($scholarship);
}

    // حذف منحة
    public function destroy($id)
    {
        $scholarship = Scholarship::findOrFail($id);
        if ($scholarship->image) {
            Storage::disk('public')->delete($scholarship->image);
        }
        
        event(new \App\Events\ScholarshipDeleted($scholarship));
        $scholarship->delete();

        return response()->json(['message' => 'Scholarship deleted successfully']);
    }

    // التسجيل على منحة
//     public function applyScholarship(Request $request)
// {
//     $user = $request->user(); // الطالب المصادق عليه

//     $validator = Validator::make($request->all(), [
//         'academic_stage' => 'required|in:Pre-Secondary,Secondary,Institute,University Degree,Master\'s,PhD',
//         'school_name' => 'required|string|max:255',
//         'field_of_study' => 'required|string|max:255',
//         'academic_year' => 'required|string|max:20',
//         'average' => 'nullable|numeric|min:0|max:100',
//         'placement_test' => 'required|boolean',
//         'language_level' => 'required|in:Beginner,Weak-Elementary,Pre-Intermediate,Intermediate,Advanced-Upper-Intermediate,I Can\'t Decide',
//     ]);

//     if ($validator->fails()) {
//         return response()->json([
//             'errors' => $validator->errors()
//         ], 422);
//     }

//     $application = ScholarshipApplication::create([
//         'user_id' => $user->id,
//         'academic_stage' => $request->academic_stage,
//         'school_name' => $request->school_name,
//         'field_of_study' => $request->field_of_study,
//         'academic_year' => $request->academic_year,
//         'average' => $request->average,
//         'placement_test' => $request->placement_test,
//         'language_level' => $request->language_level,
//     ]);

//     event(new \App\Events\ScholarshipEnrolled(auth()->user(), $application));

//     return response()->json([
//         'message' => 'تم تقديم طلب المنحة بنجاح',
//         'application' => $application
//     ], 201);
// }

    // 🟢 تقديم طلب منحة
    public function apply(Request $request, $scholarship_id)
    {
        $request->validate([
            'academic_stage' => 'required|in:Pre-Secondary,Secondary,Institute,University Degree,Masters,PhD',
            'school_name' => 'required|string|max:255',
            'field_of_study' => 'required|string|max:255',
            'academic_year' => 'required|string|max:20',
            'average' => 'nullable|numeric|min:0|max:100',
            'placement_test' => 'boolean',
            'language_level' => 'required|in:Beginner,Weak-Elementary,Pre-Intermediate,Intermediate,Advanced-Upper-Intermediate,ICantDecide',
        ]);

        $scholarship = Scholarship::findOrFail($scholarship_id);

        $application = ScholarshipApplication::create([
            'scholarship_id' => $scholarship->id,
            'user_id' => Auth::id(),
            'academic_stage' => $request->academic_stage,
            'school_name' => $request->school_name,
            'field_of_study' => $request->field_of_study,
            'academic_year' => $request->academic_year,
            'average' => $request->average,
            'placement_test' => $request->placement_test ?? false,
            'language_level' => $request->language_level,
        ]);

        return response()->json([
            'message' => 'Application submitted successfully',
            'application' => $application
        ], 201);
    }

    // 🟢 عرض طلبات الطالب نفسه
    public function myApplications()
    {
        $applications = ScholarshipApplication::where('user_id', Auth::id())
            ->with('scholarship')
            ->get();

        return response()->json($applications);
    }

    // 🟢 عرض كل الطلبات (لـ admin/manager فقط)
public function allApplications()
{
    $user = auth()->user();

    if (!in_array($user->role->name, ['admin', 'manager'])) {
        return response()->json(['message' => 'This action is unauthorized.'], 403);
    }

    // جلب الطلبات التي لم يتم قبولها أو رفضها بعد
    $applications = ScholarshipApplication::where('status', 'pending')->get();

    return response()->json($applications);
}

    
// 🟢 تحديث حالة الطلب (قبول أو رفض)
public function updateScholarshipStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:accepted,rejected',
    ]);

    $application = ScholarshipApplication::findOrFail($id);
    $application->update(['status' => $request->status]);

    return response()->json([
        'message' => 'Application ' . $request->status,
        'application' => $application
    ]);
}

public function myScholarshipApplication($scholarship_id)
{
    $user = auth()->user();

    $scholarship_applications = ScholarshipApplication::where('user_id', $user->id)
        ->where('scholarship_id', $scholarship_id)
        ->get();

    return response()->json([
        'message' => 'Your enrollments for this scholarship',
        'enrollments' => ScholarshiApplicationResource::collection($scholarship_applications)
    ]);
}

public function allPendingScholarshipApplication()
{
    $user = auth()->user();
    if (!in_array($user->role->name, ['admin', 'manager'])) {
        return response()->json(['message' => 'This action is unauthorized.'], 403);
    }

    $pending = ScholarshipApplication::with('user', 'scholarship')
        ->where('status', 'pending')
        ->get();

    return response()->json([
        'message' => 'All pending scholarship enrollments',
        'scholarship_applications' => ScholarshiApplicationResource::collection($pending)
    ]);
}

}

