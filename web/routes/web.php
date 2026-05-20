<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfilingController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\LocationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ------------------------------------------------------------------------------------------------------------------------------------ //
// LOGIN
Route::get('/', [MainController::class, 'main']);
Route::post('/validate', [LoginController::class, 'validate_user']);
Route::get('logout', [LoginController::class, 'logout']);
// ------------------------------------------------------------------------------------------------------------------------------------ //

// ------------------------------------------------------------------------------------------------------------------------------------ //
// HOME
Route::get('home', [AdminController::class, 'home']);
Route::get('setup', [AdminController::class, 'setup']);
// ------------------------------------------------------------------------------------------------------------------------------------ //

// ------------------------------------------------------------------------------------------------------------------------------------ //
// ANNOUNCEMENTS
Route::post('announcement/save', [AnnouncementController::class, 'save']);
Route::post('announcement/delete/{ann_uuid}', [AnnouncementController::class, 'delete']);
// ------------------------------------------------------------------------------------------------------------------------------------ //

// ------------------------------------------------------------------------------------------------------------------------------------ //
// ACCOUNT
Route::get('account', [UserController::class, 'account']);
Route::post('account/update', [UserController::class, 'account_update']);
Route::post('account/address/add', [UserController::class, 'account_address_add']);
Route::post('account/address/edit', [UserController::class, 'account_address_edit']);
Route::post('account/update/password', [UserController::class, 'account_update_password']);
// ------------------------------------------------------------------------------------------------------------------------------------ //

// ------------------------------------------------------------------------------------------------------------------------------------ //
// MESSAGES
Route::get('messages', [MessageController::class, 'main']);
Route::get('messages/chat/{mesg_group_id}', [MessageController::class, 'personal']);
Route::post('messages/send', [MessageController::class, 'send']);
Route::post('messages/compose', [MessageController::class, 'compose']);
// ------------------------------------------------------------------------------------------------------------------------------------ //

// ------------------------------------------------------------------------------------------------------------------------------------ //
// PROFILING
// - Users
Route::get('profiling/users/active', [ProfilingController::class, 'users_active']);
Route::get('profiling/users/deleted', [ProfilingController::class, 'users_deleted']);
Route::post('profiling/users/add', [ProfilingController::class, 'users_add']);
Route::post('profiling/users/update/role/{usr_id}', [ProfilingController::class, 'users_update_role']);
Route::post('profiling/users/reset/password/{usr_id}', [ProfilingController::class, 'users_reset_password']);
Route::post('profiling/users/delete/{usr_id}', [ProfilingController::class, 'users_delete']);
Route::post('profiling/users/restore/{usr_id}', [ProfilingController::class, 'users_restore']);
// - Technicians
Route::get('profiling/technicians/active', [ProfilingController::class, 'technicians_active']);
Route::get('profiling/technicians/deleted', [ProfilingController::class, 'technicians_deleted']);
Route::post('profiling/technicians/add', [ProfilingController::class, 'technicians_add']);
Route::post('profiling/technicians/update/availability/{usr_id}', [ProfilingController::class, 'technicians_update_availability']);
Route::post('profiling/technicians/reset/password/{usr_id}', [ProfilingController::class, 'technicians_reset_password']);
Route::post('profiling/technicians/delete/{usr_id}', [ProfilingController::class, 'technicians_delete']);
Route::post('profiling/technicians/restore/{usr_id}', [ProfilingController::class, 'technicians_restore']);
// - Clients
Route::get('profiling/clients/active', [ProfilingController::class, 'clients_active']);
Route::get('profiling/clients/deleted', [ProfilingController::class, 'clients_deleted']);
Route::post('profiling/clients/reset/password/{usr_id}', [ProfilingController::class, 'clients_reset_password']);
Route::post('profiling/clients/delete/{usr_id}', [ProfilingController::class, 'clients_delete']);
Route::post('profiling/clients/restore/{usr_id}', [ProfilingController::class, 'clients_restore']);
// ------------------------------------------------------------------------------------------------------------------------------------ //

