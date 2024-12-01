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
        14,
        15,
        16,
        17,
        18,
        19,
        21,
        31,
        32,
        33,
        34,
        35,
        36,
        51,
        52,
        53,
        61,
        62,
        63,
        64,
        65,
        71,
        72,
        73,
        74,
        75,
        76,
        81,
        82,
        91,
        92,
        93,
        94,
        95,
        96
    ];

    private $validKabupatenCodes = [
        1,
        2,
        3,
        4,
        5,
        6,
        7,
        8,
        9,
        10,
        11,
        12,
        13,
        14,
        15,
        16,
        17,
        18,
        19,
        20,
        21,
        22,
        23,
        24,
        25,
        26,
        27,
        28,
        29,
        71,
        72,
        73,
        74,
        75,
        76,
        77,
        78,
        79
    ];

    private $validKecamatanCodes = [
        1,
        2,
        3,
        4,
        5,
        6,
        7,
        8,
        9,
        10,
        11,
        12,
        13,
        14,
        15,
        16,
        17,
        18,
        19,
        20,
        21,
        22,
        23,
        24,
        25,
        26,
        27,
        28,
        29,
        30,
        31,
        32,
        33,
        34,
        35,
        36,
        37,
        38,
        39,
        40,
        41,
        42,
        30,
        31,
        32,
        33,
        34,
        35,
        36,
        37,
        38,
        39,
        40,
        41,
        42,
        43,
        44,
        45,
        46,
        47,
        48,
        49,
        50,
        30,
        31,
        32,
        33,
        34,
        35,
        36,
        37,
        38,
        39,
        40,
        41,
        42,
        43,
        44,
        45,
        46,
        47,
        48,
        49,
        30,
        31,
        32,
        33,
        34,
        35,
        36,
        37,
        38,
        39,
        40,
        41,
        42,
        35,
        36,
        37,
        38,
        39,
        40,
        41,
        42,
        39,
        40,
        41,
        42,
        40,
        41,
        42,
        41,
        42,
        42,
        43,
        44,
        45,
        46,
        47,
        48,
        49,
        50,
        51,
        52,
        53,
        54,
        55
    ];

    private $message;

    public function passes($attribute, $value)
    {
        // Length check
        if (strlen($value) !== 16) {
            $this->message = 'NIK harus tepat 16 digit angka.';
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
            $this->message = "NIK tidak valid: 2 digit pertama ($provinsi) bukan kode provinsi yang valid.";
            return false;
        }

        // Validate city code
        if (!in_array(intval($kota), $this->validKabupatenCodes)) {
            $this->message = "NIK tidak valid: 2 digit kedua ($kota) bukan kode kota yang valid.";
            return false;
        }

        // Validate district code
        if (!in_array(intval($kecamatan), $this->validKecamatanCodes)) {
            $this->message = "NIK tidak valid: 2 digit ketiga ($kecamatan) bukan kode kecamatan yang valid.";
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
            $this->message = "NIK tidak valid: digit ke-7 dan 8 ($tanggal) bukan tanggal yang valid.";
            return false;
        }

        if ($bulanInt < 1 || $bulanInt > 12) {
            $this->message = "NIK tidak valid: digit ke-9 dan 10 ($bulan) bukan bulan yang valid.";
            return false;
        }

        // Validate sequence number
        if (!is_numeric($urutan) || intval($urutan) < 1) {
            $this->message = "NIK tidak valid: 4 digit terakhir ($urutan) bukan nomor urut yang valid.";
            return false;
        }

        // Validate complete date
        try {
            $date = Carbon::createFromDate($tahunInt, $bulanInt, $tanggalInt);
            if ($date->isFuture()) {
                $this->message = "NIK tidak valid: tanggal lahir ($tanggalInt-$bulanInt-$tahunInt) tidak boleh di masa depan.";
                return false;
            }
        } catch (\Exception $e) {
            $this->message = "NIK tidak valid: kombinasi tanggal ($tanggalInt-$bulanInt-$tahunInt) tidak valid.";
            return false;
        }

        return true;
    }

    public function message()
    {
        return $this->message;
    }
}