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

class QueueFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan mahasiswa bisa masuk antrean ketika lokasi masih di dalam radius.
     */
    public function test_mahasiswa_can_join_queue_inside_radius(): void
    {
        [$pejabat, $service] = $this->seedOpenRoomWithGeofence();
        $mahasiswa = $this->makeUser('MHS001', 'mahasiswa');

        $this->actingAs($mahasiswa)
            ->postJson(route('queue.join'), [
                'dean_id' => $pejabat->kode,
                'service_id' => $service->id,
                'latitude' => -6.9731200,
                'longitude' => 110.4120200,
            ])
            ->assertOk()
            ->assertJsonPath('status', 'ok');

        $this->assertDatabaseHas('queues', [
            'kode_user' => $mahasiswa->kode,
            'kode_dosen' => $pejabat->kode,
            'service_id' => $service->id,
            'status' => 'menunggu',
        ]);
    }

    /**
     * Memastikan dosen juga mengikuti alur join antrean yang sama.
     */
    public function test_dosen_can_join_queue_inside_radius(): void
    {
        [$pejabat, $service] = $this->seedOpenRoomWithGeofence();
        $dosen = $this->makeUser('DSN001', 'dosen');

        $this->actingAs($dosen)
            ->postJson(route('queue.join'), [
                'dean_id' => $pejabat->kode,
                'service_id' => $service->id,
                'latitude' => -6.9731200,
                'longitude' => 110.4120200,
            ])
            ->assertOk()
            ->assertJsonPath('status', 'ok');
    }

    /**
     * Memastikan join queue ditolak kalau perangkat berada di luar radius layanan.
     */
    public function test_queue_join_is_rejected_outside_radius(): void
    {
        [$pejabat, $service] = $this->seedOpenRoomWithGeofence();
        $mahasiswa = $this->makeUser('MHS002', 'mahasiswa');

        $this->actingAs($mahasiswa)
            ->postJson(route('queue.join'), [
                'dean_id' => $pejabat->kode,
                'service_id' => $service->id,
                'latitude' => -6.9900000,
                'longitude' => 110.4300000,
            ])
            ->assertStatus(422)
            ->assertJson([
                'status' => 'error',
            ]);
    }

    /**
     * Memastikan pejabat bisa memanggil dan menyelesaikan antrean dari dashboard.
     */
    public function test_pejabat_can_call_and_complete_queue(): void
    {
        [$pejabat, $service] = $this->seedOpenRoomWithGeofence();
        $mahasiswa = $this->makeUser('MHS003', 'mahasiswa');

        $queue = Queue::create([
            'kode_user' => $mahasiswa->kode,
            'kode_dosen' => $pejabat->kode,
            'service_id' => $service->id,
            'nomor_antrian' => 1,
            'status' => 'menunggu',
        ]);

        $this->actingAs($pejabat)
            ->postJson(route('queue.call', $queue))
            ->assertOk()
            ->assertJsonPath('queue_status', 'occupied');

        $this->assertDatabaseHas('queues', [
            'id' => $queue->id,
            'status' => 'diproses',
        ]);

        $this->actingAs($pejabat)
            ->postJson(route('queue.complete', $queue))
            ->assertOk()
            ->assertJsonPath('queue_status', 'open');

        $this->assertDatabaseHas('queues', [
            'id' => $queue->id,
            'status' => 'selesai',
        ]);
    }

    /**
     * Memastikan endpoint status dan antrean pribadi mengembalikan data yang dipakai frontend.
     */
    public function test_queue_status_and_my_queue_endpoints_return_data(): void
    {
        [$pejabat, $service] = $this->seedOpenRoomWithGeofence();
        $mahasiswa = $this->makeUser('MHS004', 'mahasiswa');

        Queue::create([
            'kode_user' => $mahasiswa->kode,
            'kode_dosen' => $pejabat->kode,
            'service_id' => $service->id,
            'nomor_antrian' => 1,
            'status' => 'menunggu',
        ]);

        $this->actingAs($mahasiswa)
            ->getJson(route('queue.my'))
            ->assertOk()
            ->assertJsonPath('role', 'mahasiswa');

        $this->actingAs($mahasiswa)
            ->getJson(route('queue.status', ['kode' => $pejabat->kode]))
            ->assertOk()
            ->assertJsonPath('kode_dosen', $pejabat->kode);
    }

    /**
     * Memastikan dashboard antrean pejabat mengurutkan FIFO dan menandai antrean prioritas dengan benar.
     */
    public function test_pejabat_queue_endpoint_orders_fifo_and_returns_priority_queues(): void
    {
        [$pejabat, $serviceFast] = $this->seedOpenRoomWithGeofence();
        $serviceSlow = $this->makeService('Administrasi');
        $mahasiswa = $this->makeUser('MHS010', 'mahasiswa');
        $dosenPengantre = $this->makeUser('DSN010', 'dosen');

        $oldestQueue = Queue::create([
            'kode_user' => $mahasiswa->kode,
            'kode_dosen' => $pejabat->kode,
            'service_id' => $serviceFast->id,
            'nomor_antrian' => 1,
            'status' => 'menunggu',
        ]);
        $oldestQueue->forceFill([
            'created_at' => now('Asia/Jakarta')->subMinutes(30),
            'updated_at' => now('Asia/Jakarta')->subMinutes(30),
        ])->saveQuietly();

        $priorityQueue = Queue::create([
            'kode_user' => $dosenPengantre->kode,
            'kode_dosen' => $pejabat->kode,
            'service_id' => $serviceSlow->id,
            'nomor_antrian' => 2,
            'status' => 'menunggu',
        ]);
        $priorityQueue->forceFill([
            'created_at' => now('Asia/Jakarta')->subMinutes(10),
            'updated_at' => now('Asia/Jakarta')->subMinutes(10),
        ])->saveQuietly();

        $completedQueue = Queue::create([
            'kode_user' => $dosenPengantre->kode,
            'kode_dosen' => $pejabat->kode,
            'service_id' => $serviceFast->id,
            'nomor_antrian' => 3,
            'status' => 'selesai',
        ]);
        $completedQueue->forceFill([
            'created_at' => now('Asia/Jakarta')->subMinutes(5),
            'updated_at' => now('Asia/Jakarta')->subMinutes(5),
        ])->saveQuietly();

        $this->actingAs($pejabat)
            ->getJson(route('queue.my'))
            ->assertOk()
            ->assertJsonPath('role', 'pejabat')
            ->assertJsonPath('queues.0.id', $oldestQueue->id)
            ->assertJsonPath('priority_queues.0.user.role', 'dosen');

        $response = $this->actingAs($pejabat)->getJson(route('queue.my'));
        $response->assertOk();

        $payload = $response->json();
        $this->assertCount(3, $payload['queues'] ?? []);
        $this->assertCount(2, $payload['priority_queues'] ?? []);
    }

    /**
     * Memastikan guest dialihkan ke login saat mencoba akses queue flow.
     */
    public function test_guest_is_redirected_from_queue_routes(): void
    {
        $this->postJson(route('queue.join'), [])
            ->assertRedirect(route('login'));
    }

    /**
     * Menyiapkan room aktif dengan geofence agar skenario join queue realistis.
     */
    private function seedOpenRoomWithGeofence(): array
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

    /**
     * Menyiapkan user uji untuk skenario queue flow.
     */
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

    /**
     * Menyiapkan service uji sebagai target antrean.
     */
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
