<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Queue;
use App\Events\QueueStatusUpdated;

class QueueController extends Controller
{
    
   public function toggleQueue(Request $request)
{
    $user = auth()->user(); // pejabat/dosen

    // Reset semua user yang sedang aktif antrean
    User::where('is_active_queue', true)->update(['is_active_queue' => false]);

    // Set user ini aktif
    $user->is_active_queue = !$user->is_active_queue;
    $user->save();

    // Ambil user yang sekarang aktif (pejabat/dosen)
    $activePejabat = User::where('is_active_queue', true)->first();

    // Ambil semua antrean beserta relasinya dan convert ke array yang aman untuk broadcast
    $queues = Queue::with(['dosen','mahasiswa','service'])->get()->map(function($q){
        return [
            'id' => $q->id,
            'status' => $q->status,
            'dosen' => $q->dosen ? ['id'=>$q->dosen->id,'name'=>$q->dosen->name] : null,
            'mahasiswa' => $q->mahasiswa ? ['id'=>$q->mahasiswa->id,'name'=>$q->mahasiswa->nama] : null,
            'service' => $q->service ? ['id'=>$q->service->id,'nama_layanan'=>$q->service->nama_layanan] : null,
        ];
    });

    // Broadcast ke frontend
    broadcast(new QueueStatusUpdated($activePejabat, $queues))->toOthers();

    return response()->json(['status' => 'ok', 'activePejabat' => $activePejabat]);
}

}
