<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_pejabat_requires_ruangan(): void
    {
        $response = $this->from(route('users.create'))
            ->post(route('users.store'), [
                'kode' => 'PJB002',
                'name' => 'Pejabat Baru',
                'email' => 'pejabat.baru@example.com',
                'role' => 'pejabat',
                'password' => 'password',
                'password_confirmation' => 'password',
                'status' => 'aktif',
                'jabatan' => 'Wakil Dekan',
                'ruangan' => '',
            ]);

        $response->assertRedirect(route('users.create'))
            ->assertSessionHasErrors('ruangan');
    }

    public function test_store_non_pejabat_can_have_empty_ruangan(): void
    {
        $response = $this->post(route('users.store'), [
            'kode' => 'DSN100',
            'name' => 'Dosen Pengantre',
            'email' => 'dosen.pengantre@example.com',
            'role' => 'dosen',
            'password' => 'password',
            'password_confirmation' => 'password',
            'status' => 'aktif',
            'jabatan' => 'Dosen',
            'ruangan' => '',
        ]);

        $response->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'kode' => 'DSN100',
            'role' => 'dosen',
            'ruangan' => null,
        ]);
    }

    public function test_update_non_pejabat_clears_ruangan(): void
    {
        $user = User::create([
            'kode' => 'PJB001',
            'name' => 'Pejabat Lama',
            'email' => 'pejabat.lama@example.com',
            'password' => Hash::make('password'),
            'role' => 'pejabat',
            'status' => 'aktif',
            'jabatan' => 'Kepala Dekanat',
            'ruangan' => 'Ruang Dekanat 1',
        ]);

        $response = $this->put(route('users.update', $user->kode), [
            'kode' => 'PJB001',
            'name' => 'Dosen Baru',
            'email' => 'dosen.baru@example.com',
            'role' => 'dosen',
            'status' => 'aktif',
            'jabatan' => 'Dosen',
            'ruangan' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'kode' => 'PJB001',
            'role' => 'dosen',
            'ruangan' => null,
        ]);
    }
}
