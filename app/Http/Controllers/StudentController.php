<?php

namespace App\Http\Controllers;

use App\Models\g;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{

    public function registerStudent(Request $request)
{
    $validated = $request->validate([
        'first_name' => 'required',
        'last_name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6',
        'phone_number' => 'required',
        'age' => 'required|integer',
        'university_name' => 'required|string',
        'level' => 'required|string',
    ]);

    $user = User::create([
        'first_name' => $validated['first_name'],
        'last_name' => $validated['last_name'],
        'email' => $validated['email'],
        'password' => bcrypt($validated['password']),
        'phone_number' => $validated['phone_number'],
        'age' => $validated['age'],
        'role_id' => 3, // فرضًا 3 = student
    ]);

    $user->student()->create([
        'university_name' => $validated['university_name'],
        'level' => $validated['level'],
    ]);

        // $user = Auth::user();
    $token = $user->createToken('auth_token')->accessToken; // ✅ Passport هنا

    return response()->json([
        'message' => 'تم تسجيل الدخول بنجاح',
        'user' => $user,
        'token' => $token,
    ]);
    // return response()->json(['message' => 'تم تسجيل الطالب بنجاح']);
}
public function studentLogin(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'], 401);
    }

    // تحقق من أن المستخدم طالب
    if (!$user->student) {
        return response()->json(['message' => 'هذا الحساب ليس طالباً'], 403);
    }

    $token = $user->createToken('auth_token')->accessToken;

    return response()->json([
        'message' => 'تم تسجيل الدخول كطالب',
        'user' => $user,
        'student_profile' => $user->student,
        'token' => $token,
    ]);
}


public function studentProfile(Request $request)
{
    $user = auth()->user();

    if (!$user->student) {
        return response()->json(['message' => 'هذا الحساب ليس طالباً'], 403);
    }

    return response()->json([
        'message' => 'بيانات الطالب',
        'user' => $user,
        'student_profile' => $user->student,
    ]);
}

    public function studentlogout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
}
}
