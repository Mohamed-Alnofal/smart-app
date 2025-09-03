<?php

namespace App\Http\Controllers;

use App\Models\role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

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

//     // role_id للطالب فقط
//     $studentRoleId = DB::table('roles')->where('name', 'student')->value('id');

//     $user = User::create([
//         'email' => $request->email,
//         'password' => Hash::make($request->password),
//         'role_id' => $studentRoleId,
//     ]);

//     $token = $user->createToken('auth_token')->accessToken;

//     return response()->json([
//         'message' => 'تم إنشاء الحساب بنجاح.',
//         'user_id' => $user->id,
//         'token' => $token,
//     ], 201);
// }

// public function completeProfile(Request $request)
// {
//     // التحقق من صحة البيانات المدخلة
//     $validator = Validator::make($request->all(), [
//         'first_name' => 'required|string|max:255',
//         'last_name' => 'required|string|max:255',
//         'phone_number' => 'required|digits_between:8,15',
//         'gender' => 'required|in:male,female',
//         'age' => 'required|integer|min:10|max:100',
//     ]);

//     // إذا كان هناك أخطاء في التحقق
//     if ($validator->fails()) {
//         return response()->json([
//             'errors' => $validator->errors()
//         ], 422);
//     }

//     // الحصول على المستخدم المصادق عليه من التوكن
//     $user = $request->user();

//     // تحديث بيانات المستخدم
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

// public function signUp(Request $request)
// {
//     $validator = Validator::make($request->all(), [
//         // بيانات الحساب
//         'email' => 'required|email|unique:users',
//         'password' => 'required|string|min:6|confirmed',

//         // بيانات الملف الشخصي
//         'first_name' => 'required|string|max:255',
//         'last_name' => 'required|string|max:255',
//         'phone_number' => 'required|digits_between:8,15',
//         'gender' => 'required|in:male,female',
//         'birthday' => 'required|date|before:'.Carbon::now()->subYears(10)->format('Y-m-d').'|after:'.Carbon::now()->subYears(100)->format('Y-m-d'),
//     ]);

//     if ($validator->fails()) {
//         return response()->json([
//             'errors' => $validator->errors()
//         ], 422);
//     }

//     // الحصول على role_id للطالب
//     $studentRoleId = DB::table('roles')->where('name', 'student')->value('id');

//     // إنشاء المستخدم مباشرة بكامل بياناته
//     $user = User::create([
//         'email' => $request->email,
//         'password' => Hash::make($request->password),
//         'role_id' => $studentRoleId,
//         'first_name' => $request->first_name,
//         'last_name' => $request->last_name,
//         'phone_number' => $request->phone_number,
//         'gender' => $request->gender,
//         'birthday' => $request->birthday,
//     ]);

//     // إنشاء التوكن
//     $token = $user->createToken('auth_token')->accessToken;

//     return response()->json([
//         'message' => 'تم إنشاء الحساب واستكمال البيانات الشخصية بنجاح.',
//         'user_id' => $user->id,
//         'user' => $user,
//         'token' => $token
//     ], 201);
// }

public function signUp(Request $request)
{
    // التحقق من صحة البيانات
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6|confirmed',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'phone_number' => 'required|digits_between:8,15',
        'gender' => 'required|in:male,female',
        'birthday' => 'required|date',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // إنشاء المستخدم
    $studentRoleId = DB::table('roles')->where('name', 'student')->value('id');
    $user = User::create([
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role_id' => $studentRoleId,
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'phone_number' => $request->phone_number,
        'gender' => $request->gender,
        'birthday' => $request->birthday,
        'active' => false, // الحساب غير مفعل
    ]);

    // إنشاء كود التفعيل (6 أرقام مثلاً)
    $verificationCode = rand(100000, 999999);

    // تخزين الكود في جدول users أو جدول منفصل (يفضل جدول verification_codes)
    $user->verification_code = $verificationCode;
    $user->save();

    // إرسال الكود على الإيميل
    Mail::raw("رمز التفعيل الخاص بك هو: $verificationCode", function ($message) use ($user) {
        $message->to($user->email);
        $message->subject('تفعيل حسابك');
    });

    return response()->json([
        'message' => 'تم إنشاء الحساب، تحقق من بريدك الإلكتروني لإدخال رمز التفعيل.',
        'user_id' => $user->id
    ], 201);
}

public function verifyEmail(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:users,email',
        'verification_code' => 'required|digits:6',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $user = User::where('email', $request->email)
                ->where('verification_code', $request->verification_code)
                ->first();

    if (!$user) {
        return response()->json(['message' => 'كود التحقق غير صحيح.'], 400);
    }

    $user->update([
        'active' => true,
        'verification_code' => null
    ]);

       // إنشاء التوكن باستخدام Laravel Passport
        $token = $user->createToken('authToken')->accessToken;

    return response()->json([
        'message' => 'تم تفعيل الحساب بنجاح.',
        'user' => $user,
        'token' => $token
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

        if (!$user->active) {
            
            return response()->json(['message' => 'يرجى تفعيل حسابك عبر البريد الإلكتروني'], 403);
        }

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
        $user = $request->user();

    // إضافة رابط مباشر للصورة
    $user->image_url = $user->image 
        ? asset('storage/' . $user->image) 
        : null;
    return response()->json([
        'message' => 'معلومات المستخدم',
        'user' => $request->user(),
    ]);
}


public function updateProfile(Request $request)
{
    $user = $request->user();

    $validated = $request->validate([
        'first_name' => 'sometimes|required|string|max:255',
        'last_name' => 'sometimes|required|string|max:255',
        'phone_number' => 'sometimes|required|digits_between:8,15',
        'gender' => 'sometimes|required|in:male,female',
        'birthday' => [
            'sometimes',
            'required',
            'date',
            function ($attribute, $value, $fail) {
                $age = Carbon::parse($value)->age;
                if ($age < 10 || $age > 100) {
                    $fail('العمر يجب أن يكون بين 10 و 100 سنة.');
                }
            }
        ],
        'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
        'password' => 'sometimes|required|string|min:6|confirmed',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    // معالجة كلمة المرور إذا تم تمريرها
    if ($request->filled('password')) {
        $validated['password'] = Hash::make($request->password);
    }

    // معالجة الصورة إذا تم رفع واحدة جديدة
    if ($request->hasFile('image')) {
        // حذف الصورة القديمة إن وجدت
        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }

        // تخزين الصورة الجديدة
        $validated['image'] = $request->file('image')->store('profiles', 'public');
    }

    $user->update($validated);

    // إضافة رابط مباشر للصورة
    $user->image_url = $user->image 
        ? asset('storage/' . $user->image) 
        : null;

    return response()->json([
        'message' => 'تم تحديث الملف الشخصي بنجاح',
        'user' => $user
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
    

