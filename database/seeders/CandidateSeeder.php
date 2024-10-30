<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Test; // Pastikan Anda menggunakan model User
use App\Models\TestResult; // Pastikan Anda menggunakan model TestResult
use Illuminate\Support\Facades\DB;

class CandidateSeeder extends Seeder
{
    public function run()
    {
        // Data pengguna
        $candidates = [
            ['name' => 'Abdul Wahid', 'TIU' => 110, 'TWK' => 75, 'TKB' => 180, 'TW' => 300],
            ['name' => 'Achmad Sofyan', 'TIU' => 90, 'TWK' => 90, 'TKB' => 170, 'TW' => 280],
            ['name' => 'Adha Nur', 'TIU' => 85, 'TWK' => 125, 'TKB' => 166, 'TW' => 300],
            ['name' => 'Adi Pratama', 'TIU' => 95, 'TWK' => 90, 'TKB' => 200, 'TW' => 270],
            ['name' => 'Adib Fahmi', 'TIU' => 95, 'TWK' => 85, 'TKB' => 195, 'TW' => 250],
            ['name' => 'Affandi', 'TIU' => 105, 'TWK' => 90, 'TKB' => 185, 'TW' => 380],
            ['name' => 'Aini Sukma', 'TIU' => 95, 'TWK' => 120, 'TKB' => 200, 'TW' => 270],
            ['name' => 'Airlangga ', 'TIU' => 100, 'TWK' => 100, 'TKB' => 180, 'TW' => 290],
            ['name' => 'Ajeng Fitri', 'TIU' => 85, 'TWK' => 70, 'TKB' => 167, 'TW' => 275],
            ['name' => 'Annisa Triani', 'TIU' => 85, 'TWK' => 100, 'TKB' => 180, 'TW' => 325],
            ['name' => 'Arga Saputra', 'TIU' => 105, 'TWK' => 95, 'TKB' => 180, 'TW' => 286],
            ['name' => 'Athma Jayanto', 'TIU' => 100, 'TWK' => 90, 'TKB' => 185, 'TW' => 310],
            ['name' => 'Atra Atlanta Putra', 'TIU' => 95, 'TWK' => 100, 'TKB' => 185, 'TW' => 230],
            ['name' => 'Azzahra Nabila', 'TIU' => 105, 'TWK' => 95, 'TKB' => 200, 'TW' => 227],
            ['name' => 'Bagas Aji', 'TIU' => 115, 'TWK' => 85, 'TKB' => 166, 'TW' => 340],
            ['name' => 'Bagas Fajar', 'TIU' => 115, 'TWK' => 80, 'TKB' => 166, 'TW' => 365],
            ['name' => 'Bagas Labando', 'TIU' => 105, 'TWK' => 95, 'TKB' => 190, 'TW' => 275],
            ['name' => 'Bagus Ary', 'TIU' => 100, 'TWK' => 65, 'TKB' => 225, 'TW' => 245],
            ['name' => 'Bayu Aji', 'TIU' => 100, 'TWK' => 65, 'TKB' => 200, 'TW' => 265],
            ['name' => 'Bayu Angin', 'TIU' => 125, 'TWK' => 70, 'TKB' => 195, 'TW' => 225],
            ['name' => 'Cecilia Hanggara', 'TIU' => 105, 'TWK' => 90, 'TKB' => 180, 'TW' => 275],
            ['name' => 'Daffa Purna Nada', 'TIU' => 100, 'TWK' => 100, 'TKB' => 185, 'TW' => 240],
            ['name' => 'David Verdiansah', 'TIU' => 100, 'TWK' => 90, 'TKB' => 225, 'TW' => 240],
            ['name' => 'Dea Ananda', 'TIU' => 105, 'TWK' => 90, 'TKB' => 200, 'TW' => 265],
            ['name' => 'Dealova Saraswathi', 'TIU' => 90, 'TWK' => 90, 'TKB' => 170, 'TW' => 345],
            ['name' => 'Deonandra Bagus', 'TIU' => 110, 'TWK' => 100, 'TKB' => 190, 'TW' => 275],
            ['name' => 'Desy Sulis', 'TIU' => 105, 'TWK' => 125, 'TKB' => 185, 'TW' => 300],
            ['name' => 'Dewanri', 'TIU' => 100, 'TWK' => 110, 'TKB' => 180, 'TW' => 320],
            ['name' => 'Dewanti Astita', 'TIU' => 105, 'TWK' => 90, 'TKB' => 190, 'TW' => 345],
            ['name' => 'Diastuti Putri', 'TIU' => 90, 'TWK' => 85, 'TKB' => 215, 'TW' => 240],
            ['name' => 'Diaz Rifki', 'TIU' => 100, 'TWK' => 70, 'TKB' => 225, 'TW' => 340],
            ['name' => 'Dua Dwiyanto', 'TIU' => 85, 'TWK' => 90, 'TKB' => 166, 'TW' => 255],
            ['name' => 'Dwi Putra', 'TIU' => 85, 'TWK' => 75, 'TKB' => 180, 'TW' => 260],
            ['name' => 'Eka Herdianto', 'TIU' => 85, 'TWK' => 85, 'TKB' => 190, 'TW' => 310],
            ['name' => 'Eka Nugraha', 'TIU' => 105, 'TWK' => 105, 'TKB' => 170, 'TW' => 345],
            ['name' => 'FADHILLAH KURNIAWAN', 'TIU' => 100, 'TWK' => 120, 'TKB' => 166, 'TW' => 255],
            ['name' => 'Fareza Aulia ', 'TIU' => 105, 'TWK' => 100, 'TKB' => 180, 'TW' => 385],
            ['name' => 'FATA ZILL ', 'TIU' => 95, 'TWK' => 100, 'TKB' => 180, 'TW' => 340],
            ['name' => 'Fendi Wasbadi', 'TIU' => 90, 'TWK' => 110, 'TKB' => 200, 'TW' => 350],
            ['name' => 'Friska Lutfiana', 'TIU' => 105, 'TWK' => 115, 'TKB' => 185, 'TW' => 385],
            ['name' => 'Gusti Ngurah', 'TIU' => 90, 'TWK' => 110, 'TKB' => 180, 'TW' => 280],
            ['name' => 'Hadi Wijaya', 'TIU' => 115, 'TWK' => 100, 'TKB' => 180, 'TW' => 270],
            ['name' => 'Hafdzia Rahmah', 'TIU' => 110, 'TWK' => 120, 'TKB' => 180, 'TW' => 265],
            ['name' => 'Hamam Banando', 'TIU' => 85, 'TWK' => 120, 'TKB' => 195, 'TW' => 315],
            ['name' => 'Hasanarfa Rafanza', 'TIU' => 95, 'TWK' => 75, 'TKB' => 190, 'TW' => 280],
            ['name' => 'Humam Banando', 'TIU' => 90, 'TWK' => 125, 'TKB' => 200, 'TW' => 380],
            ['name' => 'Inayah Oktafia', 'TIU' => 100, 'TWK' => 100, 'TKB' => 180, 'TW' => 360],
            ['name' => 'Indah Fajar', 'TIU' => 100, 'TWK' => 85, 'TKB' => 195, 'TW' => 375],
            ['name' => 'Indah Sari', 'TIU' => 90, 'TWK' => 100, 'TKB' => 200, 'TW' => 385],
            ['name' => 'Indin Sukma', 'TIU' => 105, 'TWK' => 65, 'TKB' => 190, 'TW' => 250],
            ['name' => 'Indraswari', 'TIU' => 90, 'TWK' => 90, 'TKB' => 170, 'TW' => 300],
            ['name' => 'Inni Rusydiana', 'TIU' => 100, 'TWK' => 85, 'TKB' => 225, 'TW' => 390],
            ['name' => 'Irvansyah ', 'TIU' => 95, 'TWK' => 75, 'TKB' => 200, 'TW' => 385],
            ['name' => 'Isa Nur Hidayah', 'TIU' => 105, 'TWK' => 90, 'TKB' => 180, 'TW' => 280],
            ['name' => 'Julia Sulistyowati', 'TIU' => 95, 'TWK' => 100, 'TKB' => 190, 'TW' => 275],
            ['name' => 'Kadar Mustahar', 'TIU' => 95, 'TWK' => 110, 'TKB' => 190, 'TW' => 295],
            ['name' => 'kartika Fernanda', 'TIU' => 120, 'TWK' => 75, 'TKB' => 185, 'TW' => 345],
            ['name' => 'KHOIRUN NISA', 'TIU' => 105, 'TWK' => 100, 'TKB' => 185, 'TW' => 345],
            ['name' => 'KIRANA ATSIILA', 'TIU' => 120, 'TWK' => 150, 'TKB' => 180, 'TW' => 375],
            ['name' => 'Krisna Bayu', 'TIU' => 110, 'TWK' => 65, 'TKB' => 180, 'TW' => 270],
            ['name' => 'Kurnia Aghest', 'TIU' => 90, 'TWK' => 100, 'TKB' => 195, 'TW' => 280],
            ['name' => 'Leila Putri Cantika', 'TIU' => 95, 'TWK' => 70, 'TKB' => 200, 'TW' => 300],
            ['name' => 'Limas Nuri ', 'TIU' => 110, 'TWK' => 85, 'TKB' => 200, 'TW' => 340],
            ['name' => 'Lismawati', 'TIU' => 105, 'TWK' => 85, 'TKB' => 220, 'TW' => 375],
            ['name' => 'Liswanto', 'TIU' => 130, 'TWK' => 95, 'TKB' => 200, 'TW' => 345],
            ['name' => 'Lucky Ridho', 'TIU' => 85, 'TWK' => 85, 'TKB' => 225, 'TW' => 365],
            ['name' => 'Lukman Wijaya', 'TIU' => 90, 'TWK' => 90, 'TKB' => 200, 'TW' => 370],
            ['name' => 'Luqman Rosdianto', 'TIU' => 95, 'TWK' => 100, 'TKB' => 180, 'TW' => 370],
            ['name' => 'Luthfi Adiytra', 'TIU' => 90, 'TWK' => 80, 'TKB' => 200, 'TW' => 375],
            ['name' => 'Luthfi Julio', 'TIU' => 100, 'TWK' => 110, 'TKB' => 180, 'TW' => 340],
            ['name' => 'MAHARANI', 'TIU' => 110, 'TWK' => 90, 'TKB' => 180, 'TW' => 370],
            ['name' => 'Mahen Listianto', 'TIU' => 85, 'TWK' => 105, 'TKB' => 180, 'TW' => 385],
            ['name' => 'Margaretha Lukman', 'TIU' => 85, 'TWK' => 70, 'TKB' => 195, 'TW' => 386],
            ['name' => 'Maulana Akbar', 'TIU' => 95, 'TWK' => 65, 'TKB' => 200, 'TW' => 375],
            ['name' => 'Maulana Azka', 'TIU' => 110, 'TWK' => 70, 'TKB' => 180, 'TW' => 324],
            ['name' => 'Mawar Utami', 'TIU' => 100, 'TWK' => 100, 'TKB' => 170, 'TW' => 389],
            ['name' => 'Milka Putri', 'TIU' => 100, 'TWK' => 100, 'TKB' => 185, 'TW' => 285],
            ['name' => 'Miski Utami', 'TIU' => 110, 'TWK' => 90, 'TKB' => 200, 'TW' => 390],
            ['name' => 'Mozza Gadis', 'TIU' => 150, 'TWK' => 75, 'TKB' => 190, 'TW' => 280],
            ['name' => 'Muhammad Rifki', 'TIU' => 95, 'TWK' => 90, 'TKB' => 185, 'TW' => 300],
            ['name' => 'Muhammad Wijayanto', 'TIU' => 90, 'TWK' => 105, 'TKB' => 190, 'TW' => 250],
            ['name' => 'Ni Komang Noriska', 'TIU' => 100, 'TWK' => 100, 'TKB' => 200, 'TW' => 385],
            ['name' => 'Ningsih Romati', 'TIU' => 100, 'TWK' => 115, 'TKB' => 200, 'TW' => 300],
            ['name' => 'Nova luis', 'TIU' => 95, 'TWK' => 100, 'TKB' => 185, 'TW' => 280],
            ['name' => 'Novita Chayati', 'TIU' => 105, 'TWK' => 75, 'TKB' => 180, 'TW' => 275],
            ['name' => 'Nur Addinda', 'TIU' => 105, 'TWK' => 85, 'TKB' => 170, 'TW' => 378],
            ['name' => 'Oktafias Lukman', 'TIU' => 95, 'TWK' => 65, 'TKB' => 190, 'TW' => 267],
            ['name' => 'Parama Wiastomo', 'TIU' => 85, 'TWK' => 120, 'TKB' => 195, 'TW' => 287],
            ['name' => 'Pradani Susi', 'TIU' => 100, 'TWK' => 90, 'TKB' => 180, 'TW' => 345],
            ['name' => 'Pratama Fihim', 'TIU' => 85, 'TWK' => 75, 'TKB' => 180, 'TW' => 373],
            ['name' => 'Putra Akbar', 'TIU' => 100, 'TWK' => 100, 'TKB' => 215, 'TW' => 387],
            ['name' => 'Putri Utami', 'TIU' => 85, 'TWK' => 75, 'TKB' => 200, 'TW' => 367],
            ['name' => 'Raden Rafi', 'TIU' => 90, 'TWK' => 120, 'TKB' => 195, 'TW' => 297],
            ['name' => 'Raden Rifqi', 'TIU' => 95, 'TWK' => 110, 'TKB' => 200, 'TW' => 290],
            ['name' => 'Rahma Wati', 'TIU' => 85, 'TWK' => 65, 'TKB' => 200, 'TW' => 314],
            ['name' => 'Ramadhan Budi', 'TIU' => 90, 'TWK' => 65, 'TKB' => 180, 'TW' => 346],
            ['name' => 'Restu Astuti', 'TIU' => 90, 'TWK' => 80, 'TKB' => 170, 'TW' => 345],
            ['name' => 'Rhaisa Shafa', 'TIU' => 100, 'TWK' => 100, 'TKB' => 170, 'TW' => 376],
            ['name' => 'Ridho Maulaazka', 'TIU' => 100, 'TWK' => 75, 'TKB' => 180, 'TW' => 345],
            ['name' => 'Rihadatul Aisy', 'TIU' => 100, 'TWK' => 80, 'TKB' => 190, 'TW' => 287],
            ['name' => 'Rizal Putra', 'TIU' => 85, 'TWK' => 70, 'TKB' => 195, 'TW' => 303],
            ['name' => 'Rizki Laulatul', 'TIU' => 100, 'TWK' => 120, 'TKB' => 185, 'TW' => 356],
            ['name' => 'Rizki Pratama', 'TIU' => 100, 'TWK' => 80, 'TKB' => 180, 'TW' => 395],
            ['name' => 'ROSYITA INDAH', 'TIU' => 85, 'TWK' => 90, 'TKB' => 195, 'TW' => 297],
            ['name' => 'Rusydiana Shafira', 'TIU' => 85, 'TWK' => 90, 'TKB' => 180, 'TW' => 359],
            ['name' => 'Salsabila Istiqlal', 'TIU' => 90, 'TWK' => 85, 'TKB' => 180, 'TW' => 348],
            ['name' => 'Sari Firdausi', 'TIU' => 90, 'TWK' => 80, 'TKB' => 215, 'TW' => 387],
            ['name' => 'Sekar Pambayun', 'TIU' => 90, 'TWK' => 80, 'TKB' => 190, 'TW' => 387],
            ['name' => 'Shafira Azzahra', 'TIU' => 95, 'TWK' => 85, 'TKB' => 180, 'TW' => 390],
            ['name' => 'Siska Rahmawati', 'TIU' => 100, 'TWK' => 70, 'TKB' => 195, 'TW' => 345],
            ['name' => 'Siska Utami Putri', 'TIU' => 95, 'TWK' => 90, 'TKB' => 195, 'TW' => 389],
            ['name' => 'Sita Putri Aulia', 'TIU' => 100, 'TWK' => 80, 'TKB' => 200, 'TW' => 309],
            ['name' => 'Sukma Melati', 'TIU' => 100, 'TWK' => 100, 'TKB' => 180, 'TW' => 296],
            ['name' => 'Sulis Tri Utami', 'TIU' => 95, 'TWK' => 90, 'TKB' => 180, 'TW' => 298],
            ['name' => 'Sunarya', 'TIU' => 130, 'TWK' => 100, 'TKB' => 175, 'TW' => 328],
            ['name' => 'Surya Putra', 'TIU' => 120, 'TWK' => 95, 'TKB' => 185, 'TW' => 356],
            ['name' => 'TIARA EKA', 'TIU' => 100, 'TWK' => 90, 'TKB' => 190, 'TW' => 381],
            ['name' => 'Tiara Putri', 'TIU' => 150, 'TWK' => 100, 'TKB' => 180, 'TW' => 261],
            ['name' => 'Titin Rahma', 'TIU' => 150, 'TWK' => 110, 'TKB' => 170, 'TW' => 256],
            ['name' => 'Ulfiani Latifah', 'TIU' => 95, 'TWK' => 85, 'TKB' => 200, 'TW' => 276],
            ['name' => 'Valentina Sekar ', 'TIU' => 120, 'TWK' => 135, 'TKB' => 190, 'TW' => 277],
            ['name' => 'Virgiannisa Sefsani', 'TIU' => 100, 'TWK' => 95, 'TKB' => 180, 'TW' => 289],
            ['name' => 'Wachid Daga', 'TIU' => 120, 'TWK' => 100, 'TKB' => 190, 'TW' => 290],
            ['name' => 'Wildan Nurcholis', 'TIU' => 100, 'TWK' => 105, 'TKB' => 180, 'TW' => 276],
            ['name' => 'Wulandari Siska', 'TIU' => 85, 'TWK' => 95, 'TKB' => 166, 'TW' => 278],
            ['name' => 'Yohanes Wien', 'TIU' => 110, 'TWK' => 80, 'TKB' => 195, 'TW' => 228],
            ['name' => 'Yulfa Surya', 'TIU' => 95, 'TWK' => 105, 'TKB' => 195, 'TW' => 275],
            ['name' => 'Yuli Agar Badi', 'TIU' => 100, 'TWK' => 65, 'TKB' => 180, 'TW' => 285],
            ['name' => 'Yuliana Saraswati', 'TIU' => 150, 'TWK' => 100, 'TKB' => 166, 'TW' => 296],
            ['name' => 'Zaky Zain', 'TIU' => 105, 'TWK' => 100, 'TKB' => 180, 'TW' => 376]
        ];


        // Masukkan pengguna ke tabel users
        foreach ($candidates as $candidate) {
            $user = User::create([
                'name' => $candidate['name'],
                'role' => 'Candidate', // Asumsikan ada kolom role
                'email' => strtolower(str_replace(' ', '.', $candidate['name'])) . '@example.com', // Email unik
                'password' => bcrypt('password123'), // Kata sandi default (gunakan bcrypt untuk hashing)
            ]);

            // Menyimpan skor setiap tes ke dalam tabel test_results
            foreach (['TIU', 'TWK', 'TKB', 'TW'] as $testName) {
                // Temukan id test berdasarkan nama tesnya
                $test = Test::where('name', $testName)->first();

                // Cek apakah test ditemukan
                if ($test) {
                    // Menyimpan skor tes ke dalam tabel test_results
                    TestResult::create([
                        'user_id' => $user->id,
                        'test_id' => $test->id,
                        'score' => $candidate[$testName],
                    ]);
                } else {
                    // Opsional: Log error atau lakukan tindakan lain jika test tidak ditemukan
                    // Log::error("Test not found for name: $testName");
                }
            }
        }

    }
}
