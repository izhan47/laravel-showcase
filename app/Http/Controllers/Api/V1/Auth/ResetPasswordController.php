<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\WagEnabledHelpers;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Validator;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('api.auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function resetPassword(Request $request)
    {        
        $statusCodes = config("wagenabled.status_codes");
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'password' => 'required|confirmed|min:6'
        ]);

        if ($validator->fails()) {
            return WagEnabledHelpers::apiValidationFailResponse($validator);
        }

        $input = $request->all();
        $code = $statusCodes['normal_error'];

        $passwordReset = PasswordReset::where([
                            ['token', $request->token],                         
                        ])->first();

        if ($passwordReset) {
            if (Carbon::parse($passwordReset->updated_at)->addMinutes(300)->isPast()) {
                $passwordReset->delete();
                $message = 'This password reset link is expired, Please try agin';
            }
            else {
                $user = User::where('email', $passwordReset->email)->first();

                if ($user) {
                    $user->password = bcrypt($input['password']);
                    $user->save();
                    
                    PasswordReset::where('email', $user->email)->delete();

                    $code = $statusCodes['success'];
                    $message = "Password reset successfully!";
                }
                else {
                    $message = "Please try again";
                }
            }
        }
        else {
            $message = "Invalid user for reset password";
        }

        return WagEnabledHelpers::apiJsonResponse($responseData = [], $code, $message);
    }

    protected function sendResetResponse($response)
    {
        \Auth::logout();
        return redirect("thank-you");
    }
}
