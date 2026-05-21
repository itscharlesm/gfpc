<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use App\Http\Controllers\api\v1\mobile_controllers\MobileMapboxDistanceController;

class AppointmentController extends Controller
{
    // START BOOK APPOINTMENTS
    public function clients(Request $request)
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
            ->where('user_addresses.uadd_active', 1)
            ->select(
                'user_addresses.*',
                'addresses.add_name'
            )
            ->get()
            ->groupBy('usr_id');

        $branches = DB::table('branches')
            ->select('branch_id', 'branch_name')
            ->where('branch_active', 1)
            ->get();

        $servicePackages = DB::table('service_packages')->get();

        $servicePackageAreas = DB::table('service_package_areas')
            ->where('svcpa_active', 1)
            ->select('svcpa_id', 'svcpa_area', 'svcpa_cost', 'branch_id')
            ->get()
            ->groupBy('branch_id');

        $termiteAreas = DB::table('service_package_area_termites')
            ->where('svcpat_active', 1)
            ->select('svcpat_id', 'svcpat_sqm_details', 'svcpat_cost', 'branch_id')
            ->get()
            ->groupBy('branch_id');

        return view('service_orders.appointments.book', compact('clients', 'search', 'branches', 'addresses', 'servicePackages', 'servicePackageAreas', 'termiteAreas'));
    }

    public function clients_book(Request $request)
    {
        $request->validate([
            'usr_id' => 'required|integer',
            'branch_id' => 'required|integer',
            'uadd_id' => 'required|integer',
            'svcp_id' => 'required|integer',
            'svca_client_date' => 'required|date',
            'svca_client_time' => 'required',
            'svc_problem_description' => 'nullable|string',
            'svcpa_id' => 'nullable|integer',
            'svcpat_id' => 'nullable|integer',
        ]);

        $isTermite = (int) $request->svcp_id === 8 ? 1 : 0;
        $svcpaId = $isTermite ? null : $request->svcpa_id;
        $svcpatId = $isTermite ? $request->svcpat_id : null;

        if ($isTermite) {
            $initialPrice = DB::table('service_package_area_termites')
                ->where('svcpat_id', $svcpatId)
                ->value('svcpat_cost') ?? 0;
        } else {
            $initialPrice = DB::table('service_package_areas')
                ->where('svcpa_id', $svcpaId)
                ->value('svcpa_cost') ?? 0;
        }

        // Distance & Location Price Calculation
        $address = DB::table('user_addresses')->where('uadd_id', $request->uadd_id)->first();
        $branch = DB::table('branches')->where('branch_id', $request->branch_id)->first();
        $locationFee = DB::table('service_package_area_locations')->where('branch_id', $request->branch_id)->first();

        $kmDistance = 0;
        $locationPrice = 0;

        if ($address && $branch && $locationFee) {

            // Haversine (straight-line fallback)
            $lat1 = deg2rad($branch->branch_latitude);
            $lon1 = deg2rad($branch->branch_longitude);
            $lat2 = deg2rad($address->uadd_latitude);
            $lon2 = deg2rad($address->uadd_longitude);

            $dlat = $lat2 - $lat1;
            $dlon = $lon2 - $lon1;

            $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;
            $haversineKm = 6371 * 2 * asin(sqrt($a));

            // Try Mapbox driving distance first, fall back to Haversine if it fails
            try {
                $mapboxKm = MobileMapboxDistanceController::getDrivingDistanceKm(
                    $branch->branch_longitude,
                    $branch->branch_latitude,
                    $address->uadd_longitude,
                    $address->uadd_latitude
                );

                $rawKm = ($mapboxKm !== null && $mapboxKm > 0) ? $mapboxKm : $haversineKm;
            } catch (\Exception $e) {
                $rawKm = $haversineKm;
            }

            // Round: .1–.4 round down, .5–.9 round up
            $kmDistance = (fmod($rawKm, 1) >= 0.5) ? ceil($rawKm) : floor($rawKm);

            // Price: first 10km = flat rate, beyond = flat + extra km * succeeding cost
            if ($kmDistance <= 10) {
                $locationPrice = $locationFee->svcpal_first_cost;
            } else {
                $locationPrice = $locationFee->svcpal_first_cost
                    + (($kmDistance - 10) * $locationFee->svcpal_succeeding_cost);
            }
        }

        $svcId = DB::table('services')->insertGetId([
            'svc_uuid' => generateuuid(),
            'branch_id' => $request->branch_id,
            'usr_id' => $request->usr_id,
            'svc_km_distance' => $kmDistance,
            'svc_is_package' => 0,
            'svcpat_id' => $svcpatId,
            'svc_is_termite' => $isTermite,
            'svc_problem_description' => $request->svc_problem_description,
            'svc_status' => 'REQUESTED',
            'svc_initial_price' => $initialPrice,
            'svc_location_price' => $locationPrice,
            'svc_balance' => $initialPrice + $locationPrice,
            'svc_payment_status' => 'NO PAYMENT',
            'svc_date_created' => Carbon::now(),
            'svc_created_by' => session('usr_id'),
            'svc_active' => 1,
        ]);

        DB::table('services')
            ->where('svc_id', $svcId)
            ->update(['svc_sa_number' => $svcId]);

        DB::table('service_order_pests')->insert([
            'svcop_uuid' => generateuuid(),
            'svc_id' => $svcId,
            'svcp_id' => $request->svcp_id,
            'svcop_date_created' => Carbon::now(),
            'svcop_created_by' => session('usr_id'),
            'svcop_active' => 1,
        ]);

        DB::table('service_orders')->insert([
            'svco_uuid' => generateuuid(),
            'svc_id' => $svcId,
            'svcpa_id' => $svcpaId,
            'svcpat_id' => $svcpatId,
            'svco_date_created' => Carbon::now(),
            'svco_created_by' => session('usr_id'),
            'svco_active' => 1,
        ]);

        DB::table('service_appointments')->insert([
            'svca_uuid' => generateuuid(),
            'svc_id' => $svcId,
            'uadd_id' => $request->uadd_id,
            'svca_client_date' => $request->svca_client_date,
            'svca_client_time' => $request->svca_client_time,
            'svca_status' => 'UNASSIGNED',
            'svca_date_created' => Carbon::now(),
            'svca_created_by' => session('usr_id'),
            'svca_active' => 1,
        ]);

        $serviceOrder = 'SA-' . str_pad($svcId, 6, '0', STR_PAD_LEFT);
        logUserActivity('Book Appointment', 'Booked appointment ' . $serviceOrder);

        session()->flash('successMessage', 'Appointment successfully booked.');
        return redirect()->back();
    }
    // END BOOK APPOINTMENTS

    // START REQUESTED APPOINTMENTS
    public function requested_appointments(Request $request)
    {
        $search = $request->search ?? '';
        $sessionBranchId = session('branch_id');

        $query = DB::table('services')
            ->leftJoin('users', 'services.usr_id', '=', 'users.usr_id')
            ->leftJoin('branches', 'services.branch_id', '=', 'branches.branch_id')
            ->where('services.svc_active', 1)
            ->whereIn('services.svc_status', ['REQUESTED', 'CONFIRM ASSESSMENT']);

        // Branch filter (same logic as users_active)
        if ($sessionBranchId != 1) {
            $query->where('services.branch_id', $sessionBranchId);
        }

        $query->select(
            'services.svc_id',
            'services.svc_sa_number',
            'services.svc_is_termite',
            'services.svc_is_package',
            'services.svc_status',
            'services.svc_payment_status',
            'services.svc_date_created',
            'users.usr_first_name',
            'users.usr_last_name',
            'users.usr_email',
            'users.usr_mobile',
            'branches.branch_name'
        );

        // Search
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('users.usr_first_name', 'LIKE', "%$search%")
                    ->orWhere('users.usr_last_name', 'LIKE', "%$search%")
                    ->orWhere('users.usr_email', 'LIKE', "%$search%")
                    ->orWhere('users.usr_mobile', 'LIKE', "%$search%")
                    ->orWhere('branches.branch_name', 'LIKE', "%$search%");
            });
        }

        $query->orderBy('services.svc_date_created', 'asc');

        $appointments = $query->paginate(50);

        return view('service_orders.appointments.requested.requested', compact('appointments', 'search'));
    }

    public function requested_appointments_view($svc_id)
    {
        $display = DB::table('services')
            ->leftJoin('users', 'services.usr_id', '=', 'users.usr_id')
            ->leftJoin('branches', 'services.branch_id', '=', 'branches.branch_id')
            ->leftJoin('service_appointments', 'services.svc_id', '=', 'service_appointments.svc_id')
            ->leftJoin('user_addresses', 'service_appointments.uadd_id', '=', 'user_addresses.uadd_id')
            ->leftJoin('addresses', 'user_addresses.add_id', '=', 'addresses.add_id')
            ->where('services.svc_id', $svc_id)
            ->select(
                'services.svc_id',
                'services.svc_sa_number',
                'services.svc_km_distance',
                'services.svc_property_type',
                'services.svc_is_package',
                'services.svcpat_id',
                'services.svc_is_termite',
                'services.svc_type_treatment',
                'services.svc_sqm_initial',
                'services.svc_sqm_final',
                'services.svc_with_device',
                'services.svc_device_count',
                'services.svc_problem_description',
                'services.svc_status',
                'services.svc_infestation',
                'services.svc_initial_price',
                'services.svc_location_price',
                'services.svc_device_price',
                'services.svc_fixed_price',
                'services.svc_final_price',
                'services.svc_balance',
                'services.svc_payment_status',
                'services.svc_assessment_recommendation',
                'services.svc_chemical_quantity',
                'services.svc_chemical_metric',
                'users.usr_first_name',
                'users.usr_last_name',
                'users.usr_email',
                'users.usr_mobile',
                'branches.branch_id',
                'branches.branch_name',
                'service_appointments.svca_client_date',
                'service_appointments.svca_client_time',
                'service_appointments.svca_approved_date',
                'service_appointments.svca_approved_time_from',
                'service_appointments.svca_approved_time_to',
                'service_appointments.svca_date_approved',
                'user_addresses.uadd_street',
                'user_addresses.uadd_barangay',
                'user_addresses.uadd_city',
                'user_addresses.uadd_province',
                'user_addresses.uadd_region',
                'addresses.add_name'
            )
            ->first();

        // Pest Types for this service
        $pestTypes = DB::table('service_order_pests')
            ->leftJoin('service_packages', 'service_order_pests.svcp_id', '=', 'service_packages.svcp_id')
            ->where('service_order_pests.svc_id', $svc_id)
            ->where('service_order_pests.svcop_active', 1)
            ->select(
                'service_order_pests.svcop_id',
                'service_packages.svcp_id',
                'service_packages.svcp_pest_type'
            )
            ->get();

        // Add Pest Type
        $existingPests = DB::table('service_order_pests')
            ->where('svc_id', $svc_id)
            ->where('svcop_active', 1)
            ->pluck('svcp_id')
            ->toArray();

        $servicePackages = DB::table('service_packages')
            ->where('svcp_id', '!=', 8)
            ->whereNotIn('svcp_id', $existingPests)
            ->get();

        // Service Orders with Areas (non-termite: svcpat_id IS NULL)
        $serviceAreas = DB::table('service_orders')
            ->leftJoin('service_package_areas', 'service_orders.svcpa_id', '=', 'service_package_areas.svcpa_id')
            ->where('service_orders.svc_id', $svc_id)
            ->whereNull('service_orders.svcpat_id')
            ->where('service_orders.svco_active', 1)
            ->select(
                'service_orders.svco_id',
                'service_package_areas.svcpa_id',
                'service_package_areas.svcpa_area',
                'service_package_areas.svcpa_cost'
            )
            ->get();

        // Add Service Area
        $existingAreas = DB::table('service_orders')
            ->where('svc_id', $svc_id)
            ->where('svco_active', 1)
            ->pluck('svcpa_id')
            ->toArray();

        $servicePackageAreas = DB::table('service_package_areas')
            ->whereNotIn('svcpa_id', $existingAreas)
            ->get();

        // Service Orders with Termite Areas (termite: svcpat_id IS NOT NULL)
        $termiteAreas = DB::table('service_orders')
            ->leftJoin('service_package_area_termites', 'service_orders.svcpat_id', '=', 'service_package_area_termites.svcpat_id')
            ->where('service_orders.svc_id', $svc_id)
            ->whereNotNull('service_orders.svcpat_id')
            ->where('service_orders.svco_active', 1)
            ->select(
                'service_orders.svco_id',
                'service_package_area_termites.svcpat_id',
                'service_package_area_termites.svcpat_sqm_details',
                'service_package_area_termites.svcpat_cost'
            )
            ->get();

        // Device cost for this branch (termite only)
        $deviceCost = DB::table('service_package_area_devices')
            ->where('branch_id', $display->branch_id)
            ->where('svcpad_active', 1)
            ->select('svcpad_id', 'svcpad_cost')
            ->first();

        $locationRate = DB::table('service_package_area_locations')
            ->where('branch_id', $display->branch_id)
            ->where('svcpal_active', 1)
            ->select('svcpal_first_cost', 'svcpal_succeeding_cost')
            ->first();

        $termiteAreaOptions = DB::table('service_package_area_termites')
            ->where('branch_id', $display->branch_id)
            ->where('svcpat_active', 1)
            ->select('svcpat_id', 'svcpat_sqm_details', 'svcpat_cost')
            ->get();

        // Client Appointment Images
        $appointmentImages = DB::table('service_appointment_images')
            ->join('service_appointments', 'service_appointment_images.svca_id', '=', 'service_appointments.svca_id')
            ->where('service_appointments.svc_id', $svc_id)
            ->where('service_appointment_images.svcap_active', 1)
            ->select('service_appointment_images.*')
            ->get();

        return view('service_orders.appointments.requested.view_requested', compact('display', 'pestTypes', 'servicePackages', 'serviceAreas', 'servicePackageAreas', 'termiteAreas', 'deviceCost', 'locationRate', 'termiteAreaOptions', 'appointmentImages'));
    }

    public function requested_appointments_view_add_pest(Request $request)
    {
        $request->validate([
            'svc_id' => 'required',
            'svcp_id' => 'required'
        ]);

        $svc_id = $request->svc_id;
        $svcp_id = $request->svcp_id;

        // check if already exists active
        $exists = DB::table('service_order_pests')
            ->where('svc_id', $svc_id)
            ->where('svcp_id', $svcp_id)
            ->where('svcop_active', 1)
            ->first();

        if ($exists) {
            alert()->error('Pest type already added.');
            return redirect()->back();
        }

        DB::table('service_order_pests')->insert([
            'svcop_uuid' => generateuuid(),
            'svc_id' => $svc_id,
            'svcp_id' => $svcp_id,
            'svcop_date_created' => Carbon::now(),
            'svcop_created_by' => session('usr_id'),
            'svcop_active' => 1
        ]);

        $pest = DB::table('service_packages')
            ->where('svcp_id', $svcp_id)
            ->first();

        $serviceOrder = 'SA-' . str_pad($svc_id, 6, '0', STR_PAD_LEFT);

        logUserActivity(
            'Manage Appointments',
            'Added pest type ' . ($pest->svcp_pest_type ?? '') . ' to ' . $serviceOrder
        );

        session()->flash('successMessage', 'Pest type successfully added.');
        return redirect()->back();
    }

    public function requested_appointments_view_delete_pest(Request $request, $svcop_id)
    {
        $pest = DB::table('service_order_pests')
            ->leftJoin('service_packages', 'service_order_pests.svcp_id', '=', 'service_packages.svcp_id')
            ->where('service_order_pests.svcop_id', $svcop_id)
            ->select(
                'service_order_pests.svcop_id',
                'service_order_pests.svc_id',
                'service_packages.svcp_pest_type'
            )
            ->first();

        if (!$pest) {
            alert()->error('Pest type not found.');
            return redirect()->back();
        }

        DB::table('service_order_pests')
            ->where('svcop_id', $svcop_id)
            ->update([
                'svcop_date_modified' => Carbon::now(),
                'svcop_modified_by' => session('usr_id'),
                'svcop_active' => 0
            ]);

        $serviceOrder = 'SA-' . str_pad($pest->svc_id, 6, '0', STR_PAD_LEFT);

        logUserActivity(
            'Manage Appointments',
            'Deleted pest type ' . $pest->svcp_pest_type . ' from ' . $serviceOrder
        );

        session()->flash('successMessage', 'Appointment service pest type has been deleted.');
        return redirect()->back();
    }

    public function requested_appointments_view_add_service(Request $request)
    {
        $request->validate([
            'svc_id' => 'required',
            'svcpa_id' => 'required'
        ]);

        $svc_id = $request->svc_id;
        $svcpa_id = $request->svcpa_id;

        // check if already added
        $exists = DB::table('service_orders')
            ->where('svc_id', $svc_id)
            ->where('svcpa_id', $svcpa_id)
            ->where('svco_active', 1)
            ->first();

        if ($exists) {
            alert()->error('Service area already added.');
            return redirect()->back();
        }

        $area = DB::table('service_package_areas')
            ->where('svcpa_id', $svcpa_id)
            ->first();

        if (!$area) {
            alert()->error('Service area not found.');
            return redirect()->back();
        }

        $service = DB::table('services')
            ->where('svc_id', $svc_id)
            ->first();

        if (!$service) {
            alert()->error('Service not found.');
            return redirect()->back();
        }

        $newInitial = $service->svc_initial_price + $area->svcpa_cost;
        $newBalance = $service->svc_balance + $area->svcpa_cost;

        DB::beginTransaction();

        // insert service order
        DB::table('service_orders')->insert([
            'svco_uuid' => generateuuid(),
            'svc_id' => $svc_id,
            'svcpa_id' => $svcpa_id,
            'svco_date_created' => Carbon::now(),
            'svco_created_by' => session('usr_id'),
            'svco_active' => 1
        ]);

        // update service pricing
        DB::table('services')
            ->where('svc_id', $svc_id)
            ->update([
                'svc_initial_price' => $newInitial,
                'svc_balance' => $newBalance
            ]);

        DB::commit();

        $serviceOrder = 'SA-' . str_pad($svc_id, 6, '0', STR_PAD_LEFT);

        logUserActivity(
            'Manage Appointments',
            'Added service area "' . $area->svcpa_area . '" to ' . $serviceOrder .
            '. Added ' . number_format($area->svcpa_cost, 2) .
            '. New Initial: ' . number_format($newInitial, 2) .
            ', New Balance: ' . number_format($newBalance, 2)
        );

        session()->flash('successMessage', 'Service area successfully added.');
        return redirect()->back();
    }

    public function requested_appointments_view_delete_service(Request $request, $svcpa_id)
    {
        $service = DB::table('service_orders')
            ->leftJoin('service_package_areas', 'service_orders.svcpa_id', '=', 'service_package_areas.svcpa_id')
            ->leftJoin('services', 'service_orders.svc_id', '=', 'services.svc_id')
            ->where('service_orders.svcpa_id', $svcpa_id)
            ->where('service_orders.svco_active', 1)
            ->select(
                'service_orders.svco_id',
                'service_orders.svc_id',
                'service_orders.svcpa_id',
                'service_package_areas.svcpa_area',
                'service_package_areas.svcpa_cost',
                'services.svc_initial_price',
                'services.svc_balance'
            )
            ->first();

        if (!$service) {
            alert()->error('Service order not found.');
            return redirect()->back();
        }

        $newInitialPrice = $service->svc_initial_price - $service->svcpa_cost;
        $newBalance = $service->svc_balance - $service->svcpa_cost;

        DB::beginTransaction();

        // Soft delete service order
        DB::table('service_orders')
            ->where('svcpa_id', $svcpa_id)
            ->update([
                'svco_date_modified' => Carbon::now(),
                'svco_modified_by' => session('usr_id'),
                'svco_active' => 0
            ]);

        // Update service prices
        DB::table('services')
            ->where('svc_id', $service->svc_id)
            ->update([
                'svc_initial_price' => $newInitialPrice,
                'svc_balance' => $newBalance
            ]);

        DB::commit();

        $serviceOrder = 'SA-' . str_pad($service->svc_id, 6, '0', STR_PAD_LEFT);

        logUserActivity(
            'Manage Appointments',
            'Deleted service area "' . $service->svcpa_area .
            '" from ' . $serviceOrder .
            '. Deducted ' . number_format($service->svcpa_cost, 2) .
            ' from Initial Price and Balance. ' .
            'New Initial Price: ' . number_format($newInitialPrice, 2) .
            ', New Balance: ' . number_format($newBalance, 2)
        );

        session()->flash(
            'successMessage',
            'Appointment service order has been deleted and prices updated.'
        );

        return redirect()->back();
    }

    public function requested_appointments_view_assess(Request $request)
    {
        $request->validate([
            'svc_id' => 'required',
            'svc_sa_number' => 'required',
            'svc_infestation' => 'required',
            'svc_location_price' => 'required|numeric',
            'svc_final_price' => 'required|numeric',
            'svca_approved_date' => 'required',
            'svca_approved_time_from' => 'required',
            'svca_approved_time_to' => 'required',
        ]);

        $svc_id = $request->svc_id;

        if (
            DB::table('services')
                ->where('svc_sa_number', $request->svc_sa_number)
                ->where('svc_id', '!=', $svc_id)
                ->exists()
        ) {

            session()->flash('errorMessage', 'SA Number already exists.');
            return redirect()->back();
        }

        $svc_id = $request->svc_id;
        $isTermite = $request->svc_is_termite;
        $isPackage = $request->svc_is_package;
        $servicePrice = $request->svc_location_price;
        $finalPrice = $request->svc_final_price;

        // Fetch existing service record
        $service = DB::table('services')->where('svc_id', $svc_id)->first();

        // Shared fields across both paths
        $sharedFields = [
            'svc_sa_number' => $request->svc_sa_number,
            'svc_property_type' => $request->svc_property_type,
            'svc_km_distance' => $request->svc_km_distance,
            'svc_fixed_price' => $request->svc_fixed_price,
            'svc_chemical_quantity' => $request->svc_chemical_quantity,
            'svc_chemical_metric' => $request->svc_chemical_metric,
            'svc_assessment_recommendation' => $request->svc_assessment_recommendation,
        ];

        if ($isTermite == 1) {
            // TERMITE PATH
            $treatmentType = $request->svc_type_treatment;
            $withDevice = ($treatmentType === 'HYBRID TREATMENT') ? 1 : 0;
            $deviceCount = $withDevice ? (int) $request->svc_device_count : null;
            $sqmInitial = $request->svc_sqm_initial ?? $service->svc_sqm_initial;

            // Recompute device price server-side (mirrors JS: count × unit cost)
            $deviceCostRow = DB::table('service_package_area_devices')
                ->where('branch_id', $service->branch_id)
                ->where('svcpad_active', 1)
                ->first();

            $devicePrice = ($withDevice && $deviceCount && $deviceCostRow)
                ? $deviceCount * $deviceCostRow->svcpad_cost
                : 0;

            DB::table('services')
                ->where('svc_id', $svc_id)
                ->update(array_merge($sharedFields, [
                    'svc_is_termite' => 1,
                    'svcpat_id' => $request->svcpat_id,
                    'svc_type_treatment' => $treatmentType,
                    'svc_with_device' => $withDevice,
                    'svc_device_count' => $deviceCount,
                    'svc_device_price' => $devicePrice,
                    'svc_sqm_initial' => $sqmInitial,
                    'svc_sqm_final' => $sqmInitial,
                    'svc_status' => 'CONFIRM ASSESSMENT',
                    'svc_infestation' => $request->svc_infestation,
                    'svc_location_price' => $servicePrice,
                    'svc_final_price' => $finalPrice,
                    'svc_balance' => $finalPrice,
                    'svc_date_modified' => Carbon::now(),
                    'svc_modified_by' => session('usr_id'),
                ]));

            DB::table('service_orders')
                ->where('svc_id', $svc_id)
                ->update([
                    'svcpat_id' => $request->svcpat_id,
                    'svco_date_modified' => Carbon::now(),
                    'svco_modified_by' => session('usr_id'),
                ]);

        } else {
            // NON-TERMITE PATH
            $sqmInitial = $service->svc_sqm_initial;
            if ($isPackage == 0 && is_null($service->svc_sqm_initial)) {
                $sqmInitial = null;
            } elseif ($isPackage == 1) {
                $sqmInitial = $request->svc_sqm_initial ?? $service->svc_sqm_initial;
            }

            DB::table('services')
                ->where('svc_id', $svc_id)
                ->update(array_merge($sharedFields, [
                    'svc_is_package' => $isPackage,
                    'svc_sqm_initial' => $sqmInitial,
                    'svc_sqm_final' => $isPackage == 1 ? $sqmInitial : null,
                    'svc_status' => 'CONFIRM ASSESSMENT',
                    'svc_infestation' => $request->svc_infestation,
                    'svc_location_price' => $servicePrice,
                    'svc_initial_price' => $request->svc_initial_price ?? $service->svc_initial_price,
                    'svc_final_price' => $finalPrice,
                    'svc_balance' => $finalPrice,
                    'svc_date_modified' => Carbon::now(),
                    'svc_modified_by' => session('usr_id'),
                ]));
        }

        // APPOINTMENT (shared)
        DB::table('service_appointments')
            ->where('svc_id', $svc_id)
            ->update([
                'svca_approved_date' => $request->svca_approved_date,
                'svca_approved_time_from' => $request->svca_approved_time_from,
                'svca_approved_time_to' => $request->svca_approved_time_to,
                'svca_date_approved' => Carbon::now(),
                'svca_approved_by' => session('usr_id'),
                'svca_date_modified' => Carbon::now(),
                'svca_modified_by' => session('usr_id'),
            ]);

        $serviceOrder = 'SA-' . str_pad($svc_id, 6, '0', STR_PAD_LEFT);

        logUserActivity('Manage Appointments', 'Assessed appointment ' . $serviceOrder);

        session()->flash('successMessage', 'Appointment successfully assessed.');
        return redirect()->back();
    }

    public function requested_appointments_view_assess_confirmation(Request $request)
    {
        $request->validate([
            'svc_id' => 'required',
            'svc_sa_number' => 'required',
            'svc_infestation' => 'required',
            'svc_location_price' => 'required|numeric',
            'svc_final_price' => 'required|numeric',
            'svca_approved_date' => 'required',
            'svca_approved_time_from' => 'required',
            'svca_approved_time_to' => 'required',
        ]);

        $svc_id = $request->svc_id;

        if (
            DB::table('services')
                ->where('svc_sa_number', $request->svc_sa_number)
                ->where('svc_id', '!=', $svc_id)
                ->exists()
        ) {

            session()->flash('errorMessage', 'SA Number already exists.');
            return redirect()->back();
        }

        $svc_id = $request->svc_id;
        $isTermite = $request->svc_is_termite;
        $isPackage = $request->svc_is_package;
        $servicePrice = $request->svc_location_price;
        $finalPrice = $request->svc_final_price;

        $service = DB::table('services')->where('svc_id', $svc_id)->first();

        // ← NEW: shared fields (mirrors assess method)
        $sharedFields = [
            'svc_sa_number' => $request->svc_sa_number,
            'svc_property_type' => $request->svc_property_type,
            'svc_km_distance' => $request->svc_km_distance,
            'svc_fixed_price' => $request->svc_fixed_price,
            'svc_chemical_quantity' => $request->svc_chemical_quantity,
            'svc_chemical_metric' => $request->svc_chemical_metric,
            'svc_assessment_recommendation' => $request->svc_assessment_recommendation,
        ];

        if ($isTermite == 1) {
            $treatmentType = $request->svc_type_treatment;
            $withDevice = ($treatmentType === 'HYBRID TREATMENT') ? 1 : 0;
            $deviceCount = $withDevice ? (int) $request->svc_device_count : null;
            $sqmInitial = $request->svc_sqm_initial ?? $service->svc_sqm_initial;

            $deviceCostRow = DB::table('service_package_area_devices')
                ->where('branch_id', $service->branch_id)
                ->where('svcpad_active', 1)
                ->first();

            $devicePrice = ($withDevice && $deviceCount && $deviceCostRow)
                ? $deviceCount * $deviceCostRow->svcpad_cost
                : 0;

            DB::table('services')
                ->where('svc_id', $svc_id)
                ->update(array_merge($sharedFields, [   // ← merged
                    'svc_is_termite' => 1,
                    'svcpat_id' => $request->svcpat_id,
                    'svc_type_treatment' => $treatmentType,
                    'svc_with_device' => $withDevice,
                    'svc_device_count' => $deviceCount,
                    'svc_device_price' => $devicePrice,
                    'svc_sqm_initial' => $sqmInitial,
                    'svc_sqm_final' => $sqmInitial,
                    'svc_status' => 'ASSESSED',
                    'svc_infestation' => $request->svc_infestation,
                    'svc_location_price' => $servicePrice,
                    'svc_final_price' => $finalPrice,
                    'svc_balance' => $finalPrice,
                    'svc_date_modified' => Carbon::now(),
                    'svc_modified_by' => session('usr_id'),
                ]));

            DB::table('service_orders')
                ->where('svc_id', $svc_id)
                ->update([
                    'svcpat_id' => $request->svcpat_id,
                    'svco_date_modified' => Carbon::now(),
                    'svco_modified_by' => session('usr_id'),
                ]);

        } else {
            $sqmInitial = $service->svc_sqm_initial;
            if ($isPackage == 0 && is_null($service->svc_sqm_initial)) {
                $sqmInitial = null;
            } elseif ($isPackage == 1) {
                $sqmInitial = $request->svc_sqm_initial ?? $service->svc_sqm_initial;
            }

            DB::table('services')
                ->where('svc_id', $svc_id)
                ->update(array_merge($sharedFields, [   // ← merged
                    'svc_is_package' => $isPackage,
                    'svc_sqm_initial' => $sqmInitial,
                    'svc_sqm_final' => $isPackage == 1 ? $sqmInitial : null,
                    'svc_status' => 'ASSESSED',
                    'svc_infestation' => $request->svc_infestation,
                    'svc_location_price' => $servicePrice,
                    'svc_initial_price' => $request->svc_initial_price ?? $service->svc_initial_price,
                    'svc_final_price' => $finalPrice,
                    'svc_balance' => $finalPrice,
                    'svc_date_modified' => Carbon::now(),
                    'svc_modified_by' => session('usr_id'),
                ]));
        }

        DB::table('service_appointments')
            ->where('svc_id', $svc_id)
            ->update([
                'svca_approved_date' => $request->svca_approved_date,
                'svca_approved_time_from' => $request->svca_approved_time_from,
                'svca_approved_time_to' => $request->svca_approved_time_to,
                'svca_date_approved' => Carbon::now(),
                'svca_approved_by' => session('usr_id'),
                'svca_date_modified' => Carbon::now(),
                'svca_modified_by' => session('usr_id'),
            ]);

        $serviceOrder = 'SA-' . str_pad($svc_id, 6, '0', STR_PAD_LEFT);

        logUserActivity('Manage Appointments', 'Confirmed Assessment ' . $serviceOrder);

        session()->flash('successMessage', 'Appointment successfully assessed.');
        return redirect()->action(
            [AppointmentController::class, 'assessed_appointments_view'],
            ['svc_id' => $svc_id]
        );
    }
    // END REQUESTED APPOINTMENTS

    // START ASSESSED APPOINTMENTS
    public function assessed_appointments(Request $request)
    {
        $search = $request->search ?? '';
        $sessionBranchId = session('branch_id');

        $query = DB::table('services')
            ->leftJoin('users', 'services.usr_id', '=', 'users.usr_id')
            ->leftJoin('branches', 'services.branch_id', '=', 'branches.branch_id')
            ->leftJoin('service_appointments', 'service_appointments.svc_id', '=', 'services.svc_id')
            ->where('services.svc_active', 1)
            ->where('services.svc_status', 'ASSESSED');

        // Branch filter
        if ($sessionBranchId != 1) {
            $query->where('services.branch_id', $sessionBranchId);
        }

        $query->select(
            'services.svc_id',
            'services.svc_sa_number',
            'services.svc_is_termite',
            'services.svc_is_package',
            'services.svc_status',
            'services.svc_payment_status',
            'services.svc_date_created',
            'users.usr_first_name',
            'users.usr_last_name',
            'users.usr_email',
            'users.usr_mobile',
            'branches.branch_name',
            'service_appointments.svca_approved_date',
            'service_appointments.svca_approved_time_from',
            'service_appointments.svca_approved_time_to'
        );

        // Search
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('users.usr_first_name', 'LIKE', "%$search%")
                    ->orWhere('users.usr_last_name', 'LIKE', "%$search%")
                    ->orWhere('users.usr_email', 'LIKE', "%$search%")
                    ->orWhere('users.usr_mobile', 'LIKE', "%$search%")
                    ->orWhere('branches.branch_name', 'LIKE', "%$search%");
            });
        }

        $query->orderBy('services.svc_date_created', 'asc');

        $appointments = $query->paginate(50);

        return view('service_orders.appointments.assessed.assessed', compact('appointments', 'search'));
    }
    public function assessed_appointments_view($svc_id)
    {
        $display = DB::table('services')
            ->leftJoin('users', 'services.usr_id', '=', 'users.usr_id')
            ->leftJoin('branches', 'services.branch_id', '=', 'branches.branch_id')
            ->leftJoin('service_appointments', 'services.svc_id', '=', 'service_appointments.svc_id')
            ->leftJoin('user_addresses', 'service_appointments.uadd_id', '=', 'user_addresses.uadd_id')
            ->leftJoin('addresses', 'user_addresses.add_id', '=', 'addresses.add_id')
            ->leftJoin('users as approver', 'service_appointments.svca_approved_by', '=', 'approver.usr_id')
            ->where('services.svc_id', $svc_id)
            ->select(
                'services.svc_id',
                'services.svc_sa_number',
                'services.svc_km_distance',
                'services.svc_property_type',
                'services.svc_is_package',
                'services.svcpat_id',
                'services.svc_is_termite',
                'services.svc_type_treatment',
                'services.svc_sqm_initial',
                'services.svc_sqm_final',
                'services.svc_with_device',
                'services.svc_device_count',
                'services.svc_problem_description',
                'services.svc_status',
                'services.svc_infestation',
                'services.svc_initial_price',
                'services.svc_location_price',
                'services.svc_device_price',
                'services.svc_fixed_price',
                'services.svc_final_price',
                'services.svc_balance',
                'services.svc_payment_status',
                'services.svc_assessment_recommendation',
                'services.svc_chemical_quantity',
                'services.svc_chemical_metric',
                'users.usr_first_name',
                'users.usr_last_name',
                'users.usr_email',
                'users.usr_mobile',
                'branches.branch_id',
                'branches.branch_name',
                'service_appointments.svca_client_date',
                'service_appointments.svca_client_time',
                'service_appointments.svca_approved_date',
                'service_appointments.svca_approved_time_from',
                'service_appointments.svca_approved_time_to',
                'service_appointments.svca_date_approved',
                'approver.usr_first_name as approved_first_name',
                'approver.usr_last_name as approved_last_name',
                'user_addresses.uadd_street',
                'user_addresses.uadd_barangay',
                'user_addresses.uadd_city',
                'user_addresses.uadd_province',
                'user_addresses.uadd_region',
                'addresses.add_name'
            )
            ->first();

        // Pest Types for this service
        $pestTypes = DB::table('service_order_pests')
            ->leftJoin('service_packages', 'service_order_pests.svcp_id', '=', 'service_packages.svcp_id')
            ->where('service_order_pests.svc_id', $svc_id)
            ->where('service_order_pests.svcop_active', 1)
            ->select(
                'service_order_pests.svcop_id',
                'service_packages.svcp_id',
                'service_packages.svcp_pest_type'
            )
            ->get();

        // Service Orders with Areas (non-termite: svcpat_id IS NULL)
        $serviceAreas = DB::table('service_orders')
            ->leftJoin('service_package_areas', 'service_orders.svcpa_id', '=', 'service_package_areas.svcpa_id')
            ->where('service_orders.svc_id', $svc_id)
            ->whereNull('service_orders.svcpat_id')
            ->where('service_orders.svco_active', 1)
            ->select(
                'service_orders.svco_id',
                'service_package_areas.svcpa_id',
                'service_package_areas.svcpa_area',
                'service_package_areas.svcpa_cost'
            )
            ->get();

        // Service Orders with Termite Areas (termite: svcpat_id IS NOT NULL)
        $termiteAreas = DB::table('service_orders')
            ->leftJoin('service_package_area_termites', 'service_orders.svcpat_id', '=', 'service_package_area_termites.svcpat_id')
            ->where('service_orders.svc_id', $svc_id)
            ->whereNotNull('service_orders.svcpat_id')
            ->where('service_orders.svco_active', 1)
            ->select(
                'service_orders.svco_id',
                'service_package_area_termites.svcpat_id',
                'service_package_area_termites.svcpat_sqm_details',
                'service_package_area_termites.svcpat_cost'
            )
            ->get();

        // Client Appointment Images
        $appointmentImages = DB::table('service_appointment_images')
            ->join('service_appointments', 'service_appointment_images.svca_id', '=', 'service_appointments.svca_id')
            ->where('service_appointments.svc_id', $svc_id)
            ->where('service_appointment_images.svcap_active', 1)
            ->select('service_appointment_images.*')
            ->get();

        $approvedDate = null;
        $approvedTimeFrom = null;
        $approvedTimeTo = null;

        $appointment = DB::table('service_appointments')
            ->where('svc_id', $svc_id)
            ->first();

        if ($appointment) {
            $approvedDate = $appointment->svca_approved_date;
            $approvedTimeFrom = $appointment->svca_approved_time_from;
            $approvedTimeTo = $appointment->svca_approved_time_to;
        }

        // Day-of-week name from approved date (MONDAY, TUESDAY, etc.)
        $dayName = $approvedDate
            ? strtoupper(Carbon::parse($approvedDate)->format('l'))
            : null;

        // Technicians
        $technicians = DB::table('users')
            ->where('utyp_id', 2)
            ->where('usr_active', 1)
            ->where('branch_id', $display->branch_id)
            ->orderBy('usr_last_name', 'asc')
            ->select('usr_id', 'usr_first_name', 'usr_last_name')
            ->get()
            ->map(function ($tech) use ($dayName, $approvedDate, $approvedTimeFrom, $approvedTimeTo) {
                // Check rest day
                $isRestDay = false;
                if ($dayName) {
                    $avail = DB::table('user_availabilities')
                        ->where('usr_id', $tech->usr_id)
                        ->where('uavail_name', $dayName)
                        ->first();
                    $isRestDay = !$avail || $avail->uavail_active == 0;
                }

                // Check existing assignments that overlap
                $isBusy = false;
                if ($approvedDate && $approvedTimeFrom && $approvedTimeTo) {
                    $conflict = DB::table('service_appointment_schedules')
                        ->join('service_appointments', 'service_appointments.svca_id', '=', 'service_appointment_schedules.svca_id')
                        ->where('service_appointment_schedules.svcas_assigned_to', $tech->usr_id)
                        ->where('service_appointment_schedules.svcas_active', 1)
                        ->where('service_appointments.svca_approved_date', $approvedDate)
                        ->where('service_appointments.svca_approved_time_from', '<', $approvedTimeTo)
                        ->where('service_appointments.svca_approved_time_to', '>', $approvedTimeFrom)
                        ->first();
                    $isBusy = (bool) $conflict;
                }

                $tech->is_rest_day = $isRestDay;
                $tech->is_busy = $isBusy;
                return $tech;
            });

        // Existing schedules for the timeline (all techs, same date)
        $daySchedules = [];
        if ($approvedDate) {
            $rows = DB::table('service_appointment_schedules')
                ->join('service_appointments', 'service_appointments.svca_id', '=', 'service_appointment_schedules.svca_id')
                ->join('services', 'services.svc_id', '=', 'service_appointments.svc_id')
                ->join('users as clients', 'clients.usr_id', '=', 'services.usr_id')
                ->leftJoin('user_addresses', 'service_appointments.uadd_id', '=', 'user_addresses.uadd_id')
                ->leftJoin('users as assigner', 'assigner.usr_id', '=', 'service_appointment_schedules.svcas_assigned_by')
                ->where('service_appointment_schedules.svcas_active', 1)
                ->where('service_appointments.svca_approved_date', $approvedDate)
                ->select(
                    'service_appointment_schedules.svcas_assigned_to',
                    'service_appointment_schedules.svcas_date_assigned',
                    'service_appointment_schedules.svcas_assigned_by',
                    'assigner.usr_first_name as assigner_first_name',
                    'assigner.usr_last_name as assigner_last_name',
                    'service_appointments.svca_approved_time_from',
                    'service_appointments.svca_approved_time_to',
                    'clients.usr_first_name',
                    'clients.usr_last_name',
                    'clients.usr_email',
                    'clients.usr_mobile',
                    'user_addresses.uadd_street',
                    'user_addresses.uadd_barangay',
                    'user_addresses.uadd_city',
                    'user_addresses.uadd_province',
                    'user_addresses.uadd_region',
                    'services.svc_km_distance'
                )
                ->get();

            foreach ($rows as $row) {
                $daySchedules[$row->svcas_assigned_to][] = $row;
            }
        }

        $allTechs = $technicians->map(function ($t) {
            return [
                'id' => $t->usr_id,
                'label' => $t->usr_last_name . ', ' . substr($t->usr_first_name, 0, 1) . '.',
                'is_rest' => $t->is_rest_day,
                'is_busy' => $t->is_busy,
            ];
        })->values();

        return view('service_orders.appointments.assessed.view_assessed', compact('display', 'pestTypes', 'serviceAreas', 'termiteAreas', 'appointmentImages', 'technicians', 'approvedDate', 'approvedTimeFrom', 'approvedTimeTo', 'daySchedules', 'allTechs'));
    }

    public function assessed_appointments_view_assign(Request $request)
    {
        $request->validate([
            'svc_id' => 'required',
            'svcas_assigned_to' => 'required',
        ]);

        $svc_id = $request->svc_id;

        // Get the svca_id for this service
        $appointment = DB::table('service_appointments')
            ->where('svc_id', $svc_id)
            ->first();

        DB::table('services')
            ->where('svc_id', $svc_id)
            ->update([
                'svc_status' => 'SCHEDULED',
            ]);

        DB::table('service_appointments')
            ->where('svc_id', $svc_id)
            ->update([
                'svca_status' => 'ASSIGNED',
                'svca_date_modified' => Carbon::now(),
                'svca_modified_by' => session('usr_id'),
            ]);

        DB::table('service_appointment_schedules')
            ->insert([
                'svcas_uuid' => (string) \Illuminate\Support\Str::uuid(),
                'svca_id' => $appointment->svca_id,
                'svcas_assigned_to' => $request->svcas_assigned_to,
                'svcas_assigned_by' => session('usr_id'),
                'svcas_date_assigned' => Carbon::now(),
                'svcas_date_created' => Carbon::now(),
                'svcas_created_by' => session('usr_id'),
                'svcas_active' => 1,
            ]);

        $serviceOrder = 'SA-' . str_pad($svc_id, 6, '0', STR_PAD_LEFT);

        logUserActivity('Manage Appointments', 'Assigned Technician ' . $serviceOrder);

        session()->flash('successMessage', 'Assigned technician successfully.');
        return redirect()->action(
            [AppointmentController::class, 'scheduled_appointments_view'],
            ['svc_id' => $svc_id]
        );
    }
    // END ASSESSED APPOINTMENTS

    // START SCHEDULED APPOINTMENTS
    public function scheduled_appointments(Request $request)
    {
        $search = $request->search ?? '';
        $sessionBranchId = session('branch_id');

        $query = DB::table('services')
            ->leftJoin('users', 'services.usr_id', '=', 'users.usr_id')
            ->leftJoin('branches', 'services.branch_id', '=', 'branches.branch_id')
            ->leftJoin('service_appointments', 'service_appointments.svc_id', '=', 'services.svc_id')
            ->where('services.svc_active', 1)
            ->where('services.svc_status', 'SCHEDULED');

        // Branch filter
        if ($sessionBranchId != 1) {
            $query->where('services.branch_id', $sessionBranchId);
        }

        $query->select(
            'services.svc_id',
            'services.svc_sa_number',
            'services.svc_is_termite',
            'services.svc_is_package',
            'services.svc_status',
            'services.svc_payment_status',
            'services.svc_date_created',
            'users.usr_first_name',
            'users.usr_last_name',
            'users.usr_email',
            'users.usr_mobile',
            'branches.branch_name',
            'service_appointments.svca_approved_date',
            'service_appointments.svca_approved_time_from',
            'service_appointments.svca_approved_time_to'
        );

        // Search
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('users.usr_first_name', 'LIKE', "%$search%")
                    ->orWhere('users.usr_last_name', 'LIKE', "%$search%")
                    ->orWhere('users.usr_email', 'LIKE', "%$search%")
                    ->orWhere('users.usr_mobile', 'LIKE', "%$search%")
                    ->orWhere('branches.branch_name', 'LIKE', "%$search%");
            });
        }

        $query->orderBy('services.svc_date_created', 'asc');

        $appointments = $query->paginate(50);

        return view('service_orders.appointments.scheduled.scheduled', compact('appointments', 'search'));
    }

    public function scheduled_appointments_view($svc_id)
    {
        $display = DB::table('services')
            ->leftJoin('users', 'services.usr_id', '=', 'users.usr_id')
            ->leftJoin('branches', 'services.branch_id', '=', 'branches.branch_id')
            ->leftJoin('service_appointments', 'services.svc_id', '=', 'service_appointments.svc_id')
            ->leftJoin('user_addresses', 'service_appointments.uadd_id', '=', 'user_addresses.uadd_id')
            ->leftJoin('addresses', 'user_addresses.add_id', '=', 'addresses.add_id')
            ->leftJoin('users as approver', 'service_appointments.svca_approved_by', '=', 'approver.usr_id')
            ->leftJoin('service_appointment_schedules', 'service_appointments.svca_id', '=', 'service_appointment_schedules.svca_id')
            ->leftJoin('users as assigned_to', 'service_appointment_schedules.svcas_assigned_to', '=', 'assigned_to.usr_id')
            ->where('services.svc_id', $svc_id)
            ->select(
                'services.svc_id',
                'services.svc_sa_number',
                'services.svc_km_distance',
                'services.svc_property_type',
                'services.svc_is_package',
                'services.svcpat_id',
                'services.svc_is_termite',
                'services.svc_type_treatment',
                'services.svc_sqm_initial',
                'services.svc_sqm_final',
                'services.svc_with_device',
                'services.svc_device_count',
                'services.svc_problem_description',
                'services.svc_status',
                'services.svc_infestation',
                'services.svc_initial_price',
                'services.svc_location_price',
                'services.svc_device_price',
                'services.svc_fixed_price',
                'services.svc_final_price',
                'services.svc_balance',
                'services.svc_payment_status',
                'services.svc_assessment_recommendation',
                'services.svc_chemical_quantity',
                'services.svc_chemical_metric',
                'users.usr_first_name',
                'users.usr_last_name',
                'users.usr_email',
                'users.usr_mobile',
                'branches.branch_id',
                'branches.branch_name',
                'service_appointments.svca_client_date',
                'service_appointments.svca_client_time',
                'service_appointments.svca_approved_date',
                'service_appointments.svca_approved_time_from',
                'service_appointments.svca_approved_time_to',
                'service_appointments.svca_date_approved',
                'approver.usr_first_name as approved_first_name',
                'approver.usr_last_name as approved_last_name',
                'service_appointment_schedules.svcas_assigned_to as current_technician_id',  // <-- added
                'service_appointment_schedules.svcas_date_assigned',
                'assigned_to.usr_first_name as assigned_first_name',
                'assigned_to.usr_last_name as assigned_last_name',
                'user_addresses.uadd_street',
                'user_addresses.uadd_barangay',
                'user_addresses.uadd_city',
                'user_addresses.uadd_province',
                'user_addresses.uadd_region',
                'addresses.add_name'
            )
            ->first();

        $pestTypes = DB::table('service_order_pests')
            ->leftJoin('service_packages', 'service_order_pests.svcp_id', '=', 'service_packages.svcp_id')
            ->where('service_order_pests.svc_id', $svc_id)
            ->where('service_order_pests.svcop_active', 1)
            ->select(
                'service_order_pests.svcop_id',
                'service_packages.svcp_id',
                'service_packages.svcp_pest_type'
            )
            ->get();

        $serviceAreas = DB::table('service_orders')
            ->leftJoin('service_package_areas', 'service_orders.svcpa_id', '=', 'service_package_areas.svcpa_id')
            ->where('service_orders.svc_id', $svc_id)
            ->whereNull('service_orders.svcpat_id')
            ->where('service_orders.svco_active', 1)
            ->select(
                'service_orders.svco_id',
                'service_package_areas.svcpa_id',
                'service_package_areas.svcpa_area',
                'service_package_areas.svcpa_cost'
            )
            ->get();

        $termiteAreas = DB::table('service_orders')
            ->leftJoin('service_package_area_termites', 'service_orders.svcpat_id', '=', 'service_package_area_termites.svcpat_id')
            ->where('service_orders.svc_id', $svc_id)
            ->whereNotNull('service_orders.svcpat_id')
            ->where('service_orders.svco_active', 1)
            ->select(
                'service_orders.svco_id',
                'service_package_area_termites.svcpat_id',
                'service_package_area_termites.svcpat_sqm_details',
                'service_package_area_termites.svcpat_cost'
            )
            ->get();

        $appointmentImages = DB::table('service_appointment_images')
            ->join('service_appointments', 'service_appointment_images.svca_id', '=', 'service_appointments.svca_id')
            ->where('service_appointments.svc_id', $svc_id)
            ->where('service_appointment_images.svcap_active', 1)
            ->select('service_appointment_images.*')
            ->get();

        $approvedDate = null;
        $approvedTimeFrom = null;
        $approvedTimeTo = null;

        $appointment = DB::table('service_appointments')
            ->where('svc_id', $svc_id)
            ->first();

        if ($appointment) {
            $approvedDate = $appointment->svca_approved_date;
            $approvedTimeFrom = $appointment->svca_approved_time_from;
            $approvedTimeTo = $appointment->svca_approved_time_to;
        }

        $dayName = $approvedDate
            ? strtoupper(Carbon::parse($approvedDate)->format('l'))
            : null;

        $technicians = DB::table('users')
            ->where('utyp_id', 2)
            ->where('usr_active', 1)
            ->where('branch_id', $display->branch_id)
            ->orderBy('usr_last_name', 'asc')
            ->select('usr_id', 'usr_first_name', 'usr_last_name')
            ->get()
            ->map(function ($tech) use ($dayName, $approvedDate, $approvedTimeFrom, $approvedTimeTo) {
                $isRestDay = false;
                if ($dayName) {
                    $avail = DB::table('user_availabilities')
                        ->where('usr_id', $tech->usr_id)
                        ->where('uavail_name', $dayName)
                        ->first();
                    $isRestDay = !$avail || $avail->uavail_active == 0;
                }

                $isBusy = false;
                if ($approvedDate && $approvedTimeFrom && $approvedTimeTo) {
                    $conflict = DB::table('service_appointment_schedules')
                        ->join('service_appointments', 'service_appointments.svca_id', '=', 'service_appointment_schedules.svca_id')
                        ->where('service_appointment_schedules.svcas_assigned_to', $tech->usr_id)
                        ->where('service_appointment_schedules.svcas_active', 1)
                        ->where('service_appointments.svca_approved_date', $approvedDate)
                        ->where('service_appointments.svca_approved_time_from', '<', $approvedTimeTo)
                        ->where('service_appointments.svca_approved_time_to', '>', $approvedTimeFrom)
                        ->first();
                    $isBusy = (bool) $conflict;
                }

                $tech->is_rest_day = $isRestDay;
                $tech->is_busy = $isBusy;
                return $tech;
            });

        // Existing schedules for the timeline (all techs, same date)
        $daySchedules = [];
        if ($approvedDate) {
            $rows = DB::table('service_appointment_schedules')
                ->join('service_appointments', 'service_appointments.svca_id', '=', 'service_appointment_schedules.svca_id')
                ->join('services', 'services.svc_id', '=', 'service_appointments.svc_id')
                ->join('users as clients', 'clients.usr_id', '=', 'services.usr_id')
                ->leftJoin('user_addresses', 'service_appointments.uadd_id', '=', 'user_addresses.uadd_id')
                ->leftJoin('users as assigner', 'assigner.usr_id', '=', 'service_appointment_schedules.svcas_assigned_by')
                ->where('service_appointment_schedules.svcas_active', 1)
                ->where('service_appointments.svca_approved_date', $approvedDate)
                ->select(
                    'service_appointment_schedules.svcas_assigned_to',
                    'service_appointment_schedules.svcas_date_assigned',
                    'service_appointment_schedules.svcas_assigned_by',
                    'assigner.usr_first_name as assigner_first_name',
                    'assigner.usr_last_name as assigner_last_name',
                    'service_appointments.svca_approved_time_from',
                    'service_appointments.svca_approved_time_to',
                    'service_appointments.svc_id as appt_svc_id',   // <-- added to identify THIS appointment
                    'clients.usr_first_name',
                    'clients.usr_last_name',
                    'clients.usr_email',
                    'clients.usr_mobile',
                    'user_addresses.uadd_street',
                    'user_addresses.uadd_barangay',
                    'user_addresses.uadd_city',
                    'user_addresses.uadd_province',
                    'user_addresses.uadd_region',
                    'services.svc_km_distance'
                )
                ->get();

            foreach ($rows as $row) {
                $daySchedules[$row->svcas_assigned_to][] = $row;
            }
        }

        $allTechs = $technicians->map(function ($t) {
            return [
                'id' => $t->usr_id,
                'label' => $t->usr_last_name . ', ' . substr($t->usr_first_name, 0, 1) . '.',
                'is_rest' => $t->is_rest_day,
                'is_busy' => $t->is_busy,
            ];
        })->values();

        return view('service_orders.appointments.scheduled.view_scheduled', compact(
            'display',
            'pestTypes',
            'serviceAreas',
            'termiteAreas',
            'appointmentImages',
            'technicians',
            'approvedDate',
            'approvedTimeFrom',
            'approvedTimeTo',
            'daySchedules',
            'allTechs'
        ));
    }

    public function scheduled_appointments_view_update_technician(Request $request, $svc_id)
    {
        $request->validate([
            'svcas_assigned_to' => 'required',
        ]);

        DB::table('service_appointment_schedules')
            ->join('service_appointments', 'service_appointments.svca_id', '=', 'service_appointment_schedules.svca_id')
            ->where('service_appointments.svc_id', $svc_id)
            ->where('service_appointment_schedules.svcas_active', 1)
            ->update([
                'service_appointment_schedules.svcas_assigned_to' => $request->svcas_assigned_to,
                'service_appointment_schedules.svcas_date_modified' => Carbon::now(),
                'service_appointment_schedules.svcas_modified_by' => session('usr_id'),
            ]);

        $serviceOrder = 'SA-' . str_pad($svc_id, 6, '0', STR_PAD_LEFT);

        logUserActivity('Manage Appointments', 'Changed Technician ' . $serviceOrder);

        session()->flash('successMessage', 'Technician changed successfully.');
        return redirect()->back();
    }
    // END SCHEDULED APPOINTMENTS

    // START DELETED APPOINTMENTS
    public function delete_appointment(Request $request, $svc_id)
    {
        $service = DB::table('services')
            ->leftJoin('users', 'services.usr_id', '=', 'users.usr_id')
            ->where('services.svc_id', '=', $svc_id)
            ->select(
                'services.svc_id',
                'users.usr_first_name',
                'users.usr_last_name'
            )
            ->first();

        if (!$service) {
            alert()->error('Service not found.');
            return redirect()->back();
        }

        DB::table('services')
            ->where('svc_id', '=', $svc_id)
            ->update([
                'svc_date_modified' => Carbon::now(),
                'svc_modified_by' => session('usr_id'),
                'svc_active' => 0
            ]);

        logUserActivity(
            'Manage Appointments',
            'Deleted appointment of ' . $service->usr_first_name . ' ' . $service->usr_last_name
        );

        session()->flash('successMessage', 'Appointment has been deleted.');
        return redirect()->back();
    }
}