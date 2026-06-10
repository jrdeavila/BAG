<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function username()
    {
        return 'usuario';
    }

    protected function credentials(Request $request)
    {
        return [
            'correo' => $request->input('usuario'),
            'password' => $request->input('password'),
        ];
    }

    /**
     * Tras autenticar, verifica que el funcionario tenga acceso a la plataforma
     * (area habilitada, funcionario especial o superadmin). Si no, cierra sesion.
     */
    protected function authenticated(Request $request, $user)
    {
        if (! $user->hasPlatformAccess()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'usuario' => 'Tu area no esta habilitada para usar la plataforma. Contacta al administrador.',
            ]);
        }
    }
}
