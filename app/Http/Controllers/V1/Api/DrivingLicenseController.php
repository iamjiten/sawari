<?php

namespace App\Http\Controllers\V1\Api;

use App\Enums\AcceptRejectEnum;
use App\Enums\KycTypeEnum;
use App\Http\Controllers\V1\SuperController;
use App\Http\Requests\DrivingLicenseRequest;
use App\Http\Resources\DrivingLicenseResource;
use App\Models\DrivingLicense;
use App\Notifications\KycVerificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DrivingLicenseController extends SuperController
{
    public function __construct()
    {
        parent::__construct(
            DrivingLicense::class,
            DrivingLicenseResource::class,
            DrivingLicenseRequest::class,
            DrivingLicenseRequest::class
        );
    }

    public function selfDrivingLicense()
    {
        $drivingLicense = DrivingLicense::where('user_id', auth()->id())->first();
        if ($drivingLicense) {
            return DrivingLicenseResource::make($drivingLicense);

        }
        return $this->error('Driving License not found', 404);
    }

    public function update($id)
    {
        $model = $this->model::findOrFail($id);
        if ($model->status == KycTypeEnum::Reviewing) {
            return [
                'status' => 422,
                'message' => 'Reviewing content cannot be manipulated'
            ];
        }
        return parent::update($id);
    }


    public function changeActionStatus($id, $status)
    {
        if (!in_array($status, [1, 2, 3, 4])) {
            return $this->error('Status not found', 500);
        }
        try {
            DB::beginTransaction();
            $licence = $this->model::findOrFail($id);
            if ($status == 3) {
                $data = [
                    'status' => $status,
                    'remarks' => \request()->remark
                ];
            } else {
                $data = [
                    'status' => $status
                ];
                $licence->user->notify(new KycVerificationNotification($licence, false));
            }
            $licence->update($data);
            DB::commit();

            return $this->success(['status' => true], 'Status change successfully');
        } catch (Exception $e) {
            DB::rollBack();

            return $this->somethingWentWrong($e, 'Failed to change status');
        }
    }
}
