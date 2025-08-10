<?php

namespace App\Http\Controllers;

use App\Models\role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
// قبل role id
// public function signUp(Request $request)
// {
//     $validator = Validator::make($request->all(), [
//         'email' => 'required|email|unique:users',
//         'password' => 'required|string|min:6|confirmed',
//     ]);

//     if ($validator->fails()) {
//         return response()->json([
//             'errors' => $validator->errors()
//         ], 422);
//     }

//     $user = User::create([
//         'email' => $request->email,
//         'password' => Hash::make($request->password),
//     ]);

//     $token = $user->createToken('auth_token')->accessToken;

//     return response()->json([
//         'message' => 'تم إنشاء الحساب..',
//         'user_id' => $user->id,
//         'token' => $token,
//     ], 201);
// }
//  بعد role id
public function signUp(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors()
        ], 422);
    }

    // role_id للطالب فقط
    $studentRoleId = DB::table('roles')->where('name', 'student')->value('id');

    $user = User::create([
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role_id' => $studentRoleId,
    ]);

    $token = $user->createToken('auth_token')->accessToken;

    return response()->json([
        'message' => 'تم إنشاء الحساب بنجاح.',
        'user_id' => $user->id,
        'token' => $token,
    ], 201);
}

// public function completeProfile(Request $request)
// {
//     $validator = Validator::make($request->all(), [
//       //   'user_id' => 'required|exists:users,id',
//         'first_name' => 'required|string|max:255',
//         'last_name' => 'required|string|max:255',
//         'phone_number' => 'required|digits_between:8,15',
//         'gender' => 'required|in:male,female',
//         'age' => 'required|integer|min:10|max:100',
//     ]);

    
//     if ($validator->fails()) {
//         return response()->json([
//             'errors' => $validator->errors()
//         ], 422);
//     }

//     $user = User::find($request->user_id);

//     $user->update([
//         'first_name' => $request->first_name,
//         'last_name' => $request->last_name,
//         'phone_number' => $request->phone_number,
//         'gender' => $request->gender,
//         'age' => $request->age,
//     ]);

//     return response()->json([
//         'message' => 'تم استكمال البيانات الشخصية بنجاح',
//         'user' => $user
//     ]);
// }

public function completeProfile(Request $request)
{
    // التحقق من صحة البيانات المدخلة
    $validator = Validator::make($request->all(), [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'phone_number' => 'required|digits_between:8,15',
        'gender' => 'required|in:male,female',
        'age' => 'required|integer|min:10|max:100',
    ]);

    // إذا كان هناك أخطاء في التحقق
    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors()
        ], 422);
    }

    // الحصول على المستخدم المصادق عليه من التوكن
    $user = $request->user();

    // تحديث بيانات المستخدم
    $user->update([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'phone_number' => $request->phone_number,
        'gender' => $request->gender,
        'age' => $request->age,
    ]);

    return response()->json([
        'message' => 'تم استكمال البيانات الشخصية بنجاح',
        'user' => $user
    ]);
}


public function login(Request $request)
    {
        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // محاولة تسجيل الدخول
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'], 401);
        }

        // المستخدم الحالي
        $user = Auth::user();

        // إنشاء التوكن باستخدام Laravel Passport
        $token = $user->createToken('authToken')->accessToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    // App\Http\Controllers\AuthController.php

public function profile(Request $request)
{
    return response()->json([
        'message' => 'معلومات المستخدم',
        'user' => $request->user(),
    ]);
}


public function logout(Request $request)
{
    // حذف جميع التوكنات الخاصة بالمستخدم
    $request->user()->tokens()->delete();

    return response()->json([
        'message' => 'تم تسجيل الخروج بنجاح'
    ]);
}

}
    

