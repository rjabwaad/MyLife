<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
class prayerController extends Controller
{
    public function getPrayerTimes(Request $request)
    {
        $city = $request->city ?? 'Tunis';
        $country = $request->country ?? 'Tunisia';

        $response = Http::get('http://api.aladhan.com/v1/timingsByCity', [
            'city' => $city,
            'country' => $country,
            'method' => 2  // طريقة الحساب
        ]);

        return $response->json();
    }
}