// ------------------------------------------------------------------------------------------------------------------------------------ //
// SERVICE ORDER
// - Book Appointment
Route::get('service/orders/appointments/clients', [AppointmentController::class, 'clients']);
Route::post('service/orders/appointments/clients/book', [AppointmentController::class, 'clients_book']);
// - Requested Appointments
Route::get('service/orders/appointments/requested', [AppointmentController::class, 'requested_appointments']);
Route::get('service/orders/appointments/requested/{svc_id}', [AppointmentController::class, 'requested_appointments_view']);
Route::post('service/orders/appointments/requested/add/pest_type', [AppointmentController::class, 'requested_appointments_view_add_pest']);
Route::post('service/orders/appointments/requested/delete/pest_type/{svcop_id}', [AppointmentController::class, 'requested_appointments_view_delete_pest']);
Route::post('service/orders/appointments/requested/add/service_order', [AppointmentController::class, 'requested_appointments_view_add_service']);
Route::post('service/orders/appointments/requested/delete/service_order/{svcpa_id}', [AppointmentController::class, 'requested_appointments_view_delete_service']);
Route::post('service/orders/appointments/requested/assess', [AppointmentController::class, 'requested_appointments_view_assess']);
Route::post('service/orders/appointments/requested/assess/confirmation', [AppointmentController::class, 'requested_appointments_view_assess_confirmation']);
// - Assesed Appointments
Route::get('service/orders/appointments/assessed', [AppointmentController::class, 'assessed_appointments']);
Route::get('service/orders/appointments/assessed/{svc_id}', [AppointmentController::class, 'assessed_appointments_view']);
// - Scheduled Appointments
// - Ongoing Appointments
// - Completed Appointments
// - Deleted Appointments
Route::post('service/orders/appointments/delete/{svc_id}', [AppointmentController::class, 'delete_appointment']);
// ------------------------------------------------------------------------------------------------------------------------------------ //

// ------------------------------------------------------------------------------------------------------------------------------------ //
// MANAGEMENT
// - Branches
Route::get('management/branches/active', [ManagementController::class, 'branches_active']);
Route::get('management/branches/deleted', [ManagementController::class, 'branches_deleted']);
Route::post('management/branches/add', [ManagementController::class, 'branches_add']);
Route::post('management/branches/update/{branch_id}', [ManagementController::class, 'branches_update']);
Route::post('management/branches/delete/{branch_id}', [ManagementController::class, 'branches_delete']);
Route::post('management/branches/restore/{branch_id}', [ManagementController::class, 'branches_restore']);
// - Addresses
Route::get('management/addresses/active', [ManagementController::class, 'addresses_active']);
Route::get('management/addresses/deleted', [ManagementController::class, 'addresses_deleted']);
Route::post('management/addresses/add', [ManagementController::class, 'addresses_add']);
Route::post('management/addresses/update/{add_id}', [ManagementController::class, 'addresses_update']);
Route::post('management/addresses/delete/{add_id}', [ManagementController::class, 'addresses_delete']);
Route::post('management/addresses/restore/{add_id}', [ManagementController::class, 'addresses_restore']);
// - Services
Route::get('management/services/active', [ManagementController::class, 'services_active']);
Route::post('management/services/area/cost/update/{svcpa_id}', [ManagementController::class, 'services_area_cost_update']);
Route::post('management/services/area/delete/{svcpa_id}', [ManagementController::class, 'services_area_delete']);
Route::post('management/services/area/restore/{svcpa_id}', [ManagementController::class, 'services_area_restore']);
Route::post('management/services/area/termites/cost/update/{svcpa_id}', [ManagementController::class, 'services_area_termites_cost_update']);
Route::post('management/services/area/device/cost/update/{svcpad_id}', [ManagementController::class, 'services_area_device_cost_update']);
Route::post('management/services/area/location/cost/update/{svcpal_id}', [ManagementController::class, 'services_area_location_cost_update']);
// - Logins
Route::get('histories/logins', [ManagementController::class, 'login_histories']);
// - Users
Route::get('histories/users', [ManagementController::class, 'user_histories']);
// ------------------------------------------------------------------------------------------------------------------------------------ //

// ------------------------------------------------------------------------------------------------------------------------------------ //
// LOCATION
Route::get('/locations/{region}/provinces', [LocationController::class, 'provinces']);
Route::get('/locations/{province}/municipalities', [LocationController::class, 'municipalities']);
Route::get('/locations/{muni}/barangays', [LocationController::class, 'barangays']);
// ------------------------------------------------------------------------------------------------------------------------------------ //

// ------------------------------------------------------------------------------------------------------------------------------------ //
// LARAVEL COMMANDS //
Route::get('/laravel/clear-all', function () {
    $commands = [
        'cache:clear',
        'view:clear',
        'route:clear',
        'config:clear',
        'config:cache',
    ];

    foreach ($commands as $command) {
        Artisan::call($command);
    }

    return response()->json(['message' => 'All caches and configurations cleared successfully!']);
});
// ------------------------------------------------------------------------------------------------------------------------------------ //