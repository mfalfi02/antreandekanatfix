<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan admin diarahkan ke dashboard admin setelah login berhasil.
     */
    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = $this->makeUser('ADM001', 'admin');

        $this->post(route('login.submit'), [
            'kode' => $admin->kode,
            'password' => 'password',
        ])->assertRedirect(route('adm'));
    }

    /**
     * Memastikan pejabat masuk ke dashboard dosen karena alur layanan memakai tampilan tersebut.
     */
    public function test_pejabat_login_redirects_to_dosen_dashboard(): void
    {
        $pejabat = $this->makeUser('PJB001', 'pejabat');

        $this->post(route('login.submit'), [
            'kode' => $pejabat->kode,
            'password' => 'password',
        ])->assertRedirect(route('dsn'));
    }

    /**
     * Memastikan mahasiswa diarahkan ke dashboard mahasiswa setelah login sukses.
     */
    public function test_mahasiswa_login_redirects_to_mahasiswa_dashboard(): void
    {
        $mahasiswa = $this->makeUser('MHS001', 'mahasiswa');

        $this->post(route('login.submit'), [
            'kode' => $mahasiswa->kode,
            'password' => 'password',
        ])->assertRedirect(route('mhs'));
    }

    /**
     * Memastikan password salah tetap memantul ke form login dengan pesan error.
     */
    public function test_login_fails_with_wrong_password(): void
    {
        $user = $this->makeUser('MHS002', 'mahasiswa');

        $this->from(route('login'))
            ->post(route('login.submit'), [
                'kode' => $user->kode,
                'password' => 'salah',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('error', 'Kode atau password salah!');
    }

    /**
     * Memastikan logout menghapus sesi login dan mengembalikan user ke halaman login.
     */
    public function test_logout_clears_authentication_session(): void
    {
        $user = $this->makeUser('MHS003', 'mahasiswa');

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    /**
     * Memastikan route yang diproteksi middleware menolak akses guest.
     */
    public function test_guest_is_redirected_from_protected_dashboard(): void
    {
        $this->get(route('dsn'))
            ->assertRedirect(route('login'));

        $this->get(route('mhs'))
            ->assertRedirect(route('login'));
    }

    /**
     * Menyiapkan user uji dengan role tertentu agar alur login bisa divalidasi konsisten.
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
}
