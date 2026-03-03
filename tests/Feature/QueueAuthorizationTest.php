<?php

namespace Tests\Feature;

use App\Models\Queue;
use App\Models\RuangAntri;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class QueueAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_pejabat_can_toggle_queue(): void
    {
        $pejabat = $this->makeUser('PJB001', 'pejabat');
        $service = Service::create([
            'nama_layanan' => 'Legalisir',
            'deskripsi' => 'Layanan legalisir',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($pejabat)->postJson(route('queue.toggle'), [
            'status' => 'open',
            'service_scope' => 'single',
            'service_id' => $service->id,
            'expected_jam_tutup' => '23:59',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'queue_status' => 'open',
                'kode_dosen' => $pejabat->kode,
            ]);
    }

    public function test_non_pejabat_cannot_toggle_queue(): void
    {
        $service = Service::create([
            'nama_layanan' => 'Konsultasi',
            'deskripsi' => 'Layanan konsultasi',
            'status' => 'aktif',
        ]);

        $payload = [
            'status' => 'open',
            'service_scope' => 'single',
            'service_id' => $service->id,
            'expected_jam_tutup' => '23:59',
        ];

        $dosen = $this->makeUser('DSN001', 'dosen');
        $this->actingAs($dosen)
            ->postJson(route('queue.toggle'), $payload)
            ->assertForbidden();

        $mahasiswa = $this->makeUser('MHS001', 'mahasiswa');
        $this->actingAs($mahasiswa)
            ->postJson(route('queue.toggle'), $payload)
            ->assertForbidden();
    }

    public function test_non_pejabat_cannot_call_or_complete_queue(): void
    {
        $pejabat = $this->makeUser('PJB001', 'pejabat');
        $mahasiswa = $this->makeUser('MHS001', 'mahasiswa');

        $service = Service::create([
            'nama_layanan' => 'Administrasi',
            'deskripsi' => 'Layanan administrasi',
            'status' => 'aktif',
        ]);

        $queue = Queue::create([
            'kode_user' => $mahasiswa->kode,
            'kode_dosen' => $pejabat->kode,
            'service_id' => $service->id,
            'nomor_antrian' => 1,
            'status' => 'menunggu',
        ]);

        $dosenPengantre = $this->makeUser('DSN001', 'dosen');
        $this->actingAs($dosenPengantre)
            ->postJson(route('queue.call', $queue))
            ->assertForbidden();

        $this->actingAs($mahasiswa)
            ->postJson(route('queue.complete', $queue))
            ->assertForbidden();
    }

    public function test_only_mahasiswa_or_dosen_can_join_queue(): void
    {
        $pejabat = $this->makeUser('PJB001', 'pejabat');
        $admin = $this->makeUser('ADM001', 'admin');
        $dosenPengantre = $this->makeUser('DSN001', 'dosen');

        $service = Service::create([
            'nama_layanan' => 'Surat',
            'deskripsi' => 'Layanan surat',
            'status' => 'aktif',
        ]);

        RuangAntri::create([
            'kode_dosen' => $pejabat->kode,
            'service_id' => $service->id,
            'status_ruang' => 'open',
            'tanggal_buka_ruang_antri' => now('Asia/Jakarta')->toDateString(),
            'jam_buka_ruang_antri' => now('Asia/Jakarta')->format('H:i:s'),
            'expected_jam_buka_ruang_antri' => now('Asia/Jakarta')->format('H:i:s'),
            'expected_jam_tutup_ruang_antri' => '23:59:00',
        ]);

        $payload = [
            'dean_id' => $pejabat->kode,
            'service_id' => $service->id,
        ];

        $this->actingAs($admin)
            ->postJson(route('queue.join'), $payload)
            ->assertForbidden();

        $this->actingAs($dosenPengantre)
            ->postJson(route('queue.join'), $payload)
            ->assertOk()
            ->assertJson(['status' => 'ok']);
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
}
