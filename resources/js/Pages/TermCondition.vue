<template>
    <Head title="Syarat & Ketentuan" />
    <DashboardLayouts>
        <div class="max-w-7xl mx-auto mt-10 px-4">
            <h1 class="text-3xl font-bold text-center mb-8">Syarat & Ketentuan</h1>
            <div class="space-y-5">
                <CollapseBox
                    v-for="(section, index) in jobPortalTerms"
                    :key="index"
                    :title="section.title"
                    :content="section.content"
                />
            </div>
        </div>

    </DashboardLayouts>
</template>

<script setup>
import CollapseBox from '@/Components/CollapseBox.vue';
import Footer from '@/Components/Footer.vue';
import Navbar from '@/Components/Navbar.vue';
import { ref } from 'vue';
import DashboardLayouts from "@/Layouts/DashboardLayouts.vue";
import { Head } from '@inertiajs/vue3';

const jobPortalTerms = ref([
    {
        title: "Ketentuan Penggunaan Umum",
        content: "Portal lowongan kerja ini menyediakan platform untuk pencari kerja dan pemberi kerja. Dengan mendaftar dan menggunakan layanan kami, Anda setuju untuk mematuhi syarat dan ketentuan yang tercantum. Setiap pengguna bertanggung jawab atas kebenaran informasi yang diberikan."
    },
    {
        title: "Pendaftaran Pengguna",
        content: "Untuk menggunakan layanan kami, Anda wajib mendaftar dengan informasi yang akurat dan terkini. Akun pribadi Anda bersifat tidak dapat dipindahtangankan. Kami berhak untuk menolak, membatasi, atau membatalkan akun yang dicurigai melakukan aktivitas tidak sah atau melanggar ketentuan."
    },
    {
        title: "Privasi dan Perlindungan Data",
        content: "Kami berkomitmen melindungi data pribadi Anda. Informasi yang Anda berikan hanya akan digunakan untuk keperluan pencarian kerja dan akan kami jaga kerahasiaannya. Kami tidak akan membagikan data pribadi Anda kepada pihak ketiga tanpa izin eksplisit dari Anda."
    },
    {
        title: "Kewajiban Pencari Kerja",
        content: "Sebagai pencari kerja, Anda berkewajiban untuk: (1) Memberikan informasi pribadi dan profesional yang akurat, (2) Menjaga kerahasiaan kredensial akun, (3) Tidak menggunakan platform untuk tujuan yang melanggar hukum, (4) Menghormati hak-hak pemberi kerja dan pengguna lain."
    },
    {
        title: "Kewajiban Pemberi Kerja",
        content: "Pemberi kerja wajib: (1) Memberikan deskripsi lowongan yang akurat dan jelas, (2) Tidak mendiskriminasi pelamar berdasarkan usia, jenis kelamin, agama, atau latar belakang, (3) Melindungi data pribadi pelamar, (4) Memberikan informasi kompensasi yang transparan."
    },
    {
        title: "Larangan dan Batasan",
        content: "Dilarang keras: (1) Menggunakan platform untuk penipuan atau aktivitas ilegal, (2) Mengirim lamaran atau iklan spam, (3) Membuat akun palsu, (4) Merusak atau mengganggu fungsionalitas platform, (5) Mengumpulkan data pengguna tanpa izin."
    },
    {
        title: "Tanggung Jawab Hukum",
        content: "Platform ini hanya bertindak sebagai perantara. Kami tidak bertanggung jawab secara langsung atas: (1) Kebenaran informasi lowongan, (2) Proses wawancara atau perekrutan, (3) Konflik antara pencari kerja dan pemberi kerja, (4) Kompensasi atau kondisi kerja."
    },
    {
        title: "Biaya dan Pembayaran",
        content: "Pendaftaran dasar untuk pencari kerja bersifat gratis. Layanan premium atau tambahan mungkin dikenakan biaya sesuai ketentuan yang berlaku. Pemberi kerja yang ingin memasang lowongan dapat dikenakan biaya sesuai paket yang dipilih."
    },
    {
        title: "Perubahan Syarat dan Ketentuan",
        content: "Kami berhak mengubah syarat dan ketentuan sewaktu-waktu. Perubahan akan diumumkan di platform. Penggunaan berkelanjutan setelah pengumuman perubahan dianggap sebagai persetujuan terhadap ketentuan baru."
    }
]);
</script>

<style scoped>
/* Optional: Add any additional styling */
</style>
