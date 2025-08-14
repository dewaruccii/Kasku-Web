<?php

use Carbon\Carbon;
use Illuminate\Support\Str;


function uuidGenerator()
{
    return Str::uuid();
}

if (!function_exists('formatCurrency')) {
    /**
     * Format angka ke format mata uang yang sesuai.
     *
     * @param float|int $value
     * @param string $currency
     * @return string
     */
    function formatCurrency($value, $currency = 'IDR', $symbol = 'Rp. ')
    {
        // Menentukan format berdasarkan mata uang
        switch ($currency) {
            case 'IDR':
                return $symbol . number_format($value, 0, ',', '.');
            case 'USD':
                return $symbol . number_format($value, 2, '.', ',');
            case 'EUR':
                return $symbol . number_format($value, 2, '.', ',');
            case 'GBP':
                return $symbol . number_format($value, 2, '.', ',');
            default:
                // Format default jika mata uang tidak dikenali
                return $symbol . number_format($value, 2, '.', ',') . ' ' . $currency;
        }
    }
}

function formatTime($date, $format = '%d %B %Y')
{
    $originalDate = $date;
    $formattedDate = Carbon::parse($originalDate)->formatLocalized($format);

    return $formattedDate; // Output: 24 Agustus 2023
}
function formatTimeRaw($time, $formatType = 'Y-m-d')
{
    try {
        // Pastikan waktu tidak null dan buat instance Carbon
        $carbonTime = Carbon::parse($time);

        // Kembalikan waktu yang diformat sesuai dengan format yang diberikan
        return $carbonTime->format($formatType);
    } catch (\Exception $e) {
        // Tangani error jika waktu tidak valid
        return "Invalid time format" . $e->getMessage();
    }
}

function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}

function formatToTimeNow($date)
{
    // Kembalikan waktu yang sudah berlalu dalam format terdekat
    $now = Carbon::now();
    return  $date . ' ' . $now->hour . ':' . $now->minute . ':' . $now->second;
}
