<?php

namespace App\Http\Controllers\api\v1\mobile_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobileAddressController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'add_id' => 'required|integer',
            'uadd_street' => 'required|string',
            'uadd_barangay' => 'nullable|string',
            'uadd_city' => 'required|string',
            'uadd_province' => 'nullable|string',
            'uadd_region' => 'nullable|string',
            'uadd_longitude' => 'nullable|numeric',
            'uadd_latitude' => 'nullable|numeric',
        ]);

        $user = DB::table('users')
            ->where('usr_email', $request->email)
            ->where('usr_active', 1)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $hasExistingAddress = DB::table('user_addresses')
        ->where('usr_id', $user->usr_id)
        ->exists();

        DB::table('user_addresses')->insert([
            'usr_id' => $user->usr_id,
            'add_id' => $request->add_id,
            'uadd_street' => $request->uadd_street,
            'uadd_barangay' => $request->uadd_barangay,
            'uadd_city' => $request->uadd_city,
            'uadd_province' => $request->uadd_province,
            'uadd_region' => $request->uadd_region,
            'uadd_longitude' => $request->uadd_longitude,
            'uadd_latitude' => $request->uadd_latitude,
            'uadd_date_created' => now(),
            'uadd_created_by' => $user->usr_id,
            'uadd_active' => $hasExistingAddress ? 0 : 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Address saved successfully.',
        ]);
    }

    public function usedTypes(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = DB::table('users')
            ->where('usr_email', $request->email)
            ->where('usr_active', 1)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $usedTypes = DB::table('user_addresses')
            ->where('usr_id', $user->usr_id)
            ->whereIn('uadd_active', [0, 1])
            ->pluck('add_id')
            ->map(fn ($id) => (int) $id)
            ->values();

        return response()->json([
            'success' => true,
            'data' => $usedTypes,
        ]);
    }

    public function list(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = DB::table('users')
            ->where('usr_email', $request->email)
            ->where('usr_active', 1)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
                'data' => [],
            ], 404);
        }

        $addresses = DB::table('user_addresses')
            ->where('usr_id', $user->usr_id)
            ->whereIn('uadd_active', [0, 1])
            ->select(
                'uadd_id',
                'usr_id',
                'add_id',
                'uadd_street',
                'uadd_barangay',
                'uadd_city',
                'uadd_province',
                'uadd_region',
                'uadd_active'
            )
            ->orderBy('uadd_date_created', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $addresses,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'uadd_id' => 'required|integer',
            'uadd_street' => 'required|string',
            'uadd_barangay' => 'required|string',
            'uadd_city' => 'required|string',
            'uadd_province' => 'required|string',
            'uadd_region' => 'required|string',
        ]);

        $user = DB::table('users')
            ->where('usr_email', $request->email)
            ->where('usr_active', 1)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $address = DB::table('user_addresses')
            ->where('uadd_id', $request->uadd_id)
            ->where('usr_id', $user->usr_id)
            ->whereIn('uadd_active', [0, 1])
            ->first();

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found.',
            ], 404);
        }

        DB::table('user_addresses')
            ->where('uadd_id', $request->uadd_id)
            ->where('usr_id', $user->usr_id)
            ->update([
                'uadd_street' => $request->uadd_street,
                'uadd_barangay' => $request->uadd_barangay,
                'uadd_city' => $request->uadd_city,
                'uadd_province' => $request->uadd_province,
                'uadd_region' => $request->uadd_region,
                'uadd_date_modified' => now(),
                'uadd_modified_by' => $user->usr_id,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully.',
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'uadd_id' => 'required|integer',
        ]);

        $user = DB::table('users')
            ->where('usr_email', $request->email)
            ->where('usr_active', 1)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $address = DB::table('user_addresses')
            ->where('uadd_id', $request->uadd_id)
            ->where('usr_id', $user->usr_id)
            ->first();

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found.',
            ], 404);
        }

        if ($address->uadd_active == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Primary address cannot be deleted. Please set another address as primary first.',
            ], 400);
        }

        DB::table('user_addresses')
            ->where('uadd_id', $request->uadd_id)
            ->where('usr_id', $user->usr_id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully.',
        ]);
    }

    public function setPrimary(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'uadd_id' => 'required|integer',
        ]);

        $user = DB::table('users')
            ->where('usr_email', $request->email)
            ->where('usr_active', 1)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $address = DB::table('user_addresses')
            ->where('uadd_id', $request->uadd_id)
            ->where('usr_id', $user->usr_id)
            ->first();

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found.',
            ], 404);
        }

        DB::table('user_addresses')
            ->where('usr_id', $user->usr_id)
            ->update([
                'uadd_active' => 0,
            ]);

        DB::table('user_addresses')
            ->where('uadd_id', $request->uadd_id)
            ->where('usr_id', $user->usr_id)
            ->update([
                'uadd_active' => 1,
                'uadd_date_modified' => now(),
                'uadd_modified_by' => $user->usr_id,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Primary address updated.',
        ]);
    }
}