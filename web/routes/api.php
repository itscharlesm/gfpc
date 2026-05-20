    <?php

    use App\Http\Controllers\api\v1\ActivityController;
    use App\Http\Controllers\api\v1\AnnouncementController;
    use App\Http\Controllers\api\v1\ArticleController;
    use App\Http\Controllers\api\v1\AuthController;
    use App\Http\Controllers\api\v1\GoogleController;
    use App\Http\Controllers\api\v1\InnovationController;
    use App\Http\Controllers\api\v1\MailController;
    use App\Http\Controllers\api\v1\NewsController;
    use App\Http\Controllers\api\v1\ProfileController;
    use App\Http\Controllers\api\v1\mobile_controllers\MobileSignUpController;
    use App\Http\Controllers\api\v1\mobile_controllers\MobileAuthController;
    use App\Http\Controllers\api\v1\mobile_controllers\MobileProfileController;
    use App\Http\Controllers\api\v1\mobile_controllers\MobileLocationController;
    use App\Http\Controllers\api\v1\mobile_controllers\MobileAddressController;
    use App\Http\Controllers\api\v1\mobile_controllers\MobileServicePackageController;
    use App\Http\Controllers\api\v1\mobile_controllers\MobileServiceAppointmentController;
    use App\Models\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;

    /*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider and all of them will
    | be assigned to the "api" middleware group. Make something great!
    |
    */

    // Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    //     return $request->user();
    // });


    //////////////////////
    //                  //
    //  PUblic Routes   //
    //                  //
    //////////////////////

    // Registration Routes
    Route::get('/is_email_available/{email}', [AuthController::class, 'is_email_available']);
    Route::post('/is_confirmation_code_valid', [AuthController::class, 'is_confirmation_code_valid']);
    Route::post('/register', [AuthController::class, 'register']);

    // Forgot Password Routes
    Route::get('/send_password_reset_code/{email}', [AuthController::class, 'send_password_reset_code']);
    Route::post('/is_password_reset_code_valid', [AuthController::class, 'is_password_reset_code_valid']);
    Route::post('/create_new_password', [AuthController::class, 'create_new_password']);

    // Login
    Route::post('/login', [AuthController::class, 'login']);

    /////////////////////////
    //                     //
    //  Protected Routes   //
    //                     //
    /////////////////////////
    Route::group(['middleware' => ['auth:sanctum']], function () {

        // Get user data by token
        Route::get('/get_user_data/token/', [AuthController::class, 'get_user_data']);

        // Innovations
        Route::post('/post_innovation', [InnovationController::class, 'post_innovation']);
        Route::post('/edit_innovation', [InnovationController::class, 'edit_innovation']);
        Route::get('/get_innovations/{user_id}', [InnovationController::class, 'get_innovations']);
        Route::get('view_innovation/{id}', [InnovationController::class, 'view_innovation']);
        Route::post('/delete_innovation', [InnovationController::class, 'delete_innovation']);

        // Articles
        Route::get('/get_articles/{user_id}', [ArticleController::class, 'get_articles']);
        Route::get('/get_article_comments/{article_id}', [ArticleController::class, 'get_article_comments']);
        Route::post('/post_article_comment', [ArticleController::class, 'post_comment']);
        Route::post('/like_article', [ArticleController::class, 'like_article']);
        Route::patch('/dislike_article', [ArticleController::class, 'dislike_article']);

        // use only if news diay ang articles
        // News
        Route::get('/get_news/{user_id}', [NewsController::class, 'get_news']);
        Route::get('/get_news_comments/{news_id}', [NewsController::class, 'get_news_comments']);
        Route::post('/post_news_comment', [NewsController::class, 'post_comment']);
        Route::post('/like_news', [NewsController::class, 'like_news']);
        Route::patch('/dislike_news', [NewsController::class, 'dislike_news']);

        // Announcements
        Route::get('/get_announcements', [AnnouncementController::class, 'get_announcements']);

        // Activities
        Route::get('/get_activities', [ActivityController::class, 'get_activities']);

        // Profile
        Route::post('/edit_profile', [ProfileController::class, 'edit_profile']);
        Route::post('/check_password', [ProfileController::class, 'check_password']);
        Route::post('/change_password', [ProfileController::class, 'change_password']);

        Route::post('/delete_account', [AuthController::class, 'delete_account']);

        // Logout
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    Route::post('/check_account_match', [GoogleController::class, 'check_account_match']);

    ////////////////////
    //                //
    //  Test Routes   //
    //                //
    ////////////////////
    Route::get('/test', function () {
        return response()->json(User::all());
    });

    Route::get('send-mail', [MailController::class, 'index']);


    Route::get('/yawa', [InnovationController::class, 'yawa']);


    ////////////////////
    //                //
    // Mobile Routes  //
    //                //
    ////////////////////

    // Mobile API Routes
    Route::prefix('mobile')->group(function () {
        Route::post('/register', [MobileSignUpController::class, 'register']);
        Route::post('/login', [MobileAuthController::class, 'login']);
        Route::post('/profile', [MobileProfileController::class, 'getProfile']);
        Route::post('/profile/update', [MobileProfileController::class, 'updateProfile']);
        Route::get('/location/regions', [MobileLocationController::class, 'regions']);
        Route::get('/location/provinces/{reg_id}', [MobileLocationController::class, 'provinces']);
        Route::get('/location/municipalities/{prov_id}', [MobileLocationController::class, 'municipalities']);
        Route::get('/location/barangays/{mun_id}', [MobileLocationController::class, 'barangays']);
        Route::post('/address/store', [MobileAddressController::class, 'store']);
        Route::post('/address/used-types', [MobileAddressController::class, 'usedTypes']);
        Route::post('/address/list', [MobileAddressController::class, 'list']);
        Route::post('/address/update', [MobileAddressController::class, 'update']);
        Route::post('/address/delete', [MobileAddressController::class, 'delete']);
        Route::post('/address/set-primary', [MobileAddressController::class, 'setPrimary']);
        Route::get('/service-packages', [MobileServicePackageController::class, 'list']);
        Route::get('/service-package-areas/{branch_id}', [MobileServicePackageController::class, 'areas']);
        Route::post('/service-appointments/store', [MobileServiceAppointmentController::class, 'store']);
        Route::get('/service-package-area-termites/{branchId}', [MobileServiceAppointmentController::class, 'getTermiteAreaSizes']);
        Route::post('/appointments/client', [MobileServiceAppointmentController::class, 'clientAppointments']);
    });
