<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class ValidNIK implements Rule
{
    private $validProvinsiCodes = [
        11,
        12,
        13,
        16,
        14,
        21,
        15,
        17,
        18,
        19,
        31,
        36,
        31,
        33,
        34,
        35,
        51,
        61,
        62,
        63,
        64,
        65,
        52,
        53,
        71,
        72,
        73,
        74,
        75,
        76,
        81,
        82,
        91,
        92
    ];

    private $message;

    public function passes($attribute, $value)
    {
        // Length check
        if (strlen($value) !== 16) {
            $this->message = 'NIK harus 16 digit.';
            return false;
        }

        // Extract components
        $provinsi = substr($value, 0, 2);
        $kota = substr($value, 2, 2);
        $kecamatan = substr($value, 4, 2);
        $tanggal = substr($value, 6, 2);
        $bulan = substr($value, 8, 2);
        $tahun = substr($value, 10, 2);
        $urutan = substr($value, 12, 4);

        // Validate province code
        if (!in_array(intval($provinsi), $this->validProvinsiCodes)) {
            $this->message = 'Kode provinsi tidak valid.';
            return false;
        }

        // Validate city code
        if (!is_numeric($kota) || intval($kota) < 1 || intval($kota) > 99) {
            $this->message = 'Kode kota tidak valid.';
            return false;
        }

        // Validate district code
        if (!is_numeric($kecamatan) || intval($kecamatan) < 1 || intval($kecamatan) > 99) {
            $this->message = 'Kode kecamatan tidak valid.';
            return false;
        }

        // Parse date components
        $tanggalInt = intval($tanggal);
        $bulanInt = intval($bulan);
        $tahunInt = intval($tahun);
        $currentYear = intval(date('y'));

        // Year handling
        if ($tahunInt > $currentYear) {
            $tahunInt += 1900;
        } else {
            $tahunInt += 2000;
        }

        // Basic date component validation
        if ($tanggalInt < 1 || $tanggalInt > 31) {
            $this->message = 'Tanggal lahir tidak valid.';
            return false;
        }

        if ($bulanInt < 1 || $bulanInt > 12) {
            $this->message = 'Bulan lahir tidak valid.';
            return false;
        }

        // Validate sequence number
        if (!is_numeric($urutan) || intval($urutan) < 1) {
            $this->message = 'Nomor urut tidak valid.';
            return false;
        }

        // Validate complete date
        try {
            $date = Carbon::createFromDate($tahunInt, $bulanInt, $tanggalInt);
            if ($date->isFuture()) {
                $this->message = 'Tanggal lahir tidak boleh di masa depan.';
                return false;
            }
        } catch (\Exception $e) {
            $this->message = 'Tanggal lahir tidak valid.';
            return false;
        }

        return true;
    }

    public function message()
    {
        return $this->message;
    }
}