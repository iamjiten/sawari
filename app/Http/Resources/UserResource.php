<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
//        $notificationCount = auth()->user()->unreadNotifications->count();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'merchant_id' => $this->merchant_id,
            'merchant' => $this->merchant,
            'mobile' => $this->mobile,
            'image' => $this->getMedia('profile')->first() ? $this->getMedia('profile')->first()->getUrl() : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT8lCCz3naP8eDnFr476DeuRV8FvxmratoD6q-yuxjKsMu-0DQCKMzH5PeTAk6qB7UyxmE&usqp=CAU',
            'email' => $this->email,
            'gender' => $this->gender,
            'ask_to_rate_trip' => $this->rateTrip?->only('id', 'order_id', 'user_id', 'amount', 'status', 'action_by', 'extra'),
            'dob' => $this->dob,
            'is_online' => $this->is_online,
            'last_seen' => $this->last_seen,
            'address' => $this->address,
            'latitude' => (double)$this->latitude,
            'longitude' => (double)$this->longitude,
            'status' => $this->status,
            'status_parsed' => $this->status?->name,
            'kyc_status' => $this->kyc_status,
            'type' => $this->type,
            'type_parsed' => $this->type?->name,
            'wallet' => count($this->wallet) > 0 ? $this?->wallet()?->orderBy('id', 'desc')?->first()?->total_amount : 0,
//            'unread_notification_count' => $notificationCount,
            'role' => $this->roles->first()?->id,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
