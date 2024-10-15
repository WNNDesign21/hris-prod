<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
     * @override attemptLogin on AuthenticatesUsers trait
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     * @override
     */
    protected function attemptLogin(Request $request)
    {
        $user =  $this->guard()->attempt(
            $this->credentials($request), $request->boolean('remember')
        );

        if ($user) {
            $authenticatedUser = $this->guard()->user();
            if ($authenticatedUser->hasAnyRole(['admin','personalia'])) {
                return true;
            } else {
                if ($authenticatedUser->karyawan->status_karyawan === 'AT') {
                    return true;
                } else {
                    $this->guard()->logout();
                    return false;
                }
            }
        }

        return false;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
