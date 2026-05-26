<?php

namespace App\Http\Controllers\api\v1\mobile_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Controllers\api\v1\mobile_controllers\MobileMapboxDistanceController;

class MobileServiceAppointmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'uadd_id' => 'required',
            'client_date' => 'required|date',
            'client_time' => 'required',
            'initial_price' => 'nullable|numeric',
            'service_packages' => 'required',
            'service_areas' => 'nullable',
            'is_termite' => 'nullable|in:0,1',
            'termite_sqm_id' => 'nullable|integer',
            'images.*' => 'mimes:jpeg,jpg,png,webp|max:8192',
            'termite_sqm_input' => 'nullable|numeric|min:1',
        ]);

        DB::beginTransaction();

        try {
            $user = DB::table('users')
                ->where('usr_email', $request->email)
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.',
                ], 404);
            }

            $servicePackages = json_decode($request->service_packages, true) ?? [];
            $serviceAreas = json_decode($request->service_areas, true) ?? [];
            $isTermite = $request->is_termite == 1;
            $termiteSqmId = $request->termite_sqm_id;
            $termiteSqmInput = $request->filled('termite_sqm_input')
                ? (float) $request->termite_sqm_input
                : null;

            $initialPrice = $request->filled('initial_price')
                ? (float) $request->initial_price
                : 0;

            if ($isTermite) {
                if (!$termiteSqmInput || !$termiteSqmId) {
                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => 'Termite property size is required.',
                    ], 400);
                }

                $termiteRate = DB::table('service_package_area_termites')
                    ->where('svcpat_id', $termiteSqmId)
                    ->where('svcpat_active', 1)
                    ->first();

                if (!$termiteRate) {
                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid termite property size range.',
                    ], 400);
                }

                $rate = (float) $termiteRate->svcpat_cost;

                if ($termiteSqmInput >= 1 && $termiteSqmInput <= 50) {
                    $initialPrice = $rate;
                } else {
                    $initialPrice = $termiteSqmInput * $rate;
                }
            }
            $address = DB::table('user_addresses')
                ->where('uadd_id', $request->uadd_id)
                ->where('usr_id', $user->usr_id)
                ->first();

            if (!$address || !$address->uadd_latitude || !$address->uadd_longitude) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Selected address has no pinned map location.',
                ], 400);
            }

            $nearestBranch = null;
            $nearestDistance = null;

            $branches = DB::table('branches')
                ->whereNotNull('branch_latitude')
                ->whereNotNull('branch_longitude')
                ->get();

            foreach ($branches as $branch) {
                $distance = $this->calculateDistanceKm(
                    $address->uadd_latitude,
                    $address->uadd_longitude,
                    $branch->branch_latitude,
                    $branch->branch_longitude
                );

                if ($nearestDistance === null || $distance < $nearestDistance) {
                    $nearestDistance = $distance;
                    $nearestBranch = $branch;
                }
            }

            if (!$nearestBranch) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'No available branch found for this location.',
                ], 400);
            }

            $drivingDistance = MobileMapboxDistanceController::getDrivingDistanceKm(
                $nearestBranch->branch_longitude,
                $nearestBranch->branch_latitude,
                $address->uadd_longitude,
                $address->uadd_latitude
            );

            $finalDistance = $drivingDistance ?? $nearestDistance;

            $locationRate = DB::table('service_package_area_locations')
                ->where('branch_id', $nearestBranch->branch_id)
                ->where('svcpal_active', 1)
                ->first();

            if (!$locationRate) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'No location pricing found for this branch.',
                ], 400);
            }

            $baseKm = 10;
            $baseCost = (float) $locationRate->svcpal_first_cost;
            $succeedingCost = (float) $locationRate->svcpal_succeeding_cost;
            $distanceKm = (int) $finalDistance;

            if ($distanceKm <= $baseKm) {
                $deliveryFee = $baseCost;
            } else {
                $excessKm = $distanceKm - $baseKm;
                $deliveryFee = $baseCost + ($excessKm * $succeedingCost);
            }

            $totalInitialPrice = (float) $initialPrice + $deliveryFee;

            $serviceId = DB::table('services')->insertGetId([
                'svc_uuid' => Str::uuid(),
                'branch_id' => $nearestBranch->branch_id,
                'usr_id' => $user->usr_id,
                'svc_is_package' => count($servicePackages) > 1 ? 1 : 0,
                'svcpat_id' => $isTermite ? $termiteSqmId : null,
                'svc_is_termite' => $isTermite ? 1 : 0,
                'svc_type_treatment' => null,
                'svc_sqm_initial' => $isTermite ? $termiteSqmInput : null,
                'svc_sqm_final' => null,
                'svc_with_device' => null,
                'svc_device_count' => null,
                'svc_problem_description' => strtoupper($request->problem_description),
                'svc_status' => 'REQUESTED',
                'svc_infestation' => null,
                'svc_initial_price' => $totalInitialPrice,
                'svc_km_distance' => $finalDistance,
                'svc_final_price' => null,
                'svc_balance' => $totalInitialPrice,
                'svc_payment_status' => 'NO PAYMENT',
                'svc_attachment' => null,
                'svc_frequency_type' => null,
                'svc_frequency' => null,
                'svc_date_created' => now(),
                'svc_created_by' => $user->usr_id,
                'svc_date_modified' => null,
                'svc_modified_by' => null,
                'svc_active' => 1,
            ]);

            DB::table('services')
            ->where('svc_id', $serviceId)
            ->update([
                'svc_sa_number' => $serviceId,
            ]);

            foreach ($servicePackages as $package) {
                DB::table('service_order_pests')->insert([
                    'svcop_uuid' => Str::uuid(),
                    'svc_id' => $serviceId,
                    'svcp_id' => $package['id'],
                    'svcop_date_created' => now(),
                    'svcop_created_by' => $user->usr_id,
                    'svcop_date_modified' => null,
                    'svcop_modified_by' => null,
                    'svcop_active' => 1,
                ]);
            }

            if ($isTermite && $termiteSqmId) {
                DB::table('service_orders')->insert([
                    'svco_uuid' => Str::uuid(),
                    'svc_id' => $serviceId,
                    'svcpa_id' => null,
                    'svcpat_id' => $termiteSqmId,
                    'svco_date_created' => now(),
                    'svco_created_by' => $user->usr_id,
                    'svco_date_modified' => null,
                    'svco_modified_by' => null,
                    'svco_active' => 1,
                ]);
            } else {
                foreach ($serviceAreas as $area) {
                    DB::table('service_orders')->insert([
                        'svco_uuid' => Str::uuid(),
                        'svc_id' => $serviceId,
                        'svcpa_id' => $area['id'],
                        'svcpat_id' => null,
                        'svco_date_created' => now(),
                        'svco_created_by' => $user->usr_id,
                        'svco_date_modified' => null,
                        'svco_modified_by' => null,
                        'svco_active' => 1,
                    ]);
                }
            }

            $appointmentId = DB::table('service_appointments')->insertGetId([
                'svca_uuid' => Str::uuid(),
                'svc_id' => $serviceId,
                'uadd_id' => $request->uadd_id,
                'svca_client_date' => $request->client_date,
                'svca_client_time' => $request->client_time,
                'svca_status' => 'REQUESTED',
                'svca_approved_date' => null,
                'svca_approved_time_from' => null,
                'svca_approved_time_to' => null,
                'svca_date_approved' => null,
                'svca_approved_by' => null,
                'svca_date_created' => now(),
                'svca_created_by' => $user->usr_id,
                'svca_date_modified' => null,
                'svca_modified_by' => null,
                'svca_active' => 1,
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $fileName = uniqid() . '_' . $image->getClientOriginalName();
                    $folderPath = public_path('images/client_images');

                    if (!file_exists($folderPath)) {
                        mkdir($folderPath, 0755, true);
                    }

                    $path = $folderPath . '/' . $fileName;
                    $ext = strtolower($image->getClientOriginalExtension());

                    if (in_array($ext, ['jpg', 'jpeg'])) {
                        $source = imagecreatefromjpeg($image->getPathname());
                        imagejpeg($source, $path, 75);
                        imagedestroy($source);
                    } elseif ($ext === 'png') {
                        $source = imagecreatefrompng($image->getPathname());

                        if ($source && imageistruecolor($source) === false) {
                            imagepalettetotruecolor($source);
                        }

                        if ($source) {
                            imagepng($source, $path, 7);
                            imagedestroy($source);
                        }
                    } elseif ($ext === 'webp') {
                        $source = imagecreatefromwebp($image->getPathname());
                        imagewebp($source, $path, 75);
                        imagedestroy($source);
                    }

                    DB::table('service_appointment_images')->insert([
                        'svcap_uuid' => Str::uuid(),
                        'svca_id' => $appointmentId,
                        'svcap_image' => $fileName,
                        'svcap_date_created' => now(),
                        'svcap_created_by' => $user->usr_id,
                        'svcap_date_modified' => null,
                        'svcap_modified_by' => null,
                        'svcap_active' => 1,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Service appointment request submitted successfully.',
                'svc_id' => $serviceId,
                'svca_id' => $appointmentId,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Unable to submit service appointment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getTermiteAreaSizes($branchId)
    {
        $data = DB::table('service_package_area_termites')
            ->where('branch_id', $branchId)
            ->where('svcpat_active', 1)
            ->select(
                'svcpat_id',
                'svcpat_sqm_details',
                'svcpat_cost'
            )
            ->orderBy('svcpat_id', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function clientAppointments(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = DB::table('users')
            ->where('usr_email', $request->email)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
                'data' => [],
            ], 404);
        }

        $appointments = DB::table('service_appointments as sa')
            ->join('services as s', 'sa.svc_id', '=', 's.svc_id')
            ->leftJoin('user_addresses as ua', 'sa.uadd_id', '=', 'ua.uadd_id')
            ->leftJoin('service_package_area_termites as spat', 's.svcpat_id', '=', 'spat.svcpat_id')
            ->where('s.usr_id', $user->usr_id)
            ->where('sa.svca_active', 1)
            ->where('s.svc_active', 1)
            ->where('s.svc_status', '!=', 'DELETED')
            ->select(
                'sa.svca_id',
                'sa.svc_id',
                'sa.uadd_id',
                's.svc_status as svca_status',
                'sa.svca_client_date',
                'sa.svca_client_time',
                'sa.svca_date_created',

                's.svc_is_termite',
                's.svc_balance',

                'ua.uadd_street',
                'ua.uadd_barangay',
                'ua.uadd_city',
                'ua.uadd_province',

                'spat.svcpat_sqm_details'
            )
            ->orderBy('sa.svca_date_created', 'desc')
            ->get();
        
        $appointments->transform(function ($appointment) {
            $pests = DB::table('service_order_pests as sop')
                ->join('service_packages as sp', 'sop.svcp_id', '=', 'sp.svcp_id')
                ->where('sop.svc_id', $appointment->svc_id)
                ->where('sop.svcop_active', 1)
                ->where('sp.svcp_active', 1)
                ->pluck('sp.svcp_pest_type')
                ->toArray();

            $appointment->fullPestTypes = implode(', ', $pests);

            return $appointment;
        });

        $appointments->transform(function ($appointment) {
            $pests = DB::table('service_order_pests as sop')
                ->join('service_packages as sp', 'sop.svcp_id', '=', 'sp.svcp_id')
                ->where('sop.svc_id', $appointment->svc_id)
                ->where('sop.svcop_active', 1)
                ->where('sp.svcp_active', 1)
                ->pluck('sp.svcp_pest_type')
                ->toArray();

            $areas = DB::table('service_orders as so')
                ->join('service_package_areas as spa', 'so.svcpa_id', '=', 'spa.svcpa_id')
                ->where('so.svc_id', $appointment->svc_id)
                ->where('so.svco_active', 1)
                ->where('spa.svcpa_active', 1)
                ->pluck('spa.svcpa_area')
                ->toArray();

            $appointment->fullPestTypes = implode(', ', $pests);
            $appointment->areaTypes = implode(', ', $areas);

            return $appointment;
        });

        return response()->json([
            'success' => true,
            'data' => $appointments,
        ]);
    }

    private function calculateDistanceKm($lat1, $lon1, $lat2, $lon2)
    {
        $lat1 = (float) $lat1;
        $lon1 = (float) $lon1;
        $lat2 = (float) $lat2;
        $lon2 = (float) $lon2;

        $earthRadius = 6371;

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
        ));

        return round($earthRadius * $angle);
    }
}