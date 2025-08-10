<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    //
    
    public function registerAdmin(Request $request)
{
    $validated = $request->validate([
        'first_name' => 'required',
        'last_name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6',
        'phone_number' => 'required',
        'age' => 'required|integer',
        'job_title' => 'required|string',
    ]);

    $user = User::create([
        'first_name' => $validated['first_name'],
        'last_name' => $validated['last_name'],
        'email' => $validated['email'],
        'password' => bcrypt($validated['password']),
        'phone_number' => $validated['phone_number'],
        'age' => $validated['age'],
        'role_id' => 1, // فرضًا 1 = admin
    ]);

    $user->admin()->create([
        'job_title' => $validated['job_title'],
    ]);

    // $user = Auth::user();
    $token = $user->createToken('auth_token')->accessToken; // ✅ Passport هنا

    return response()->json([
        'message' => 'تم تسجيل الدخول بنجاح',
        'user' => $user,
        'token' => $token,
    ]);
    // return response()->json(['message' => 'تم تسجيل المشرف بنجاح']);
}

public function adminLogin(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'], 401);
    }

    if (!$user->admin) {
        return response()->json(['message' => 'هذا الحساب ليس مشرفاً'], 403);
    }

    $token = $user->createToken('auth_token')->accessToken;

    return response()->json([
        'message' => 'تم تسجيل الدخول كمشرف',
        'user' => $user,
        'admin_profile' => $user->admin,
        'token' => $token,
    ]);
}

public function adminProfile(Request $request)
{
    $user = auth()->user();

    if (!$user->admin) {
        return response()->json(['message' => 'هذا الحساب ليس مشرفاً'], 403);
    }

    return response()->json([
        'message' => 'بيانات المشرف',
        'user' => $user,
        'admin_profile' => $user->admin,
    ]);
}

    public function adminlogout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
}
}
