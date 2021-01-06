<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\WagEnabledHelpers;
use App\Mail\UerPasswordResetMail;
use App\Models\PasswordReset;
use App\Models\User;
use App\Notifications\ResetPassword;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Response;
use Validator;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->statusCodes = config("wagenabled.status_codes");
    }

    public function forgotPassword(Request $request) {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return WagEnabledHelpers::apiValidationFailResponse($validator);
        }
        $input = $request->all();

        $user = User::where("email", "=", $input['email'])->withTrashed()->first();                
        if ($user) {
            if( $user->deleted_at == null  ) {
                $randomStringTemplate = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $timestamp = Carbon::createFromFormat('U.u', microtime(true))->format("YmdHisu");
                $randomString = substr(str_shuffle(str_repeat($randomStringTemplate, 5)), 0, 15);
                $randomString2 = substr(str_shuffle(str_repeat($randomStringTemplate, 5)), 0, 15);
                $randomString3 = substr(str_shuffle(str_repeat($randomStringTemplate, 5)), 0, 15);

                $token = $timestamp . $randomString . $randomString2 . $randomString3;        

                $passwordReset = PasswordReset::updateOrCreate(
                                    ['email' => $user->email],
                                    [
                                        'email' => $user->email,
                                        'token' => $token
                                    ]
                                );
                if ($passwordReset) {    
                    Mail::to($user->email)->send(new UerPasswordResetMail($user, $token));
                    $data = array(
                        'message' => "Reset password link sent to your email, please check it",
                    );               
                    return Response::json($data, $this->statusCodes['success']);
                }
                else {
                    $data = ['message' => "Please try again"];
                }
            }
            else {
                $validator->getMessageBag()->add('email', 'Please contact wag enabled support'); 
                return WagEnabledHelpers::apiValidationFailResponse($validator);  
            }
        }
        else {
            $validator->getMessageBag()->add('email', 'Please enter correct email address'); 
            return WagEnabledHelpers::apiValidationFailResponse($validator);   
        }
        
        return Response::json($data, $this->statusCodes['form_validation']);
    }

}
