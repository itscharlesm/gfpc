<?php

namespace App\Http\Controllers\api\v1\mobile_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MobileAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = DB::table('users')
            ->where('usr_email', $request->email)
            ->where('usr_active', 1)
            ->first();

        if (!$user || !Hash::check($request->password, $user->usr_password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email or password is incorrect.',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'user' => [
                'usr_id' => $user->usr_id,
                'usr_email' => $user->usr_email,
                'usr_first_name' => $user->usr_first_name,
                'usr_last_name' => $user->usr_last_name,
                'utyp_id' => (int) $user->utyp_id,
            ],
        ], 200);
    }
}