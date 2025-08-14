<?php

namespace App\Services;

use App\Models\Kur;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ExchangeService
{
    public static function getKursFromGlobal($from, $date)
    {
        $tanggal = Carbon::createFromDate($date);
        $formatTanggal = '/' . $tanggal->year . '/' . $tanggal->month . '/' . $tanggal->day;
        $exchange = Http::get(env('EXCHANGE_RATE_URL') . env('EXCHANGE_RATE_API_KEY') . '/history/' . $from . $formatTanggal);
        $exchange = $exchange->json();
        if ($exchange['result'] === 'error') {
            return false;
        }
        // GET https://v6.exchangerate-api.com/v6/YOUR-API-KEY/history/USD/YEAR/MONTH/DAY
    }
    public static function getLatestKursFromGlobal($kurs_code)
    {
        $exchange = Http::get(env('EXCHANGE_RATE_URL') . env('EXCHANGE_RATE_API_KEY') . '/latest/' . $kurs_code);
        $exchange = $exchange->json();
        if ($exchange['result'] === 'error') {
            dd($exchange);
        }
        dd($exchange);
    }
    public static function getLatestKurs($kurs_code)
    {
        $exchange = Kur::where('code', $kurs_code)->latest()->first();

        if (!$exchange) {
            return ['success' => false, 'message' => 'Kurs Not Found', 'kurs_code' => $kurs_code];
        }

        return  $exchange->KursExhcange?->exchange;
    }
}
