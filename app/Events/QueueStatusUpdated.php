<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class QueueStatusUpdated implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public string $userKode;
    public string $queueStatus;
    public string $queueStatusLabel;
    public array $meta;

    public function __construct(string $userKode, string $queueStatus, array $meta = [])
    {
        $this->userKode = $userKode;
        $this->queueStatus = $queueStatus;
        $this->queueStatusLabel = match ($queueStatus) {
            'open' => 'Antrean Dibuka',
            'occupied' => 'Melayani',
            default => 'Antrean Ditutup',
        };
        $this->meta = $meta;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('queue-status');
    }

    public function broadcastAs(): string
    {
        return 'queue.status.updated';
    }
}
