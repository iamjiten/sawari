<?php

use App\Http\Controllers\EsewaController;
use App\Http\Controllers\OrderTrackController;
use App\Http\Controllers\V1\Api\AddressBookController;
use App\Http\Controllers\V1\Api\AuthController;
use App\Http\Controllers\V1\Api\CitizenshipController;
use App\Http\Controllers\V1\Api\DeliveryTypeController;
use App\Http\Controllers\V1\Api\DrivingLicenseController;
use App\Http\Controllers\V1\Api\HomeController;
use App\Http\Controllers\V1\Api\ModuleController;
use App\Http\Controllers\V1\Api\MoverOrderController;
use App\Http\Controllers\V1\Api\OrderController;
use App\Http\Controllers\V1\Api\PackageController;
use App\Http\Controllers\V1\Api\PackageSizeController;
use App\Http\Controllers\V1\Api\RentalAreaController;
use App\Http\Controllers\V1\Api\RentalLocationController;
use App\Http\Controllers\V1\Api\RentalOrderController;
use App\Http\Controllers\V1\Api\RentalVehicleController;
use App\Http\Controllers\V1\Api\SettingController;
use App\Http\Controllers\V1\Api\TripController;
use App\Http\Controllers\V1\Api\RatingController;
use App\Http\Controllers\V1\Api\TypeSettingController;
use App\Http\Controllers\V1\Api\UserController;
use App\Http\Controllers\V1\Api\VehicleController;
use App\Http\Controllers\V1\Api\VehicleRequestController;
use App\Http\Controllers\V1\Api\VehicleTypeController;
use App\Http\Controllers\V1\Api\SavedReceiverController;
use App\Models\RentalArea;
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

Route::get('v1/home/banners', [HomeController::class, 'getBanners']);
Route::controller(AuthController::class)->prefix('v1/auth')->group(function () {

    // authentication related routes
    Route::post('generate', 'generate');
    Route::post('verify', 'verify');
    Route::post('signup', 'signup');
});

Route::post('v1/orders/track', [OrderTrackController::class, 'track']);
Route::get('v1/user/delete-user/{id}', [UserController::class, 'deleteUser']);

