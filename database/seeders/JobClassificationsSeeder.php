<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobClassificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('job_classifications')->insert(
            [
                ['name' => 'Akuntansi'],
                ['name' => 'Administrasi & Dukungan Perkantoran'],
                ['name' => 'Periklanan, Seni & Media'],
                ['name' => 'Perbankan & Layanan Finansial'],
                ['name' => 'Call Center & Layanan Konsumen'],
                ['name' => 'CEO & Manajemen Umum'],
                ['name' => 'Layanan & Pengembangan Masyarakat'],
                ['name' => 'Konstruksi'],
                ['name' => 'Konsultasi & Strategi'],
                ['name' => 'Desain & Arsitektur'],
                ['name' => 'Pendidikan & Pelatihan'],
                ['name' => 'Teknik'],
                ['name' => 'Pertanian, Hewan & Konservasi'],
                ['name' => 'Pemerintahan & Pertahanan'],
                ['name' => 'Kesehatan & Medis'],
                ['name' => 'Hospitaliti & Pariwisata'],
                ['name' => 'Sumber Daya Manusia & Perekrutan'],
                ['name' => 'Teknologi Informasi & Komunikasi'],
                ['name' => 'Asuransi & Dana Pensiun'],
                ['name' => 'Hukum'],
                ['name' => 'Manufaktur, Transportasi & Logistik'],
                ['name' => 'Pemasaran & Komunikasi'],
                ['name' => 'Pertambangan, Sumber Daya Alam & Energi'],
                ['name' => 'Real Estat & Properti'],
                ['name' => 'Ritel & Produk Konsumen'],
                ['name' => 'Penjualan'],
                ['name' => 'Sains & Teknologi'],
                ['name' => 'Pekerjaan Lepas'],
                ['name' => 'Olahraga & Rekreasi'],
                ['name' => 'Keterampilan & Jasa']
            ]
        );
    }
}
