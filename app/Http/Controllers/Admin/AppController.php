<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class AppController extends Controller
{
    public function index()
    {
    	return view('admin.admin');
    }

    public function showLogin()
    {
    	return view('admin.login');
    }

    public function attemptLogin(Request $request)
    {
    	$this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:6'
        ]);

        if (Auth::guard('admin')->attempt([
        	'email' => $request->email, 'password' => $request->password
        ], $request->get('remember'))) {
            return redirect()->intended(route('admin.index'));
        }

        return back()->withInput($request->only('email', 'remember'));
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        return redirect()->route('admin.index');
    }
}
