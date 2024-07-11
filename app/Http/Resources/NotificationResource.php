<?php

namespace App\Http\Resources;

use App\Enums\ModuleEnum;
use App\Models\Citizenship;
use App\Models\DrivingLicense;
use App\Models\Module;
use App\Models\MoverOrder;
use App\Models\Order;
use App\Models\RentalOrder;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        dd($this->read_at);
//        return parent::toArray($request);
        return [
            "id" => $this->id,
            "title" => $this->data['title'],
            "body" => $this->data['body'],
            "description" => $this->data['description'],
            "type" => $this->data['type'],
            "order_id" => $this->data['order_id'],
            "read_at" => ($this->read_at)?Carbon::parse($this->read_at)->format('Y-m-d H:i:s'): null,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }

}
