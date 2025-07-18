<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UploadResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'       => $this->id,
            'filename' => $this->filename,
            'status'   => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
            'time_ago'   => $this->created_at->diffForHumans(),
        ];
    }
}

