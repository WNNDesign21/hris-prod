<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Validation\ValidationException;

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

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        $login = request()->input('username');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$field => $login]);
        return $field;
    }

    
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'username' => [trans('auth.failed')],
        ]);
    }
    
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
            if ($authenticatedUser->hasAnyRole(['admin','personalia','security'])) {
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
}
