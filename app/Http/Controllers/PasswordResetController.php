<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
class PasswordResetController extends Controller
{
  // 1. إرسال الكود إلى الإيميل
    public function requestCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $code = random_int(1000, 9999);

        DB::table('password_reset_codes')->updateOrInsert(
            ['email' => $request->email],
            [
                'code' => $code,
                'expires_at' => now()->addMinutes(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // إرسال الكود بالإيميل (Mailtrap مثلًا)
        Mail::raw("رمز إعادة تعيين كلمة المرور هو: $code", function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('رمز إعادة تعيين كلمة المرور');
        });

        return response()->json(['message' => 'تم إرسال رمز إعادة تعيين كلمة المرور.']);
    }

    public function verifyAccount(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'code' => 'required|digits:4',
    ]);

    $record = DB::table('password_reset_codes')
        ->where('email', $request->email)
        ->where('code', $request->code)
        ->where('expires_at', '>', Carbon::now())
        ->first();

    if (!$record) {
        return response()->json(['message' => 'رمز غير صالح أو منتهي الصلاحية.'], 422);
    }

    DB::table('password_reset_codes')->where('email', $request->email)->delete();

    return response()->json(['message' => 'تم التحقق من البريد الإلكتروني بنجاح.']);
}

    // // 2. التحقق من الكود وإعادة تعيين كلمة المرور
    // public function resetWithCode(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'code' => 'required|digits:4',
    //         'password' => 'required|string|min:6|confirmed',
    //     ]);

    //     $record = DB::table('password_reset_codes')
    //         ->where('email', $request->email)
    //         ->where('code', $request->code)
    //         ->where('expires_at', '>', Carbon::now())
    //         ->first();

    //     if (!$record) {
    //         return response()->json(['message' => 'رمز غير صالح أو منتهي الصلاحية.'], 422);
    //     }

    //     $user = User::where('email', $request->email)->first();
    //     $user->update([
    //         'password' => Hash::make($request->password),
    //     ]);

    //     DB::table('password_reset_codes')->where('email', $request->email)->delete();

    //     return response()->json(['message' => 'تم تغيير كلمة المرور بنجاح.']);
    // }
}
