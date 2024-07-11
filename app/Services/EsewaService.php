<?php

namespace App\Services;

use App\Enums\OrderTrackEnum;
use App\Enums\RentalOrderStatusEnum;
use App\Enums\SettlementChannelEnum;
use App\Enums\TransactionChannelEnum;
use App\Enums\TransactionStatusEnum;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EsewaService
{

    public
    function payment_failed($order, $response)
    {
        $transaction = $order->transaction;
        $transaction->updateTransaction(
            TransactionChannelEnum::Esewa->value,
            TransactionStatusEnum::Failed->value,
            $response
        );

        return response()->json([
            'data' => null,
            'message' => 'transaction updated',
            'code' => 200
        ]);
    }

    public function payment_success($order, $response)
    {
        $transaction = $order->transaction;
        $verified = $this->verify_payment($transaction, $response);
        if ($verified) {
            try {
                $user_id = $order->vehicles->first()?->user_id;
                // create settlement here
                $settlementService = new SettlementRentalService();
                $settlement = $settlementService->createSettlement([
                    "type" => "DR",
                    "channel" => SettlementChannelEnum::Esewa->value,
                    "user_id" => $user_id,
                    "actual_amount" => $order->net_amount
                ]);

                // update user wallet here
                $previousWallet = Wallet::query()
                    ->where('user_id', $settlement->user_id)
                    ->orderBy('id', 'desc')
                    ->first();

                $previousTotalAmount = $previousWallet ? $previousWallet->total_amount : 0;
                $settlement->wallet()->create([
                    "type" => 'CR',
                    "user_id" => $settlement->user_id,
                    "amount" => $settlement->settlement_amount,
                    "total_amount" => $previousTotalAmount + $settlement->settlement_amount
                ]);

                $transaction->updateTransaction(
                    TransactionChannelEnum::Esewa->value,
                    TransactionStatusEnum::Completed->value,
                    $response
                );

                $order->update([
                    'status' => RentalOrderStatusEnum::Booked->value
                ]);

                addToOrderTrack($order, ['old' => RentalOrderStatusEnum::Assigned->name, 'new' => RentalOrderStatusEnum::Booked->name], OrderTrackEnum::Status->value);


                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e);
                return $this->somethingWentWrong($e);
            }

            return response()->json([
                'data' => $order,
                'message' => 'transaction completed',
                'code' => 200
            ]);
        } else {
            $transaction->updateTransaction(
                TransactionChannelEnum::Esewa->value,
                TransactionStatusEnum::Ambiguous->value,
                $response
            );

            return response()->json([
                'data' => null,
                'message' => 'ambiguous transaction',
                'code' => 500
            ]);
        }


    }

    public function verify_payment($transaction, $response): bool
    {
        if ($transaction->amount == $response['totalAmount'] && $transaction->pid == $response['productId']) {
            return true;
        };
        return false;
    }
}
