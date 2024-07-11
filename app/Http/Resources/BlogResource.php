<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'icon' => $this->media->first() ? $this->media->first()->getUrl() : "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQQW-leymuAPn7liA_za4WhHiYI9mth23SgNfS_1s3XgO3QmY-rhIwzLvJ6cUvvzF6nR1s&usqp=CAU",
            'body' => $this->body,
            'status' => $this->status,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
