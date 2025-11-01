<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Queue;
use App\Models\User;
use App\Events\QueueStatusUpdated;
use App\Models\Service;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Logika redirect berdasarkan role
        switch ($user->role) {
            case 'admin':
                return view('admin.dashboard', compact('user'));
            case 'pejabat':
                // pejabat diarahkan ke dashboard dosen
                return view('dosen.dashboard', compact('user'));
            case 'dosen':
            case 'mahasiswa':
                // dosen dan mahasiswa ke dashboard mahasiswa
                return view('mahasiswa.dashboard', compact('user'));
            default:
                abort(403, 'Role tidak dikenali');
        }

       
    }
    public function joinQueue(Request $request)
    {
        $student = User::where('role','mahasiswa')->first();

        $request->validate([
            'dean_id'=>'required|exists:users,id',
            'service_id'=>'required'
        ]);

        $lastQueue = Queue::where('dosen_id',$request->dean_id)->latest('nomor_antrian')->first();
        $nomor = $lastQueue ? $lastQueue->nomor_antrian+1 : 1;

        $queue = Queue::create([
            'kode_user'=>$student->kode,
            'dosen_id'=>$request->dean_id,
            'service_id'=>$request->service_id,
            'nomor_antrian'=>$nomor,
            'status'=>'menunggu'
        ]);

        $queue->load(['dosen','service','mahasiswa']);

        broadcast(new QueueStatusUpdated($queue))->toOthers();

        return response()->json(['status'=>'ok','queue'=>$queue]);
    }

    // Dosen buka/tutup antrean
    public function toggleQueue(Request $request)
    {
        // contoh pejabat default
        $user = User::where('role','dekan')->first();

        // reset semua active queue
        User::where('is_active_queue',true)->update(['is_active_queue'=>false]);

        $user->is_active_queue = !$user->is_active_queue;
        $user->save();

        $activePejabat = User::where('is_active_queue',true)->first();
        $queues = Queue::with(['dosen','mahasiswa','service'])->get()->map(function($q){
            return [
                'id'=>$q->id,
                'status'=>$q->status,
                'dosen'=>$q->dosen?['id'=>$q->dosen->id,'name'=>$q->dosen->name]:null,
                'mahasiswa'=>$q->mahasiswa?['id'=>$q->mahasiswa->id,'name'=>$q->mahasiswa->name]:null,
                'service'=>$q->service?['id'=>$q->service->id,'name'=>$q->service->nama_layanan]:null
            ];
        });

        broadcast(new QueueStatusUpdated($activePejabat,$queues))->toOthers();

        return response()->json(['status'=>'ok','activePejabat'=>$activePejabat,'queues'=>$queues]);
    }
}
