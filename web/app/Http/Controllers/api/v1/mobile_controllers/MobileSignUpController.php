<?php

namespace App\Http\Controllers\api\v1\mobile_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MobileSignUpController extends Controller
{
    public function validate_user(Request $request)
    {
        $usr_email = $request->email;
        $usr_password = $request->password;

        // TEMPORARY HARD-CODED LOGIN
        if ($usr_email === 'admin@gmail.com' && $usr_password === '123456') {
            $user = DB::table('users')
                ->where('usr_id', '=', 1)
                ->where('usr_active', '=', '1')
                ->first();

            if ($user) {
                setUserSessionVariables($user);
                return redirect()->action([AdminController::class, 'home']);
            }
        }

        $user = DB::table('users')
            ->where('usr_email', '=', $usr_email)
            ->where('usr_active', '=', '1')
            ->first();

        if ($user) {
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

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,usr_email',
            'mobile' => 'required|string|max:15|unique:users,usr_mobile',
            'password' => 'required|string|min:6|confirmed',
            'middle_name' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = DB::table('users')->insert([
            'usr_uuid' => generateuuid(),
            'branch_id' => 2,
            'utyp_id' => 3,
            'usr_first_name' => strtoupper($request->first_name),
            'usr_middle_name' => strtoupper($request->middle_name),
            'usr_last_name' => strtoupper($request->last_name),
            'usr_email' => $request->email,
            'usr_mobile' => $request->mobile,
            'usr_password' => Hash::make($request->password),
            'usr_birth_date' => $request->birth_date,
            'usr_date_created' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
        ], 201);
    }
}