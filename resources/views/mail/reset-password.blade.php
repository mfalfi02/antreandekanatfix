@component('mail::message')

<div style="padding: 18px 20px; border-radius: 16px; background: linear-gradient(135deg, #1e1b4b 0%, #4338ca 55%, #0ea5e9 100%); color: #fff; margin-bottom: 24px;">
    <div style="font-size: 12px; letter-spacing: .18em; text-transform: uppercase; opacity: .9; margin-bottom: 8px;">
        Sistem Antrean Dekanat
    </div>
    <div style="font-size: 28px; font-weight: 800; line-height: 1.15;">
        Reset Password Aman
    </div>
</div>

# Halo, {{ $name }}

Kami menerima permintaan reset password untuk akun Anda.
Klik tombol di bawah untuk membuat password baru.

@component('mail::button', ['url' => $url, 'color' => 'primary'])
Reset Password Sekarang
@endcomponent

@component('mail::panel')
Link reset ini berlaku selama **{{ $expireMinutes }} menit** dan hanya bisa dipakai untuk akun ini.
@endcomponent

@component('mail::subcopy')
Jika tombol di atas tidak berfungsi, salin tautan ini ke browser:

{{ $url }}
@endcomponent

Jika Anda tidak merasa meminta reset password, abaikan email ini.

Terima kasih,<br>
Tim Sistem Antrean Dekanat
@endcomponent
