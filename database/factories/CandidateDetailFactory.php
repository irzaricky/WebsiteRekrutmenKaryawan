<?php

namespace Database\Factories;

use App\Models\CandidateDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class CandidateDetailFactory extends Factory
{
    protected $model = CandidateDetail::class;

    public function definition()
    {
        // Valid province codes from ValidNIK rule
        $validProvinsiCodes = [
            '11',
            '12',
            '13',
            '14',
            '15',
            '16',
            '17',
            '18',
            '19',
            '21',
            '31',
            '32',
            '33',
            '34',
            '35',
            '36',
            '51',
            '52',
            '53',
            '61',
            '62',
            '63',
            '64',
            '65',
            '71',
            '72',
            '73',
            '74',
            '75',
            '76',
            '81',
            '82',
            '91',
            '92',
            '93',
            '94',
            '95',
            '96'
        ];

        $validKabupatenCodes = [
            '01',
            '02',
            '03',
            '04',
            '05',
            '06',
            '07',
            '08',
            '09',
            '10',
            '11',
            '12',
            '13',
            '14',
            '15',
            '16',
            '17',
            '18',
            '19',
            '20',
            '21',
            '22',
            '23',
            '24',
            '25',
            '26',
            '27',
            '28',
            '29',
            '71',
            '72',
            '73',
            '74',
            '75',
            '76',
            '77',
            '78',
            '79'
        ];

        $validKecamatanCodes = [
            '01',
            '02',
            '03',
            '04',
            '05',
            '06',
            '07',
            '08',
            '09',
            '10',
            '11',
            '12',
            '13',
            '14',
            '15',
            '16',
            '17',
            '18',
            '19',
            '20',
            '21',
            '22',
            '23',
            '24',
            '25',
            '26',
            '27',
            '28',
            '29',
            '30',
            '31',
            '32',
            '33',
            '34',
            '35',
            '36',
            '37',
            '38',
            '39',
            '40',
            '41',
            '42',
            '43',
            '44',
            '45',
            '46',
            '47',
            '48',
            '49',
            '50',
            '51',
            '52',
            '53',
            '54',
            '55'
        ];

        // Generate birth date between 18-40 years ago
        $birthDate = Carbon::parse($this->faker->dateTimeBetween('-40 years', '-18 years'));

        // Generate NIK components
        $provinsi = $this->faker->randomElement($validProvinsiCodes);
        $kota = $this->faker->randomElement($validKabupatenCodes);
        $kecamatan = $this->faker->randomElement($validKecamatanCodes);
        $tanggal = str_pad($birthDate->day, 2, '0', STR_PAD_LEFT);
        $bulan = str_pad($birthDate->month, 2, '0', STR_PAD_LEFT);
        $tahun = substr($birthDate->year, -2);
        $urutan = str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT);

        // Combine to create valid NIK
        $nik = $provinsi . $kota . $kecamatan . $tanggal . $bulan . $tahun . $urutan;

        // Generate education level first to determine required ijazah
        $educationLevel = $this->faker->randomElement(['SMA', 'D3', 'S1', 'S2', 'S3']);

        // Initialize ijazah paths and statuses
        $ijazahData = [
            'ijazah_smp_path' => null,
            'ijazah_smp_status' => null,
            'ijazah_sma_path' => null,
            'ijazah_sma_status' => null,
            'ijazah_d3_path' => null,
            'ijazah_d3_status' => null,
            'ijazah_s1_path' => null,
            'ijazah_s1_status' => null,
            'ijazah_s2_path' => null,
            'ijazah_s2_status' => null,
            'ijazah_s3_path' => null,
            'ijazah_s3_status' => null
        ];

        // // Set required ijazah based on education level
        // $statuses = ['pending', 'accepted', 'rejected'];

        // // Always include SMP and SMA for all levels
        // $ijazahData['ijazah_smp_path'] = 'ijazah/smp/dummy.pdf';
        // $ijazahData['ijazah_smp_status'] = $this->faker->randomElement($statuses);
        // $ijazahData['ijazah_sma_path'] = 'ijazah/sma/dummy.pdf';
        // $ijazahData['ijazah_sma_status'] = $this->faker->randomElement($statuses);

        // // Add additional ijazah based on education level
        // switch ($educationLevel) {
        //     case 'D3':
        //         $ijazahData['ijazah_d3_path'] = 'ijazah/d3/dummy.pdf';
        //         $ijazahData['ijazah_d3_status'] = $this->faker->randomElement($statuses);
        //         break;
        //     case 'S1':
        //         $ijazahData['ijazah_s1_path'] = 'ijazah/s1/dummy.pdf';
        //         $ijazahData['ijazah_s1_status'] = $this->faker->randomElement($statuses);
        //         break;
        //     case 'S2':
        //         $ijazahData['ijazah_s1_path'] = 'ijazah/s1/dummy.pdf';
        //         $ijazahData['ijazah_s1_status'] = $this->faker->randomElement($statuses);
        //         $ijazahData['ijazah_s2_path'] = 'ijazah/s2/dummy.pdf';
        //         $ijazahData['ijazah_s2_status'] = $this->faker->randomElement($statuses);
        //         break;
        //     case 'S3':
        //         $ijazahData['ijazah_s1_path'] = 'ijazah/s1/dummy.pdf';
        //         $ijazahData['ijazah_s1_status'] = $this->faker->randomElement($statuses);
        //         $ijazahData['ijazah_s2_path'] = 'ijazah/s2/dummy.pdf';
        //         $ijazahData['ijazah_s2_status'] = $this->faker->randomElement($statuses);
        //         $ijazahData['ijazah_s3_path'] = 'ijazah/s3/dummy.pdf';
        //         $ijazahData['ijazah_s3_status'] = $this->faker->randomElement($statuses);
        //         break;
        // }

        return array_merge([
            'user_id' => User::factory(),
            'nik' => $nik,
            'address' => $this->faker->address(),
            'birth_date' => $birthDate,
            'education_level' => $educationLevel,
            'major' => $this->faker->randomElement([
                'Teknik Informatika',
                'Sistem Informasi',
                'Ilmu Komputer',
                'Teknik Elektro',
                'Manajemen',
                'Akuntansi'
            ]),
            'institution' => $this->faker->randomElement([
                'Universitas Indonesia',
                'Institut Teknologi Bandung',
                'Universitas Gadjah Mada',
                'Universitas Brawijaya',
                'Universitas Airlangga'
            ]),
            'graduation_year' => $this->faker->numberBetween(2015, 2023),
        ], $ijazahData);
    }
}
