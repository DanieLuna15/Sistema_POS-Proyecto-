<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

//Para sweet alert en Categorias
use RealRashid\SweetAlert\Facades\Alert;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function redirectTo() {
        Alert::toast('Bienvenido(a) al sistema.', 'info');
        return route('home');
    }

    /**
     * Get the needed authorization credentials from the request.
     * Solo permite iniciar sesión a usuarios con estado HABILITADO.
     */
    protected function credentials(Request $request)
    {
        return array_merge($request->only($this->username(), 'password'), ['status' => 'HABILITADO']);
    }

    /**
     * Respuesta personalizada en caso de fallo de autenticación o cuenta inactiva.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $user = \App\User::where($this->username(), $request->{$this->username()})->first();

        if ($user && $user->status !== 'HABILITADO') {
            throw ValidationException::withMessages([
                $this->username() => ['Su cuenta de usuario se encuentra DESHABILITADA. Comuníquese con el administrador.'],
            ]);
        }

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

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
