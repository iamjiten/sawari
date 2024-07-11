<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'path' => $this->getUrl(),
            'diskPath' => $this->getDiskPath(),
            'caption' => $this->caption,
            'size' => $this->size,
            'mime_type' => $this->mime_type,
            'extension' => $this->extension,
            'aggregate_type' => $this->aggregate_type,
            'disk' => $this->disk,
            'directory' => $this->directory,
            'filename' => $this->filename,
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($this->pivot ? ($this->pivot->created_at ?: $this->created_at) : $this->created_at)->toDayDateTimeString(),
            'id' => $this->id,
            'reference_name' => $this->reference_name,
            'alt_text' => $this->alt_text,
            'pivot' => $this->pivot ?? null,
        ];
    }
}
