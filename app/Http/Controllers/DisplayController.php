<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Service;

class DisplayController extends Controller
{
  public function index()
{
    return view('display.index'); // pastikan Blade ini ada
}

public function queues()
    {
        $queues = Queue::with(['dosen','mahasiswa','service'])->get()->map(function($q){
            return [
                'id' => $q->id,
                'status' => $q->status,
                'dosen' => $q->dosen ? ['id'=>$q->dosen->id, 'name'=>$q->dosen->name] : null,
                'mahasiswa' => $q->mahasiswa ? ['id'=>$q->mahasiswa->id, 'name'=>$q->mahasiswa->nama] : null,
                'service' => $q->service ? ['id'=>$q->service->id,'nama_layanan'=>$q->service->nama_layanan] : null,
            ];
        });

        return response()->json($queues);
    }
public function refresh()
{
    $queues = Queue::with('service', 'mahasiswa', 'dosen')->get();
    return response()->json($queues);
}

}