Route::group(['prefix' => 'v1', 'as' => 'api::', 'middleware' => ['auth:sanctum', 'activeRider']], function () {

    // Vehicle type routes
    Route::prefix('vehicle-type')->group(function () {
        generateRoutes(VehicleTypeController::class, ['index']);
    });


    // Delivery type routes
    Route::prefix('delivery-type')->group(function () {
        generateRoutes(DeliveryTypeController::class, ['index']);
    });

    // Address book routes
    Route::prefix('address-books')->group(function () {
        generateRoutes(AddressBookController::class);
    });

    // Type settings routes
    Route::prefix('type-settings')->group(function () {
        generateRoutes(TypeSettingController::class, ['index']);
    });

    // Package sizes routes
    Route::prefix('package-sizes')->group(function () {
        generateRoutes(PackageSizeController::class, ['index']);
    });

    // Packages routes
    Route::prefix('packages')->group(function () {
        Route::get('self-sender', [PackageController::class, 'selfSender']);
        Route::get('self-receiver', [PackageController::class, 'selfReceiver']);
        generateRoutes(PackageController::class, ['store', 'update', 'delete']);
    });

    // orders routes
    Route::prefix('orders')->group(function () {
        Route::get('self', [OrderController::class, 'self']);
        Route::post('auth-track', [OrderTrackController::class, 'authTrack']);
        Route::get('self-ongoing', [OrderController::class, 'selfOngoing']);
        Route::get('self-activity', [OrderController::class, 'selfActivity']);
        Route::get('receiver-activity', [OrderController::class, 'receiverActivity']);
        Route::get('received', [OrderController::class, 'receivedOrders']);
        Route::post('change-status', [OrderController::class, 'changeOrderStatus']);
        Route::post('retry', [OrderController::class, 'retryOrder']);
        generateRoutes(OrderController::class);
    });

    // citizenship routes for kyc
    Route::prefix('citizenship')->group(function () {
        Route::get('self', [CitizenshipController::class, 'selfCitizenship']);
        generateRoutes(CitizenshipController::class, ['store', 'update']);
    });

    // Driving license routes for kyc
    Route::prefix('license')->group(function () {
        Route::get('self', [DrivingLicenseController::class, 'selfDrivingLicense']);
        generateRoutes(DrivingLicenseController::class, ['store', 'update']);
    });

    // Vehicle setting routes for kyc
    Route::prefix('setting')->group(function () {
        generateRoutes(SettingController::class, ['index']);
    });

    // Vehicle routes for kyc
    Route::prefix('vehicle')->group(function () {
        Route::get('self', [VehicleController::class, 'selfVehicle']);
        generateRoutes(VehicleController::class, ['store', 'update']);
    });

    // Trip route for rider
    Route::prefix('trips')->group(function () {
        Route::get('most-trips', [TripController::class, 'mostTrips']);
        Route::get('most-earned', [TripController::class, 'mostEarned']);
        Route::get('rider-activity', [TripController::class, 'riderActivity']);
        Route::get('rider-movers-activity', [TripController::class, 'riderMoverActivity']);
        Route::post('cancel-mover-order', [TripController::class, 'cancelMoversTrip']);
        Route::post('complete-mover-order', [TripController::class, 'completeMoversTrip']);
        Route::post('/', [TripController::class, 'tripHandler']);
        Route::post('/verify-receiver-otp', [TripController::class, 'verifyReceiverOtp']);
        generateRoutes(TripController::class, ['show']);
    });

    // Rating route
    Route::prefix('rating')->group(function () {
        Route::get('top-riders', [RatingController::class, 'topRatedRiders']);
        generateRoutes(RatingController::class, ['store', 'update']);
    });


    // Rider Profile
    Route::prefix('user')->group(function () {
        Route::post('checkMobile', [UserController::class, 'checkMobile']);
        Route::post('change-online-status', [UserController::class, 'changeOnlineStatus']);
        Route::get('profile', [UserController::class, 'profile']);
        Route::get('rider-ratings', [UserController::class, 'riderRatings']);
        Route::get('rider-profile/{id?}', [UserController::class, 'riderProfile']);
        Route::get('cancel-trip-rating', [UserController::class, 'setAskToRateTripToNull']);
        Route::get('get-self-notification', [UserController::class, 'getSelfNotification']);
        Route::get('read-notification/{id}', [UserController::class, 'readNotification']);
        Route::post('rider-location', [UserController::class, 'userLocation']);
        generateRoutes(UserController::class, ['update']);
    });

    // saved receiver route
    Route::prefix('saved-receivers')->group(function () {
        Route::get('self', [SavedReceiverController::class, 'self']);
        generateRoutes(SavedReceiverController::class, ['store', 'update', 'delete']);
    });

    Route::prefix('module')->group(function () {
        generateRoutes(ModuleController::class, ['index']);
    });

    Route::prefix('rental-locations')->group(function () {
        generateRoutes(RentalLocationController::class, ['index']);
    });

    Route::prefix('rental-vehicle')->group(function () {
        Route::post('change-driver-status/{vehicle}', [RentalVehicleController::class, 'changeDriverStatus']);
        Route::get('simple', [RentalVehicleController::class, 'simpleShow']);
        Route::post('search', [RentalVehicleController::class, 'search']);
        Route::get('filters', [RentalVehicleController::class, 'getFilters']);
        generateRoutes(RentalVehicleController::class);
    });

    Route::prefix('rental-order')->group(function () {
        Route::post('change-status', [RentalOrderController::class, 'changeOrderStatus']);
        Route::get('self-activity', [RentalOrderController::class, 'selfActivity']);
        Route::get('self-received', [RentalOrderController::class, 'selfReceivedOrders']);
        Route::get('self-assigned', [RentalOrderController::class, 'selfAssignedOrders']);
        Route::get('self-booked', [RentalOrderController::class, 'selfBookedOrders']);
        Route::post('cancel-order', [RentalOrderController::class, 'cancelRequest']);
        Route::post('create-transaction', [RentalOrderController::class, 'createTransaction']);
        generateRoutes(RentalOrderController::class);
    });

    Route::prefix('mover-order')->group(function () {
        Route::post('change-order-status', [MoverOrderController::class, 'changeOrderStatus']);
        Route::post('check-otp', [MoverOrderController::class, 'checkOtp']);
        Route::get('self-activity', [MoverOrderController::class, 'selfActivity']);
        Route::get('self-ongoing', [MoverOrderController::class, 'selfOngoing']);
        Route::post('cancel-order', [MoverOrderController::class, 'cancelOrderWithoutReason']);
        Route::post('retry', [MoverOrderController::class, 'retryOrder']);
        Route::get('received', [MoverOrderController::class, 'receivedOrders']);
        generateRoutes(MoverOrderController::class);
    });

    Route::prefix('rental-area')->group(function () {
        generateRoutes(RentalAreaController::class, ['index']);
    });

    Route::post('esewa', EsewaController::class);

});
