<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MasterDataCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_create_update_and_delete_flow_works(): void
    {
        $admin = $this->makeUser('ADM001', 'admin');

        $this->actingAs($admin)
            ->post(route('users.store'), [
                'kode' => 'PJB100',
                'name' => 'Pejabat Baru',
                'email' => 'pejabat.baru@example.com',
                'role' => 'pejabat',
                'password' => 'password',
                'password_confirmation' => 'password',
                'status' => 'aktif',
                'jabatan' => 'Wakil Dekan',
                'ruangan' => 'Ruang 2',
            ])
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'kode' => 'PJB100',
            'name' => 'Pejabat Baru',
            'role' => 'pejabat',
            'ruangan' => 'Ruang 2',
        ]);

        $this->actingAs($admin)
            ->put(route('users.update', 'PJB100'), [
                'kode' => 'PJB100',
                'name' => 'Dosen Baru',
                'email' => 'dosen.baru@example.com',
                'role' => 'dosen',
                'status' => 'aktif',
                'jabatan' => 'Dosen',
                'ruangan' => '',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'kode' => 'PJB100',
            'name' => 'Dosen Baru',
            'role' => 'dosen',
            'ruangan' => null,
        ]);

        $this->actingAs($admin)
            ->delete(route('users.destroy', 'PJB100'))
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseMissing('users', [
            'kode' => 'PJB100',
        ]);
    }

    public function test_service_create_update_and_delete_flow_works(): void
    {
        $admin = $this->makeUser('ADM001', 'admin');

        $this->actingAs($admin)
            ->post(route('services.store'), [
                'nama_layanan' => 'Legalisir',
                'deskripsi' => 'Layanan legalisir',
                'est' => 10,
                'status' => 'aktif',
            ])
            ->assertRedirect(route('services.index'));

        $service = Service::where('nama_layanan', 'Legalisir')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('services.update', $service), [
                'nama_layanan' => 'Surat Aktif',
                'deskripsi' => 'Layanan surat terbaru',
                'est' => 15,
                'status' => 'nonaktif',
            ])
            ->assertRedirect(route('services.index'));

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'nama_layanan' => 'Surat Aktif',
            'est' => 15,
            'status' => 'nonaktif',
        ]);

        $this->actingAs($admin)
            ->delete(route('services.destroy', $service))
            ->assertRedirect(route('services.index'));

        $this->assertDatabaseMissing('services', [
            'id' => $service->id,
        ]);
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
