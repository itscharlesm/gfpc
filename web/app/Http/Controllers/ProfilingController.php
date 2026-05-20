<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class ProfilingController extends Controller
{
    // START USERS
    public function users_active(Request $request)
    {
        $search = $request->search ?? '';

        $sessionBranchId = session('branch_id');

        // Base query
        $query = DB::table('users')
            ->leftJoin('user_roles', 'users.usr_id', '=', 'user_roles.usr_id')
            ->leftJoin('roles', 'user_roles.rol_id', '=', 'roles.rol_id')
            ->leftJoin('branches', 'users.branch_id', '=', 'branches.branch_id')
            ->where('users.utyp_id', '=', '1')
            ->where('users.usr_active', '=', '1');

        // Branch filter (unless super admin)
        if ($sessionBranchId != 1) {
            $query->where('users.branch_id', $sessionBranchId);
        }

        $query->select(
            'users.usr_id',
            'users.usr_uuid',
            'branches.branch_name',
            'users.usr_last_name',
            'users.usr_first_name',
            'users.usr_middle_name',
            'users.usr_email',
            'users.usr_mobile',
            'users.usr_active',
            DB::raw('GROUP_CONCAT(CASE WHEN user_roles.url_active = 1 THEN roles.rol_name END ORDER BY roles.rol_name SEPARATOR ", ") as roles')
        )
            ->groupBy(
                'users.usr_id',
                'users.usr_uuid',
                'branches.branch_name',
                'users.usr_last_name',
                'users.usr_first_name',
                'users.usr_middle_name',
                'users.usr_email',
                'users.usr_mobile',
                'users.usr_active'
            )
            ->orderBy('users.usr_last_name')
            ->orderBy('users.usr_first_name');

        // Search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('users.usr_last_name', 'LIKE', "%$search%")
                    ->orWhere('users.usr_first_name', 'LIKE', "%$search%")
                    ->orWhere('users.usr_email', 'LIKE', "%$search%")
                    ->orWhere('users.usr_mobile', 'LIKE', "%$search%")
                    ->orWhere('branches.branch_name', 'LIKE', "%$search%");
            });
        }

        $users = $query->paginate(500);

        $roles = DB::table('roles')
            ->select('rol_id', 'rol_name', 'rol_description')
            ->where('rol_active', 1)
            ->where('rol_id', '!=', 1)
            ->get();

        $regions = DB::table('location_regions')
            ->where('reg_active', 1)
            ->orderBy('reg_name')
            ->get(['reg_id', 'reg_name']);

        $branches = DB::table('branches')
            ->select('branch_id', 'branch_name')
            ->where('branch_active', 1)
            ->get();

        return view('profiling.users.active', compact('users', 'search', 'roles', 'regions', 'branches'));
    }

    public function users_deleted(Request $request)
    {
        $search = $request->search ?? '';

        $sessionBranchId = session('branch_id');

        // Base query
        $query = DB::table('users')
            ->leftJoin('user_roles', 'users.usr_id', '=', 'user_roles.usr_id')
            ->leftJoin('roles', 'user_roles.rol_id', '=', 'roles.rol_id')
            ->leftJoin('branches', 'users.branch_id', '=', 'branches.branch_id')
            ->where('users.utyp_id', '=', '1')
            ->where('users.usr_active', '=', '0');

        // Branch filter (unless super admin)
        if ($sessionBranchId && $sessionBranchId != 1) {
            $query->where('users.branch_id', $sessionBranchId);
        }

        $query->select(
            'users.usr_id',
            'users.usr_uuid',
            'branches.branch_name',
            'users.usr_last_name',
            'users.usr_first_name',
            'users.usr_middle_name',
            'users.usr_email',
            'users.usr_mobile',
            'users.usr_active',
            DB::raw('GROUP_CONCAT(CASE WHEN user_roles.url_active = 1 THEN roles.rol_name END ORDER BY roles.rol_name SEPARATOR ", ") as roles')
        )
            ->groupBy(
                'users.usr_id',
                'users.usr_uuid',
                'branches.branch_name',
                'users.usr_last_name',
                'users.usr_first_name',
                'users.usr_middle_name',
                'users.usr_email',
                'users.usr_mobile',
                'users.usr_active'
            )
            ->orderBy('users.usr_last_name')
            ->orderBy('users.usr_first_name');

        // Search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('users.usr_last_name', 'LIKE', "%$search%")
                    ->orWhere('users.usr_first_name', 'LIKE', "%$search%")
                    ->orWhere('users.usr_email', 'LIKE', "%$search%")
                    ->orWhere('users.usr_mobile', 'LIKE', "%$search%")
                    ->orWhere('branches.branch_name', 'LIKE', "%$search%");
            });
        }

        $users = $query->paginate(500);

        $roles = DB::table('roles')
            ->select('rol_id', 'rol_name', 'rol_description')
            ->where('rol_active', 1)
            ->where('rol_id', '!=', 1)
            ->get();

        $regions = DB::table('location_regions')
            ->where('reg_active', 1)
            ->orderBy('reg_name')
            ->get(['reg_id', 'reg_name']);

        $branches = DB::table('branches')
            ->select('branch_id', 'branch_name')
            ->where('branch_active', 1)
            ->get();

        return view('profiling.users.deleted', compact('users', 'search', 'roles', 'regions', 'branches'));
    }

    public function users_add(Request $request)
    {
        $request->validate([
            'usr_first_name' => 'required|string|max:255',
            'usr_middle_name' => 'nullable|string|max:255',
            'usr_last_name' => 'required|string|max:255',
            'usr_email' => 'required|email|max:255|unique:users,usr_email',
            'usr_mobile' => 'nullable|string|max:20',
            'branch_id' => 'required|string|max:255',

            // address validation
            'street' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'municipality' => 'nullable|string|max:100',
            'barangay' => 'nullable|string|max:100',
        ]);

        $code = '123456';

        DB::beginTransaction();

        // Insert user
        $usr_id = DB::table('users')->insertGetId([
            'usr_uuid' => generateuuid(),
            'utyp_id' => 1,
            'usr_first_name' => strtoupper($request->usr_first_name),
            'usr_middle_name' => $request->usr_middle_name ? strtoupper($request->usr_middle_name) : null,
            'usr_last_name' => strtoupper($request->usr_last_name),
            'usr_email' => $request->usr_email,
            'usr_mobile' => $request->usr_mobile ?: null,
            'branch_id' => $request->branch_id,
            'usr_password' => md5($code),
            'usr_code' => $code,
            'usr_date_created' => Carbon::now(),
            'usr_created_by' => session('usr_id'),
            'usr_active' => 1
        ]);

        // Insert address (if any field is filled)
        if ($request->street || $request->barangay || $request->municipality || $request->province || $request->region) {
            DB::table('user_addresses')->insert([
                'uadd_uuid' => generateuuid(),
                'usr_id' => $usr_id,
                'add_id' => 1,
                'uadd_street' => $request->street,
                'uadd_barangay' => $request->barangay,
                'uadd_city' => $request->municipality,
                'uadd_province' => $request->province,
                'uadd_region' => $request->region,
                'uadd_active' => 1,
            ]);
        }

        DB::commit();

        return back()->with('successMessage', 'The user has been created and the password has been set to 123456.');
    }

    public function users_update_role(Request $request, $usr_id)
    {
        $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,rol_id',
        ]);

        $selectedRoles = $request->roles ?? [];

        // Get the user's first and last name
        $user = DB::table('users')
            ->where('usr_id', $usr_id)
            ->select('usr_first_name', 'usr_last_name')
            ->first();

        if (!$user) {
            alert()->error('Error', 'User not found.');
            return redirect()->back();
        }

        // Get all current roles of the user
        $existingRoles = DB::table('user_roles')
            ->where('usr_id', $usr_id)
            ->pluck('rol_id')
            ->toArray();

        // Fetch old active roles (before update)
        $oldRoles = DB::table('roles')
            ->whereIn('rol_id', DB::table('user_roles')
                ->where('usr_id', $usr_id)
                ->where('url_active', 1)
                ->pluck('rol_id'))
            ->pluck('rol_name')
            ->toArray();

        // Step 1: Update or deactivate existing roles
        foreach ($existingRoles as $rol_id) {
            $isActive = in_array($rol_id, $selectedRoles) ? 1 : 0;

            DB::table('user_roles')
                ->where('usr_id', $usr_id)
                ->where('rol_id', $rol_id)
                ->update(['url_active' => $isActive]);
        }

        // Step 2: Insert new roles (those in selectedRoles but not in existingRoles)
        $newRolesToInsert = array_diff($selectedRoles, $existingRoles);
        foreach ($newRolesToInsert as $rol_id) {
            DB::table('user_roles')->insert([
                'usr_id' => $usr_id,
                'rol_id' => $rol_id,
                'url_active' => 1,
            ]);
        }

        // Fetch new active roles (after update)
        $newRoles = DB::table('roles')
            ->whereIn('rol_id', DB::table('user_roles')
                ->where('usr_id', $usr_id)
                ->where('url_active', 1)
                ->pluck('rol_id'))
            ->pluck('rol_name')
            ->toArray();

        // Convert roles array to comma-separated string
        $oldRolesString = implode(', ', $oldRoles);
        $newRolesString = implode(', ', $newRoles);

        // Log user activity
        logUserActivity(
            'Manage Users',
            'Updated roles for user ' . $user->usr_last_name . ', ' . $user->usr_first_name .
            ' FROM [' . $oldRolesString . '] TO [' . $newRolesString . ']'
        );

        alert()->success('Success', 'User roles updated successfully.');
        return redirect()->back();
    }

    public function users_reset_password(Request $request, $usr_id)
    {
        $user = DB::table('users')
            ->where('usr_id', '=', $usr_id)
            ->first();

        if (!$user) {
            alert()->error('User not found.');
            return redirect()->back();
        }

        DB::table('users')
            ->where('usr_uuid', '=', $usr_id)
            ->update([
                'usr_date_modified' => Carbon::now(),
                'usr_modified_by' => session('usr_id'),
                'usr_password' => md5('123456')
            ]);

        logUserActivity('Manage Users', 'Reset password for user ' . $user->usr_last_name . ', ' . $user->usr_first_name);

        session()->flash('successMessage', 'Password has been reset to 123456.');
        return redirect()->back();
    }

    public function users_delete(Request $request, $usr_id)
    {
        $user = DB::table('users')
            ->where('usr_id', '=', $usr_id)
            ->first();

        if (!$user) {
            alert()->error('User not found.');
            return redirect()->back();
        }

        DB::table('users')
            ->where('usr_id', '=', $usr_id)
            ->update([
                'usr_date_modified' => Carbon::now(),
                'usr_modified_by' => session('usr_id'),
                'usr_active' => 0
            ]);

        logUserActivity('Manage Users', 'Deleted user ' . $user->usr_last_name . ', ' . $user->usr_first_name);

        session()->flash('successMessage', 'User has been deleted.');
        return redirect()->back();
    }

    public function users_restore(Request $request, $usr_id)
    {
        $user = DB::table('users')
            ->where('usr_id', '=', $usr_id)
            ->first();

        if (!$user) {
            alert()->error('User not found.');
            return redirect()->back();
        }

        DB::table('users')
            ->where('usr_id', '=', $usr_id)
            ->update([
                'usr_date_modified' => Carbon::now(),
                'usr_modified_by' => session('usr_id'),
                'usr_active' => 1
            ]);

        logUserActivity('Manage Users', 'Restored user ' . $user->usr_last_name . ', ' . $user->usr_first_name);

        session()->flash('successMessage', 'User has been restored.');
        return redirect()->back();
    }
    // END USERS

    // START TECHINICIANS
    public function technicians_active(Request $request)
    {
        $search = $request->search ?? '';

        $sessionBranchId = session('branch_id');

        // Base query
        $query = DB::table('users')
            ->leftJoin('branches', 'users.branch_id', '=', 'branches.branch_id')
            ->leftJoin('user_availabilities', 'users.usr_id', '=', 'user_availabilities.usr_id')
            ->where('users.utyp_id', '=', '2')
            ->where('users.usr_active', '=', '1');

        // Branch filter (unless super admin)
        if ($sessionBranchId != 1) {
            $query->where('users.branch_id', $sessionBranchId);
        }

        $query->select(
            'users.usr_id',
            'users.usr_uuid',
            'branches.branch_name',
            'users.usr_last_name',
            'users.usr_first_name',
            'users.usr_middle_name',
            'users.usr_email',
            'users.usr_mobile',
            'users.usr_active',
            DB::raw('GROUP_CONCAT(CASE WHEN user_availabilities.uavail_active = 1 THEN user_availabilities.uavail_name END ORDER BY FIELD(user_availabilities.uavail_name, "Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday") SEPARATOR ", ") as availabilities')
        )
            ->groupBy(
                'users.usr_id',
                'users.usr_uuid',
                'branches.branch_name',
                'users.usr_last_name',
                'users.usr_first_name',
                'users.usr_middle_name',
                'users.usr_email',
                'users.usr_mobile',
                'users.usr_active'
            )
            ->orderBy('users.usr_last_name')
            ->orderBy('users.usr_first_name');

        // Search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('users.usr_last_name', 'LIKE', "%$search%")
                    ->orWhere('users.usr_first_name', 'LIKE', "%$search%")
                    ->orWhere('users.usr_email', 'LIKE', "%$search%")
                    ->orWhere('users.usr_mobile', 'LIKE', "%$search%")
                    ->orWhere('branches.branch_name', 'LIKE', "%$search%");
            });
        }

        $technicians = $query->paginate(500);

        $regions = DB::table('location_regions')
            ->where('reg_active', 1)
            ->orderBy('reg_name')
            ->get(['reg_id', 'reg_name']);

        $branches = DB::table('branches')
            ->select('branch_id', 'branch_name')
            ->where('branch_active', 1)
            ->get();

        return view('profiling.technicians.active', compact('technicians', 'search', 'regions', 'branches'));
    }

    public function technicians_deleted(Request $request)
    {
        $search = $request->search ?? '';

        $sessionBranchId = session('branch_id');

        // Base query
        $query = DB::table('users')
            ->leftJoin('branches', 'users.branch_id', '=', 'branches.branch_id')
            ->leftJoin('user_availabilities', 'users.usr_id', '=', 'user_availabilities.usr_id')
            ->where('users.utyp_id', '=', '2')
            ->where('users.usr_active', '=', '0');

        // Branch filter (unless super admin)
        if ($sessionBranchId && $sessionBranchId != 1) {
            $query->where('users.branch_id', $sessionBranchId);
        }

        $query->select(
            'users.usr_id',
            'users.usr_uuid',
            'branches.branch_name',
            'users.usr_last_name',
            'users.usr_first_name',
            'users.usr_middle_name',
            'users.usr_email',
            'users.usr_mobile',
            'users.usr_active',
            DB::raw('GROUP_CONCAT(CASE WHEN user_availabilities.uavail_active = 1 THEN user_availabilities.uavail_name END ORDER BY FIELD(user_availabilities.uavail_name, "Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday") SEPARATOR ", ") as availabilities')
        )
            ->groupBy(
                'users.usr_id',
                'users.usr_uuid',
                'branches.branch_name',
                'users.usr_last_name',
                'users.usr_first_name',
                'users.usr_middle_name',
                'users.usr_email',
                'users.usr_mobile',
                'users.usr_active'
            )
            ->orderBy('users.usr_last_name')
            ->orderBy('users.usr_first_name');

        // Search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('users.usr_last_name', 'LIKE', "%$search%")
                    ->orWhere('users.usr_first_name', 'LIKE', "%$search%")
                    ->orWhere('users.usr_email', 'LIKE', "%$search%")
                    ->orWhere('users.usr_mobile', 'LIKE', "%$search%")
                    ->orWhere('branches.branch_name', 'LIKE', "%$search%");
            });
        }

        $technicians = $query->paginate(500);

        $regions = DB::table('location_regions')
            ->where('reg_active', 1)
            ->orderBy('reg_name')
            ->get(['reg_id', 'reg_name']);

        $branches = DB::table('branches')
            ->select('branch_id', 'branch_name')
            ->where('branch_active', 1)
            ->get();

        return view('profiling.technicians.deleted', compact('technicians', 'search', 'regions', 'branches'));
    }

    public function technicians_add(Request $request)
    {
        $request->validate([
            'usr_first_name' => 'required|string|max:255',
            'usr_middle_name' => 'nullable|string|max:255',
            'usr_last_name' => 'required|string|max:255',
            'usr_email' => 'required|email|max:255|unique:users,usr_email',
            'usr_mobile' => 'nullable|string|max:20',
            'branch_id' => 'required|string|max:255',

            // address validation
            'street' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'municipality' => 'nullable|string|max:100',
            'barangay' => 'nullable|string|max:100',
        ]);

        $code = '123456';

        DB::beginTransaction();

        // Insert user
        $usr_id = DB::table('users')->insertGetId([
            'usr_uuid' => generateuuid(),
            'utyp_id' => 2,
            'usr_first_name' => strtoupper($request->usr_first_name),
            'usr_middle_name' => $request->usr_middle_name ? strtoupper($request->usr_middle_name) : null,
            'usr_last_name' => strtoupper($request->usr_last_name),
            'usr_email' => $request->usr_email,
            'usr_mobile' => $request->usr_mobile ?: null,
            'branch_id' => $request->branch_id,
            'usr_password' => md5($code),
            'usr_code' => $code,
            'usr_date_created' => Carbon::now(),
            'usr_created_by' => session('usr_id'),
            'usr_active' => 1
        ]);

        // Insert address (if any field is filled)
        if ($request->street || $request->barangay || $request->municipality || $request->province || $request->region) {
            DB::table('user_addresses')->insert([
                'usr_id' => $usr_id,
                'add_id' => 1,
                'uadd_street' => $request->street,
                'uadd_barangay' => $request->barangay,
                'uadd_city' => $request->municipality,
                'uadd_province' => $request->province,
                'uadd_region' => $request->region,
                'uadd_active' => 1,
            ]);
        }

        // Insert availability
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        $selected = $request->availability ?? [];

        foreach ($days as $day) {
            DB::table('user_availabilities')->insert([
                'uavail_uuid' => generateuuid(),
                'usr_id' => $usr_id,
                'uavail_name' => $day,
                'uavail_date_created' => Carbon::now(),
                'uavail_created_by' => session('usr_id'),
                'uavail_active' => in_array($day, $selected) ? 1 : 0,
            ]);
        }

        DB::commit();

        return back()->with('successMessage', 'The Technicians has been created and the password has been set to 123456.');
    }

    public function technicians_update_availability(Request $request, $usr_id)
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        $selected = $request->availability ?? [];

        foreach ($days as $day) {

            DB::table('user_availabilities')
                ->where('usr_id', $usr_id)
                ->where('uavail_name', $day)
                ->update([
                    'uavail_date_modified' => Carbon::now(),
                    'uavail_modified_by' => session('usr_id'),
                    'uavail_active' => in_array($day, $selected) ? 1 : 0,
                ]);
        }

        return back()->with('successMessage', 'Availability updated successfully.');
    }

    public function technicians_reset_password(Request $request, $usr_id)
    {
        $technician = DB::table('users')
            ->where('usr_id', '=', $usr_id)
            ->first();

        if (!$technician) {
            alert()->error('Technician not found.');
            return redirect()->back();
        }

        DB::table('users')
            ->where('usr_uuid', '=', $usr_id)
            ->update([
                'usr_date_modified' => Carbon::now(),
                'usr_modified_by' => session('usr_id'),
                'usr_password' => md5('123456')
            ]);

        logUserActivity('Manage Technicians', 'Reset password for technician ' . $technician->usr_last_name . ', ' . $technician->usr_first_name);

        session()->flash('successMessage', 'Password has been reset to 123456.');
        return redirect()->back();
    }

    public function technicians_delete(Request $request, $usr_id)
    {
        $technician = DB::table('users')
            ->where('usr_id', '=', $usr_id)
            ->first();

        if (!$technician) {
            alert()->error('Technician not found.');
            return redirect()->back();
        }

        DB::table('users')
            ->where('usr_id', '=', $usr_id)
            ->update([
                'usr_date_modified' => Carbon::now(),
                'usr_modified_by' => session('usr_id'),
                'usr_active' => 0
            ]);

        logUserActivity('Manage Technicians', 'Deleted technician ' . $technician->usr_last_name . ', ' . $technician->usr_first_name);

        session()->flash('successMessage', 'Technician has been deleted.');
        return redirect()->back();
    }

    public function technicians_restore(Request $request, $usr_id)
    {
        $technician = DB::table('users')
            ->where('usr_id', '=', $usr_id)
            ->first();

        if (!$technician) {
            alert()->error('Technician not found.');
            return redirect()->back();
        }

        DB::table('users')
            ->where('usr_id', '=', $usr_id)
            ->update([
                'usr_active' => 1
            ]);

        logUserActivity('Manage Technicians', 'Restored technician ' . $technician->usr_last_name . ', ' . $technician->usr_first_name);

        session()->flash('successMessage', 'Technician has been restored.');
        return redirect()->back();
    }
    // END TECHINICIANS

    // START CLIENTS
    public function clients_active(Request $request)
    {
        $search = $request->search ?? '';

        $sessionBranchId = session('branch_id');

        // Base query
        $query = DB::table('users')
            ->leftJoin('branches', 'users.branch_id', '=', 'branches.branch_id')
            ->where('users.utyp_id', '=', '3')
            ->where('users.usr_active', '=', '1');

        // Branch filter (unless super admin)
        if ($sessionBranchId != 1) {
            $query->where('users.branch_id', $sessionBranchId);
        }

        $query->select(
            'users.usr_id',
            'users.usr_uuid',
            'branches.branch_name',
            'users.usr_last_name',
            'users.usr_first_name',
            'users.usr_middle_name',
            'users.usr_email',
            'users.usr_mobile',
            'users.usr_birth_date',
            'users.usr_active',
        )
            ->groupBy(
                'users.usr_id',
                'users.usr_uuid',
                'branches.branch_name',
                'users.usr_last_name',
                'users.usr_first_name',
                'users.usr_middle_name',
                'users.usr_email',
                'users.usr_mobile',
                'users.usr_birth_date',
                'users.usr_active'
            )
            ->orderBy('users.usr_last_name')
            ->orderBy('users.usr_first_name');

        // Search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('users.usr_last_name', 'LIKE', "%$search%")
                    ->orWhere('users.usr_first_name', 'LIKE', "%$search%")
                    ->orWhere('users.usr_email', 'LIKE', "%$search%")
                    ->orWhere('users.usr_mobile', 'LIKE', "%$search%")
                    ->orWhere('branches.branch_name', 'LIKE', "%$search%");
            });
        }

        $clients = $query->paginate(500);

        // Get all addresses for listed clients
        $clientIds = collect($clients->items())->pluck('usr_id');

        $addresses = DB::table('user_addresses')
            ->leftJoin('addresses', 'user_addresses.add_id', '=', 'addresses.add_id')
            ->whereIn('user_addresses.usr_id', $clientIds)
            ->select(
                'user_addresses.*',
                'addresses.add_name'
            )
            ->get()
            ->groupBy('usr_id'); // group per client

        $branches = DB::table('branches')
            ->select('branch_id', 'branch_name')
            ->where('branch_active', 1)
            ->get();

        return view('profiling.clients.active', compact('clients', 'search', 'branches', 'addresses'));
    }

    public function clients_deleted(Request $request)
    {
        $search = $request->search ?? '';

        $sessionBranchId = session('branch_id');

        // Base query
        $query = DB::table('users')
            ->leftJoin('branches', 'users.branch_id', '=', 'branches.branch_id')
            ->where('users.utyp_id', '=', '3')
            ->where('users.usr_active', '=', '0');

        // Branch filter (unless super admin)
        if ($sessionBranchId != 1) {
            $query->where('users.branch_id', $sessionBranchId);
        }

        $query->select(
            'users.usr_id',
            'users.usr_uuid',
            'branches.branch_name',
            'users.usr_last_name',
            'users.usr_first_name',
            'users.usr_middle_name',
            'users.usr_email',
            'users.usr_mobile',
            'users.usr_birth_date',
            'users.usr_active',
        )
            ->groupBy(
                'users.usr_id',
                'users.usr_uuid',
                'branches.branch_name',
                'users.usr_last_name',
                'users.usr_first_name',
                'users.usr_middle_name',
                'users.usr_email',
                'users.usr_mobile',
                'users.usr_birth_date',
                'users.usr_active'
            )
            ->orderBy('users.usr_last_name')
            ->orderBy('users.usr_first_name');

        // Search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('users.usr_last_name', 'LIKE', "%$search%")
                    ->orWhere('users.usr_first_name', 'LIKE', "%$search%")
                    ->orWhere('users.usr_email', 'LIKE', "%$search%")
                    ->orWhere('users.usr_mobile', 'LIKE', "%$search%")
                    ->orWhere('branches.branch_name', 'LIKE', "%$search%");
            });
        }

        $clients = $query->paginate(500);

        // Get all addresses for listed clients
        $clientIds = collect($clients->items())->pluck('usr_id');

        $addresses = DB::table('user_addresses')
            ->leftJoin('addresses', 'user_addresses.add_id', '=', 'addresses.add_id')
            ->whereIn('user_addresses.usr_id', $clientIds)
            ->select(
                'user_addresses.*',
                'addresses.add_name'
            )
            ->get()
            ->groupBy('usr_id'); // group per client

        $branches = DB::table('branches')
            ->select('branch_id', 'branch_name')
            ->where('branch_active', 1)
            ->get();

        return view('profiling.clients.deleted', compact('clients', 'search', 'branches', 'addresses'));
    }

    public function clients_reset_password(Request $request, $usr_id)
    {
        $client = DB::table('users')
            ->where('usr_id', '=', $usr_id)
            ->first();

        if (!$client) {
            alert()->error('Client not found.');
            return redirect()->back();
        }

        DB::table('users')
            ->where('usr_uuid', '=', $usr_id)
            ->update([
                'usr_date_modified' => Carbon::now(),
                'usr_modified_by' => session('usr_id'),
                'usr_password' => md5('123456')
            ]);

        logUserActivity('Manage Clients', 'Reset password for client ' . $client->usr_last_name . ', ' . $client->usr_first_name);

        session()->flash('successMessage', 'Password has been reset to 123456.');
        return redirect()->back();
    }

    public function clients_delete(Request $request, $usr_id)
    {
        $client = DB::table('users')
            ->where('usr_id', '=', $usr_id)
            ->first();

        if (!$client) {
            alert()->error('Client not found.');
            return redirect()->back();
        }

        DB::table('users')
            ->where('usr_id', '=', $usr_id)
            ->update([
                'usr_date_modified' => Carbon::now(),
                'usr_modified_by' => session('usr_id'),
                'usr_active' => 0
            ]);

        logUserActivity('Manage Clients', 'Deleted client ' . $client->usr_last_name . ', ' . $client->usr_first_name);

        session()->flash('successMessage', 'Client has been Deleted.');
        return redirect()->back();
    }

    public function clients_restore(Request $request, $usr_id)
    {
        $client = DB::table('users')
            ->where('usr_id', '=', $usr_id)
            ->first();

        if (!$client) {
            alert()->error('Client not found.');
            return redirect()->back();
        }

        DB::table('users')
            ->where('usr_id', '=', $usr_id)
            ->update([
                'usr_date_modified' => Carbon::now(),
                'usr_modified_by' => session('usr_id'),
                'usr_active' => 1
            ]);

        logUserActivity('Manage Clients', 'Restored client ' . $client->usr_last_name . ', ' . $client->usr_first_name);

        session()->flash('successMessage', 'Client has been restored.');
        return redirect()->back();
    }
    // END CLIENTS
}