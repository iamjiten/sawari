<?php

namespace App\Http\Controllers;

use App\Enums\SettlementChannelEnum;
use App\Exceptions\ApiResponder;
use App\Http\Controllers\V1\SuperController;
use App\Http\Requests\RiderSettlementRequest;
use App\Http\Resources\SettlementResource;
use App\Models\Settlement;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettlementController extends SuperController
{
    use ApiResponder;

    public function __construct()
    {
        parent::__construct(
            Settlement::class,
            SettlementResource::class,
            RiderSettlementRequest::class,
            RiderSettlementRequest::class
        );
    }

    public function store()
    {
        $request = resolve($this->storeRequest);
        $user = User::findOrFail($request->user_id);
        DB::beginTransaction();
        try {
            $latestSettlement = $this->model::where('user_id', $request->user_id)->orderBy('id', 'desc')->first();
            $settlement = $this->model::create([
                "type" => "DR",
                "channel" => SettlementChannelEnum::MANUAL->value,
                "user_id" => $user->id,
                "actual_amount" => $request?->settlement_amount ?? 0,
                "settlement_amount" => $request?->settlement_amount ?? 0,
                "total_earned_amount" => $latestSettlement?->total_earned_amount ?? 0,
                "total_settlement_amount" => ($latestSettlement?->total_settlement_amount ?? 0) - ((float)$request->settlement_amount ?? 0),
            ]);

            $latestWallet = Wallet::where('user_id', $request->user_id)->orderBy('id', 'desc')->first();
            $walletTotalAmount = $latestWallet?->total_amount ?? 0;

            $settlement->wallet()->create([
                "type" => 'CR',
                "user_id" => $settlement->user_id,
                "amount" => $settlement?->settlement_amount ?? 0,
                "total_amount" => $walletTotalAmount + $settlement?->settlement_amount ?? 0
            ]);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->somethingWentWrong($e);
        }

        return $this->success($this->resource::make($settlement), 'settlement created');
    }
}
