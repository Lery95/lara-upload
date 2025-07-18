<?php

namespace App\Events;

use App\Models\Upload;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UploadStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $upload;

    public function __construct(Upload $upload)
    {
        $this->upload = $upload;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('uploads');
    }

    // public function broadcastAs(): string
    // {
    //     return 'UploadStatusUpdated'; // ✅ Event name on frontend
    // }

    public function broadcastWith()
    {
        return [
            'id' => $this->upload->id,
            'filename' => $this->upload->filename,
            'status' => $this->upload->status,
            'created_at' => $this->upload->created_at->toDateTimeString(),
            'time_ago' => $this->upload->created_at->diffForHumans(),
        ];
    }
}

