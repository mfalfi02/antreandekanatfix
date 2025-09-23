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
        // Ambil semua antrean dengan relasi mahasiswa, dosen, dan service
        $queues = Queue::with(['mahasiswa', 'dosen', 'service'])->get();

        // Ambil semua dosen
        $dosens = Dosen::all();

        return view('display.index', compact('queues', 'dosens'));
    }
}
