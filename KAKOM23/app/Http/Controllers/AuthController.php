<?php

namespace App\Http\Controllers;

use App\Models\College;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'access_code' => ['required', 'string'],
        ]);

        $college = College::where('access_code', trim($data['access_code']))->first();

        if (! $college) {
            return back()
                ->withErrors(['access_code' => 'Kod akses tidak sah.'])
                ->withInput();
        }

        $request->session()->put('college_id', $college->id);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('college_id');
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
