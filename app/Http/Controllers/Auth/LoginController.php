<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
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

    protected $redirectTo = '/';
    protected function redirectTo()
    {
        if (auth()->user()->role->name === "Owner" || auth()->user()->role->name === "Super Admin"  || auth()->user()->role->name === "Admin") {
            return route('admin/dashboard');
        }elseif(auth()->user()->role->name ==="Accountant"){
            // return route('admin/employeeReport');
            return route('accountant/employeeReport');

           
        }
        
        else {
            if(auth()->user()->role->name !=="Accountant"){
                abort(403);
            }else{
                return route('accountant/employeeReport');
            }
           
            // return route('admin/card');
            // if (auth()->user()->role->name ==="Accountant") {
                
            //     var_dump(auth()->user()->role->name);
            // }
        }
        // var_dump(auth()->user()->role->name);
       

    }

    protected $username;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');

        $this->username = $this->findUsername();
    }

    public function findUsername()
    {
        $login = request()->input('login');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        request()->merge([$fieldType => $login]);

        return $fieldType;
    }

    public function username()
    {
        return $this->username;
    }

    public function adminLogin()
    {
        return view('auth.login');
    }
    public function accountantLogin()
    {
        return view('auth.accountant');
    }

    protected function attemptLogin()
    {
        if (auth()->guard()->attempt([$this->findUsername() => request()->input('login'), 'password' => request()->password])) {
            return $this->guard()->attempt($this->credentials(request()));
        }
    }

    protected function sendLoginResponse()
    {
        $previous_session = session()->getHandler()->read($this->guard()->user()->session_id);
        if ($previous_session) {
            session()->getHandler()->destroy($previous_session);
        }

        request()->session()->regenerate();

        $this->guard()->user()->session_id = session()->getId();
        $this->guard()->user()->save();
        $this->clearLoginAttempts(request());

        return $this->authenticated(request(), $this->guard()->user()) ?: redirect()->intended($this->redirectPath());
    }
}
