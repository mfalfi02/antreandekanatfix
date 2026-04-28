<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GeofenceBlackBoxTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan admin bisa menyimpan setting geofence yang dipakai validasi lokasi antrean.
     */
    public function test_admin_can_save_geofence_setting(): void
    {
        $admin = $this->makeUser('ADM001', 'admin');

        $response = $this->actingAs($admin)->postJson(route('adm.location.update'), [
            'center_latitude' => -6.9731000,
            'center_longitude' => 110.4120000,
            'radius_meters' => 250,
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Pengaturan lokasi antrean berhasil disimpan.');

        $this->assertDatabaseHas('system_settings', [
            'id' => 1,
            'center_latitude' => -6.9731000,
            'center_longitude' => 110.4120000,
            'radius_meters' => 250,
        ]);
    }

    /**
     * Memastikan role non-admin ditolak saat mencoba mengubah geofence.
     */
    public function test_non_admin_cannot_save_geofence_setting(): void
    {
        $user = $this->makeUser('PJB001', 'pejabat');

        $this->actingAs($user)
            ->postJson(route('adm.location.update'), [
                'center_latitude' => -6.9731000,
                'center_longitude' => 110.4120000,
                'radius_meters' => 250,
            ])
            ->assertForbidden();
    }

    /**
     * Memastikan antrean bisa dibuka saat lokasi perangkat masih berada di dalam radius.
     */
    public function test_queue_can_be_opened_when_location_is_inside_radius(): void
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

        $response = $this->actingAs($pejabat)->postJson(route('queue.toggle'), [
            'status' => 'open',
            'service_scope' => 'single',
            'service_id' => $service->id,
            'expected_jam_tutup' => '23:59',
            'latitude' => -6.9731200,
            'longitude' => 110.4120200,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'queue_status' => 'open',
                'kode_dosen' => $pejabat->kode,
            ]);
    }

    /**
     * Memastikan pembukaan antrean ditolak kalau perangkat berada di luar radius.
     */
    public function test_queue_is_rejected_when_location_is_outside_radius(): void
    {
        $pejabat = $this->makeUser('PJB001', 'pejabat');
        $service = $this->makeService('Legalisir');

        SystemSetting::updateOrCreate(
            ['id' => 1],
            [
                'queue_status' => 'closed',
                'center_latitude' => -6.9731000,
                'center_longitude' => 110.4120000,
                'radius_meters' => 50,
            ]
        );

        $response = $this->actingAs($pejabat)->postJson(route('queue.toggle'), [
            'status' => 'open',
            'service_scope' => 'single',
            'service_id' => $service->id,
            'expected_jam_tutup' => '23:59',
            'latitude' => -6.9800000,
            'longitude' => 110.4200000,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
            ]);
    }

    /**
     * Memastikan validasi lokasi memberi error jika koordinat GPS belum terbaca.
     */
    public function test_queue_is_rejected_when_gps_coordinates_are_missing(): void
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

        $response = $this->actingAs($pejabat)->postJson(route('queue.toggle'), [
            'status' => 'open',
            'service_scope' => 'single',
            'service_id' => $service->id,
            'expected_jam_tutup' => '23:59',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Lokasi perangkat belum terbaca. Aktifkan GPS lalu izinkan akses lokasi.',
            ]);
    }

    /**
     * Memastikan alur fallback tetap bisa berjalan ketika geofence belum dikonfigurasi.
     */
    public function test_queue_can_open_without_geofence_configuration(): void
    {
        $pejabat = $this->makeUser('PJB001', 'pejabat');
        $service = $this->makeService('Legalisir');

        SystemSetting::updateOrCreate(
            ['id' => 1],
            [
                'queue_status' => 'closed',
                'center_latitude' => null,
                'center_longitude' => null,
                'radius_meters' => 300,
            ]
        );

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
            ]);
    }

    /**
     * Menyiapkan user uji untuk skenario geofence black-box.
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
     * Menyiapkan service uji untuk request toggle queue.
     */
    private function makeService(string $name): Service
    {
        return Service::create([
            'nama_layanan' => $name,
            'deskripsi' => $name . ' service',
            'status' => 'aktif',
        ]);
    }
}
