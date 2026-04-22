<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeofenceService
{
    // Ambil record setting pusat lokasi yang dipakai bersama oleh seluruh validasi antrean.
    public function getSetting(): SystemSetting
    {
        return SystemSetting::firstOrCreate(
            ['id' => 1],
            [
                'queue_status' => 'closed',
                'radius_meters' => 300,
            ]
        );
    }

    // Geofence hanya aktif jika koordinat pusat dan radius sudah diisi admin.
    public function isConfigured(SystemSetting $setting): bool
    {
        return $setting->center_latitude !== null
            && $setting->center_longitude !== null
            && (int) $setting->radius_meters > 0;
    }

    // Hitung jarak dua titik koordinat dalam meter menggunakan rumus Haversine.
    public function distanceMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000;
        $latFrom = deg2rad($lat1);
        $lngFrom = deg2rad($lng1);
        $latTo = deg2rad($lat2);
        $lngTo = deg2rad($lng2);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $a = sin($latDelta / 2) ** 2
            + cos($latFrom) * cos($latTo) * sin($lngDelta / 2) ** 2;
        $c = 2 * asin(min(1, sqrt($a)));

        return $earthRadius * $c;
    }

    // Validasi lokasi untuk aksi yang membutuhkan user berada di area kampus/dekat lokasi antrean.
    public function validateRequest(Request $request, string $actionLabel): ?JsonResponse
    {
        $setting = $this->getSetting();
        if (!$this->isConfigured($setting)) {
            // Kalau admin belum mengisi koordinat, sistem tetap berjalan tanpa geofence agar antrean tidak terblokir total.
            return null;
        }

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lokasi perangkat belum terbaca. Aktifkan GPS lalu izinkan akses lokasi.',
            ], 422);
        }

        $distance = $this->distanceMeters(
            (float) $latitude,
            (float) $longitude,
            (float) $setting->center_latitude,
            (float) $setting->center_longitude
        );

        if ($distance > (int) $setting->radius_meters) {
            return response()->json([
                'status' => 'error',
                'message' => "Anda berada di luar radius {$setting->radius_meters} meter untuk {$actionLabel}.",
                'distance_meters' => round($distance, 2),
                'radius_meters' => (int) $setting->radius_meters,
                'center' => [
                    'latitude' => (float) $setting->center_latitude,
                    'longitude' => (float) $setting->center_longitude,
                ],
            ], 422);
        }

        return null;
    }
}
