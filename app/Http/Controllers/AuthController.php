<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function admin()
    {
        return view('auth.login', [
            'type' => User::ROLE_ADMIN,
            'title' => 'Painel Administrativo',
            'subtitle' => 'Monitore motoristas, pedidos e entregas em tempo real.',
            'demoEmail' => 'admin@logmanager.test',
        ]);
    }

    public function driver()
    {
        return view('auth.login', [
            'type' => User::ROLE_DRIVER,
            'title' => 'Área do Motorista',
            'subtitle' => 'Acompanhe seus pedidos e entregas com clareza.',
            'demoEmail' => 'joao.motorista@logmanager.test',
        ]);
    }

    public function authenticate(Request $request, $type)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! in_array($type, [User::ROLE_ADMIN, User::ROLE_DRIVER])) {
            abort(404);
        }

        if (! Auth::attempt($credentials, $request->filled('remember'))) {
            return back()
                ->withErrors(['email' => 'E-mail ou senha inválidos.'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        if (Auth::user()->role !== $type) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['email' => 'Este usuário não tem acesso a esta área.'])
                ->withInput($request->only('email'));
        }

        return redirect()->route($type === User::ROLE_ADMIN ? 'admin.drivers.index' : 'driver.orders.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.admin');
    }
}
