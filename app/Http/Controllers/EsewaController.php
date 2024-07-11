<?php

namespace App\Http\Controllers;


use App\Http\Requests\EsewaRequest;
use App\Models\MoverOrder;
use App\Models\Order;
use App\Models\RentalOrder;
use App\Services\EsewaService;

class EsewaController extends Controller
{
    public function __invoke(EsewaService $esewaService, EsewaRequest $request)
    {
        $request = $request->validated();
        $module = $this->getModule($request['module']);
        $order = $module::find($request['order_id']);

        if ($order) {
            //if transaction not verified use this code
//        return $esewaService->verify_payment($request, $order);

            // if already verified transaction use below code
            if (isset($request['payment_response'])) {
                return $esewaService->payment_success($order, $request['payment_response']);
            }
            return $esewaService->payment_failed($order, $request['payment_response']);
        }
        return response()->json([
            'status' => 404,
            'message' => 'order not found',
            'data' => null
        ], 404);


    }

    public function getModule($module): string
    {
        return match ($module) {
            'rental' => RentalOrder::class,
            'mover' => MoverOrder::class,
            'package' => Order::class
        };
    }
}
