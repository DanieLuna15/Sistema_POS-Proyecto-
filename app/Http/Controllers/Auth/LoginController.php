<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

//Para sweet alert en Categorias
use RealRashid\SweetAlert\Facades\Alert;

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
    //protected $redirectTo = RouteServiceProvider::HOME;

    public function redirectTo() {
        Alert::toast('Bienvenido(a) al sistema.', 'info');
        return route('home');
    }

    /*public function redirectTo() {
        if (auth()->user()->where('status','HABILITADO')) {
            Alert::toast('Bienvenido(a) al sistema.', 'info');
            return route('home');
        } else {
            Alert::toast('Su usuario esta deshabilitado.', 'warning');
            return route('welcome');
        }
    }*/

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
