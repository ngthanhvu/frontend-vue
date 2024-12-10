<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Password;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Đăng ký người dùng
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token'), 201);
    }

    // Đăng nhập người dùng
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        // Sử dụng guard đặc biệt cho username
        if (!$token = JWTAuth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']])) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json(compact('token'));
    }

    // Yêu cầu đặt lại mật khẩu
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();
        $token = Password::createToken($user);
        $user->notify(new ResetPasswordNotification($token));

        return response()->json(['message' => 'Liên kết đặt lại mật khẩu đã được gửi!'], 200);
    }

    // Đặt lại mật khẩu
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:1|confirmed',
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['message' => __($status)], 500);
    }

    public function getUser(Request $request)
    {
        $user = Auth::user();
        return response()->json($user);
    }

    public function updateInfo(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            // Validate input data
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:users,email,' . $user->id,
                'username' => 'required|string|max:255|unique:users,username,' . $user->id
            ]);

            // Check validation
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update user information
            $user->fill($request->only(['email', 'username']));
            $user->save();

            return response()->json([
                'message' => 'Thông tin đã được cập nhật thành công',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi cập nhật thông tin',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            // Validate input data
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'new_password' => 'required|string|min:8|different:current_password|confirmed'
            ], [
                'new_password.different' => 'Mật khẩu mới phải khác mật khẩu hiện tại',
                'new_password.confirmed' => 'Xác nhận mật khẩu không khớp'
            ]);

            // Check validation
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Lỗi xác thực',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Mật khẩu hiện tại không chính xác'
                ], 400);
            }

            // Update password
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'message' => 'Mật khẩu đã được cập nhật thành công'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi cập nhật mật khẩu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
