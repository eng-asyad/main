<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Mail\VerificationMail;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate the request...
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'mobile' => ['required','unique:users,mobile','digits:10'],
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'mobile' => $validatedData['mobile'],
            'password' => Hash::make($validatedData['password']),
            'is_admin' =>false ,
           // 'is_admin' => $validatedData['is_admin']
        ]);

        // Assuming you're using Laravel Sanctum for API token authentication
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'     
         ]);       
    }


    public function login(Request $request)
    {
        // Validate the request...
        $validatedData = $request->validate([          
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if (!Auth::attempt($validatedData)) {
            return response()->json(['message' =>
             'Invalid login details'], 401);
        }

        $user = Auth::user();
        $code = rand(10000, 99999);

        // Store the code in session or database as per your logic
        session(['verification_code' => $code]);

        // Send verification email
        Mail::to($request->email)->send(new VerificationMail($code));

        // Assuming you're using Laravel Sanctum for API token authentication
        $user = User::where('email', $validatedData['email'])->firstOrFail();
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
           // 'message' => 'Successfully logged in',
             //'token' => $token
             'user' => $user,
             'access_token' => $token,
             'token_type' => 'Bearer'             
            ]);
    }
    public function verifyCode(Request $request)
    {
        $code = $request->input('code');

        if ($code == session('verification_code')) {
            // Verification successful
            return response()->json(['message' => 'Verification successful.'], 200);
        }

        return response()->json(['message' => 'Invalid verification code.'], 401);
    }
   
   
    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->noContent(200);
    }
    
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)])
            : response()->json(['email' => __($status)], 422);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)])
            : response()->json(['email' => [__($status)]], 422);
    }







}
