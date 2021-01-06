<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('guest:admin')->except('logout');
    }

    public function showLoginForm()
    {
        if ( !empty(auth()->guard('admin')->id()) ) {
            return redirect("admin");
        }
        return view('admin.auth.login');
    }

    function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|min:6',
        ]);
      
        $input = $request->all();

        if (Auth::guard('admin')->attempt(['email' => $input['email'], 'password' => $input['password']])) {
            // Authentication passed...
            return redirect("admin");
        }
        else {
            $request->session()->flash('error', 'Invalid Login Information');
            return redirect()->to(url('/admin/login'))->withInput($request->all());
        }
    }

    protected function credentials(Request $request)
    {
        return array_merge($request->only($this->username(), 'password'), ['active' => '1']);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect('admin/login');
    }

}
