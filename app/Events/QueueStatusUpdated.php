<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class QueueStatusUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $activePejabat; // Data user aktif
    public $queues;        // Data antrean (array)

    /**
     * Create a new event instance.
     *
     * @param User $activePejabat
     * @param \Illuminate\Support\Collection|array $queues
     */
    public function __construct(User $activePejabat, $queues)
    {
        $this->activePejabat = [
            'id' => $activePejabat->id,
            'name' => $activePejabat->name,
            'role' => $activePejabat->role, // misal: 'dosen' atau 'mahasiswa'
            'is_active_queue' => $activePejabat->is_active_queue,
        ];

        // Pastikan queues berupa array JSON-friendly
        $this->queues = $queues->map(function($q) {
            return [
                'id' => $q->id,
                'status' => $q->status,
                'dosen' => $q->dosen ? [
                    'id' => $q->dosen->id,
                    'name' => $q->dosen->name,
                ] : null,
                'mahasiswa' => $q->mahasiswa ? [
                    'id' => $q->mahasiswa->id,
                    'name' => $q->mahasiswa->nama,
                ] : null,
                'service' => $q->service ? [
                    'id' => $q->service->id,
                    'nama_layanan' => $q->service->nama_layanan,
                ] : null,
            ];
        })->toArray();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * Gunakan nama channel sesuai di JS (misal: 'display')
     */
    public function broadcastOn(): Channel
    {
        return new Channel('display');
    }

    /**
     * Nama event untuk JS
     */
    public function broadcastAs(): string
    {
        return 'queue.updated';
    }
}
