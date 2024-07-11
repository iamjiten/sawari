<?php

use App\Http\Controllers\SettlementController;
use App\Http\Controllers\V1\Api\ActivityLogController;
use App\Http\Controllers\V1\Api\AddressBookController;
use App\Http\Controllers\V1\Api\AuthController;
use App\Http\Controllers\V1\Api\BlogController;
use App\Http\Controllers\V1\Api\CitizenshipController;
use App\Http\Controllers\V1\Api\DashboardController;
use App\Http\Controllers\V1\Api\DeliveryTypeController;
use App\Http\Controllers\V1\Api\DrivingLicenseController;
use App\Http\Controllers\V1\Api\MediaController;
use App\Http\Controllers\V1\Api\MerchantController;
use App\Http\Controllers\V1\Api\ModuleController;
use App\Http\Controllers\V1\Api\MoverOrderController;
use App\Http\Controllers\V1\Api\OrderController;
use App\Http\Controllers\V1\Api\PackageController;
use App\Http\Controllers\V1\Api\PackageSizeController;
use App\Http\Controllers\V1\Api\PermissionController;
use App\Http\Controllers\V1\Api\RentalAreaController;
use App\Http\Controllers\V1\Api\RentalLocationController;
use App\Http\Controllers\V1\Api\RentalOrderController;
use App\Http\Controllers\V1\Api\RentalVehicleController;
use App\Http\Controllers\V1\Api\RoleController;
use App\Http\Controllers\V1\Api\SettingController;
use App\Http\Controllers\V1\Api\TransactionController;
use App\Http\Controllers\V1\Api\TypeSettingController;
use App\Http\Controllers\V1\Api\UserController;
use App\Http\Controllers\V1\Api\VehicleController;
use App\Http\Controllers\V1\Api\VehicleTypeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Helper function to generate routes for a given controller is use in the below routes
// to call this helper function composer dump-autoload or composer update command is used

Route::controller(AuthController::class)->prefix('v1/auth')->group(function () {
    // authentication related routes
    Route::post('login', [AuthController::class, 'login']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::get('verify-email/{id}/{slug}/{expires}', [AuthController::class, 'verifyEmail']);
    Route::post('is_verified_link/{id}', [AuthController::class, 'isVerifiedLink']);
    Route::post('changePassword/{id}', [AuthController::class, 'changePassword']);
});

Route::group(['prefix' => 'v1', 'as' => 'api::', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/profile', [UserController::class, 'adminProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/dashboard', [DashboardController::class, 'dashboard']);

    // User routes
    Route::prefix('users')->group(function () {
        Route::post('changePassword', [UserController::class, 'changePassword']);
        Route::get('riders/online', [UserController::class, 'onlineRiders']);
        Route::get('riders/on-trip', [UserController::class, 'onTrip']);
        Route::get('riders/analysis', [UserController::class, 'riderAnalysis']);
        Route::get('merchant/analysis', [UserController::class, 'merchantAnalysis']);
        generateRoutes(UserController::class);
    });

    // Vehicle type routes
    Route::prefix('vehicle-type')->group(function () {
        generateRoutes(VehicleTypeController::class);
    });

    // Delivery type routes
    Route::prefix('delivery-type')->group(function () {
        generateRoutes(DeliveryTypeController::class);
    });

    // Blogs routes
    Route::prefix('blogs')->group(function () {
        generateRoutes(BlogController::class);
    });

    // Address book routes
    Route::prefix('address-books')->group(function () {
        generateRoutes(AddressBookController::class);
    });

    // Type settings routes
    Route::prefix('type-settings')->group(function () {
        generateRoutes(TypeSettingController::class);
    });

    // Package sizes routes
    Route::prefix('package-sizes')->group(function () {
        generateRoutes(PackageSizeController::class);
    });

    // Packages routes
    Route::prefix('packages')->group(function () {
        generateRoutes(PackageController::class);
    });

    // Packages routes
    Route::prefix('activities')->group(function () {
        generateRoutes(ActivityLogController::class, ['index']);
    });

    // Roles routes
    Route::prefix('roles')->group(function () {
        generateRoutes(RoleController::class);
    });

    // Roles routes
    Route::prefix('roles')->group(function () {
        generateRoutes(RoleController::class);
    });

    // Permissions routes
    Route::prefix('permissions')->group(function () {
        generateRoutes(PermissionController::class);
    });

    // orders routes
    Route::prefix('orders')->group(function () {
        generateRoutes(OrderController::class);
    });

    // movers orders routes
    Route::prefix('mover-orders')->group(function () {
        generateRoutes(MoverOrderController::class);
    });

    // transactions routes
    Route::prefix('transactions')->group(function () {
        generateRoutes(TransactionController::class);
    });

    // citizenship routes for kyc
    Route::prefix('citizenship')->group(function () {
        Route::post('change/{id}/status/{status}', [CitizenshipController::class, 'changeActionStatus']);
        generateRoutes(CitizenshipController::class);
    });

    // driving license routes for kyc
    Route::prefix('license')->group(function () {
        Route::post('change/{id}/status/{status}', [DrivingLicenseController::class, 'changeActionStatus']);
        generateRoutes(DrivingLicenseController::class);
    });

    // Vehicle routes for kyc
    Route::prefix('vehicle')->group(function () {
        Route::post('change/{id}/status/{status}', [VehicleController::class, 'changeActionStatus']);
        generateRoutes(VehicleController::class, ['index']);
    });

    // Rental Vehicle routes
    Route::prefix('rental-vehicles')->group(function () {
        generateRoutes(RentalVehicleController::class);
        Route::post('{id}/change-available', [RentalVehicleController::class, 'changeActionAvailable']);
    });

    Route::prefix('settings')->group(function () {
        Route::get('{type}/all', [SettingController::class, 'typeAll']);
        generateRoutes(SettingController::class);
    });

    Route::prefix('merchants')->group(function () {
        generateRoutes(MerchantController::class);
    });

    Route::prefix('module')->group(function () {
        generateRoutes(ModuleController::class);
    });

    Route::prefix('rental-locations')->group(function () {
        generateRoutes(RentalLocationController::class);
    });

    Route::prefix('rental-order')->group(function () {
        Route::get('vehicle-requests', [RentalOrderController::class, 'vehicleRequests']);
        Route::post('accept-request', [RentalOrderController::class, 'acceptRequest']);
        Route::post('reject-request', [RentalOrderController::class, 'rejectRequest']);
        Route::post('complete-order', [RentalOrderController::class, 'orderComplete']);
        generateRoutes(RentalOrderController::class);
    });

    Route::prefix('rental-area')->group(function () {
        generateRoutes(RentalAreaController::class);
    });

    Route::prefix('medias')->group(function () {
        Route::post('upload', [MediaController::class, 'uploadFiles']);
        generateRoutes(MediaController::class, ['index', 'delete']);
    });

    Route::prefix('settlement')->group(function () {
        generateRoutes(SettlementController::class, ['index', 'store']);
    });
});
