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

    /**
     * Membawa status antrean ke event realtime agar dashboard dan display publik ikut sinkron.
     */
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

    /**
     * Mengirim event ke channel publik queue-status untuk listener realtime.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('queue-status');
    }

    /**
     * Menetapkan nama event agar frontend bisa mendengar perubahan status secara konsisten.
     */
    public function broadcastAs(): string
    {
        return 'queue.status.updated';
    }
}
