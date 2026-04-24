<?php

namespace Tests\Feature;

use App\Models\Queue;
use App\Models\RuangAntri;
use App\Models\Service;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DisplayAndReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_display_pages_are_accessible(): void
    {
        $this->get(route('display'))->assertOk();
        $this->get(route('display.queues'))->assertOk();
        $this->get(route('display.refresh'))->assertOk();
    }

    public function test_admin_dashboard_and_reports_are_accessible(): void
    {
        $admin = $this->makeUser('ADM001', 'admin');

        $this->actingAs($admin)->get(route('adm'))->assertOk();
        $this->actingAs($admin)->get(route('reports.daily'))->assertOk();
        $this->actingAs($admin)->get(route('reports.services'))->assertOk();
        $this->actingAs($admin)->get(route('reports.rekap'))->assertOk();
    }

    public function test_queue_display_api_returns_active_data(): void
    {
        [$pejabat, $service] = $this->seedActiveQueueData();
        $mahasiswa = $this->makeUser('MHS001', 'mahasiswa');

        Queue::create([
            'kode_user' => $mahasiswa->kode,
            'kode_dosen' => $pejabat->kode,
            'service_id' => $service->id,
            'nomor_antrian' => 1,
            'status' => 'menunggu',
        ]);

        $this->getJson(route('display.queues'))
            ->assertOk()
            ->assertJsonStructure([
                'queues',
                'active_staff',
                'dosen_queue_summary',
            ]);
    }

    private function seedActiveQueueData(): array
    {
        $pejabat = $this->makeUser('PJB001', 'pejabat');
        $service = $this->makeService('Legalisir');

        SystemSetting::updateOrCreate(
            ['id' => 1],
            [
                'queue_status' => 'closed',
                'center_latitude' => -6.9731000,
                'center_longitude' => 110.4120000,
                'radius_meters' => 300,
            ]
        );

        RuangAntri::create([
            'kode_dosen' => $pejabat->kode,
            'service_id' => $service->id,
            'status_ruang' => 'open',
            'tanggal_buka_ruang_antri' => now('Asia/Jakarta')->toDateString(),
            'jam_buka_ruang_antri' => now('Asia/Jakarta')->format('H:i:s'),
            'expected_jam_buka_ruang_antri' => now('Asia/Jakarta')->format('H:i:s'),
            'expected_jam_tutup_ruang_antri' => '23:59:00',
        ]);

        return [$pejabat, $service];
    }

    private function makeUser(string $kode, string $role): User
    {
        return User::create([
            'kode' => $kode,
            'name' => 'User ' . $kode,
            'email' => strtolower($kode) . '@example.com',
            'password' => Hash::make('password'),
            'role' => $role,
            'status' => 'aktif',
            'jabatan' => $role === 'pejabat' ? 'Kepala Dekanat' : null,
            'ruangan' => $role === 'pejabat' ? 'Ruang 1' : null,
        ]);
    }

    private function makeService(string $name): Service
    {
        return Service::create([
            'nama_layanan' => $name,
            'deskripsi' => $name . ' service',
            'status' => 'aktif',
            'est' => 10,
        ]);
    }
}
