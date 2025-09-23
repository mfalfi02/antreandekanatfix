<?php




// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use App\Models\Admin;
// use App\Models\Dosen;
// use App\Models\Mahasiswa;
// use Illuminate\Support\Facades\Hash;

// class LoginController extends Controller
// {
//     public function showLoginForm()
//     {
//         return view('auth.login');
//     }

//     public function login(Request $request)
//     {
//         $request->validate([
//             'identifier' => 'required',
//             'password'   => 'required',
//         ]);

//         $id = $request->identifier;
//         $pw = $request->password;

//         // 🔹 Login Admin
//         $admin = Admin::where('email', $id)->first();
//         if ($admin && Hash::check($pw, $admin->password)) {
//             Auth::guard('admin')->login($admin);
//             $request->session()->regenerate();
//             return redirect()->route('dashboard.admin');
//         }

//         // 🔹 Login Dosen
//         $dosen = Dosen::where('kode_dosen', $id)->first();
//         if ($dosen && Hash::check($pw, $dosen->password)) {
//             Auth::guard('dosen')->login($dosen);
//             $request->session()->regenerate();
//             return redirect()->route('dosen.dashboard');
//         }

//         // 🔹 Login Mahasiswa
//         $mhs = Mahasiswa::where('nim', $id)->orWhere('email', $id)->first();
//         if($mhs && Auth::guard('mahasiswa')->attempt(['nim' => $mhs->nim, 'password' => $pw])) {
//             $request->session()->regenerate();
//             return redirect()->route('mahasiswa.dashboard');
//         }


//         // Jika semua gagal
//         return back()->withErrors(['identifier' => 'Login gagal!']);
//     }

//     public function logout(Request $request)
//     {
//         // Logout guard yang sedang aktif
//         if (Auth::guard('admin')->check()) {
//             Auth::guard('admin')->logout();
//         }

//         if (Auth::guard('dosen')->check()) {
//             Auth::guard('dosen')->logout();
//         }

//         if (Auth::guard('mahasiswa')->check()) {
//             Auth::guard('mahasiswa')->logout();
//         }

//         $request->session()->invalidate();
//         $request->session()->regenerateToken();

//         return redirect()->route('login');
//     }
// }



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Dosen;
use App\Models\Mahasiswa;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required',
            'password'   => 'required',
        ]);

        $identifier = $request->identifier;
        $password = $request->password;

        // 🔹 Login Admin
        $admin = Admin::where('email', $identifier)->first();
        if ($admin && \Hash::check($password, $admin->password)) {
            Auth::guard('admin')->login($admin);
            $request->session()->regenerate();
            return redirect()->route('dashboard.admin');
        }

        // 🔹 Login Dosen
        $dosen = Dosen::where('kode_dosen', $identifier)->first();
        if ($dosen && \Hash::check($password, $dosen->password)) {
            Auth::guard('dosen')->login($dosen);
            $request->session()->regenerate();
            return redirect()->route('dosen.dashboard');
        }

        // 🔹 Login Mahasiswa
        // Gunakan attempt langsung agar hash dicek otomatis
        $field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'nim';

            if (Auth::guard('mahasiswa')->attempt([$field => $identifier, 'password' => $password])) {
                $request->session()->regenerate();
                return redirect()->route('mahasiswa.dashboard');
            }

        // Jika semua gagal
        return back()->withErrors(['identifier' => 'Login gagal, NIM / Email atau Password salah!']);
    }

    public function logout(Request $request)
    {
        // Logout semua guard
        if (Auth::guard('admin')->check()) Auth::guard('admin')->logout();
        if (Auth::guard('dosen')->check()) Auth::guard('dosen')->logout();
        if (Auth::guard('mahasiswa')->check()) Auth::guard('mahasiswa')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
