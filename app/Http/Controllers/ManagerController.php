<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManagerController extends Controller
{
    //
    
public function registerManager(Request $request)
{
    $validated = $request->validate([
        'first_name' => 'required',
        'last_name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6',
        'phone_number' => 'required',
        'age' => 'required|integer',
        'department' => 'required|string',
    ]);

    $user = User::create([
        'first_name' => $validated['first_name'],
        'last_name' => $validated['last_name'],
        'email' => $validated['email'],
        'password' => bcrypt($validated['password']),
        'phone_number' => $validated['phone_number'],
        'age' => $validated['age'],
        'role_id' => 2, // فرضًا 2 = manager
    ]);

    $user->manager()->create([
        'department' => $validated['department'],
    ]);

        // $user = Auth::user();
    $token = $user->createToken('auth_token')->accessToken; // ✅ Passport هنا

    return response()->json([
        'message' => 'تم تسجيل الدخول بنجاح',
        'user' => $user,
        'token' => $token,
    ]);
    // return response()->json(['message' => 'تم تسجيل المدير بنجاح']);
}

public function managerLogin(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'], 401);
    }

    if (!$user->manager) {
        return response()->json(['message' => 'هذا الحساب ليس مديراً'], 403);
    }

    $token = $user->createToken('auth_token')->accessToken;

    return response()->json([
        'message' => 'تم تسجيل الدخول كمدير',
        'user' => $user,
        'manager_profile' => $user->manager,
        'token' => $token,
    ]);
}


public function managerProfile(Request $request)
{
    $user = auth()->user();

    if (!$user->manager) {
        return response()->json(['message' => 'هذا الحساب ليس مديراً'], 403);
    }

    return response()->json([
        'message' => 'بيانات المدير',
        'user' => $user,
        'manager_profile' => $user->manager,
    ]);
}

    public function managerlogout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
}
}
