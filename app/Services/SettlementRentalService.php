<?php

namespace App\Services;

use App\Models\Settlement;
use App\Models\Trip;

class SettlementRentalService
{
    private int $settlementPercentage = 10;

    public function createSettlement(array $data)
    {
        $oldSettlementData = Settlement::where('user_id', @$data['user_id'])->orderBy('id', 'desc')->first();
        $oldTotalEarnedAmount = $oldSettlementData ? $oldSettlementData->total_earned_amount : 0;
        $oldTotalSettlementAmount = $oldSettlementData ? $oldSettlementData->total_settlement_amount : 0;

        $settlementAmount = $this->calculateSettlementAmount($data['actual_amount']);
        $earned_amount = $data['actual_amount'] - $settlementAmount;

        $additionalData = [
            "settlement_amount" => $settlementAmount,
            "settlement_percentage" => $this->settlementPercentage,
            "earned_amount" => $earned_amount,
            "total_earned_amount" => $oldTotalEarnedAmount + $earned_amount,
            "total_settlement_amount" => $oldTotalSettlementAmount - $settlementAmount
        ];
        $data = array_merge($data, $additionalData);

        return Settlement::create($data);

    }

    public function calculateSettlementAmount($actual_amount): float
    {
        return ($this->settlementPercentage / 100) * $actual_amount;
    }

}
