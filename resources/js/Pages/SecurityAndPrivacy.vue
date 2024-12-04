<template>
    <Head title="Kebijakan Keamanan dan Privasi" />
    <Navbar/>
    <div class="max-w-7xl mx-auto mt-10 px-4">
        <h1 class="text-3xl font-bold text-center mb-8">Kebijakan Keamanan dan Privasi</h1>
        <div class="space-y-5">
            <CollapseBox
                v-for="(section, index) in securityPrivacyTerms"
                :key="index"
                :title="section.title"
                :content="section.content"
            />
        </div>
    </div>

    <Footer/>
</template>

<script setup>
import CollapseBox from '@/Components/CollapseBox.vue';
import Footer from '@/Components/Footer.vue';
import Navbar from '@/Components/Navbar.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const securityPrivacyTerms = ref([
    {
        title: "Pengumpulan Informasi Pribadi",
        content: "Kami mengumpulkan informasi pribadi yang Anda berikan secara langsung saat mendaftar, menggunakan layanan, atau berinteraksi dengan platform kami. Informasi yang dikumpulkan mencakup nama, alamat email, nomor telepon, alamat, dan data identitas lainnya yang diperlukan untuk keperluan verifikasi dan layanan."
    },
    {
        title: "Jenis Data yang Dikumpulkan",
        content: "Kami mengumpulkan beberapa jenis data, termasuk: (1) Data pribadi yang Anda berikan, (2) Data penggunaan platform, (3) Lokasi geografis, (4) Informasi perangkat yang digunakan, (5) Riwayat interaksi dengan platform, (6) Cookies dan teknologi pelacakan serupa."
    },
    {
        title: "Tujuan Penggunaan Data",
        content: "Data pribadi Anda digunakan untuk: (1) Autentikasi dan verifikasi akun, (2) Penyediaan layanan yang diminta, (3) Personalisasi pengalaman pengguna, (4) Komunikasi terkait layanan, (5) Peningkatan kualitas platform, (6) Kepatuhan hukum dan regulasi."
    },
    {
        title: "Keamanan Data",
        content: "Kami menerapkan standar keamanan tingkat lanjut untuk melindungi data Anda, termasuk: (1) Enkripsi data menggunakan protokol SSL/TLS, (2) Penyimpanan data terenkripsi, (3) Pembatasan akses data internal, (4) Pemantauan berkala terhadap potensi kerentanan, (5) Cadangan data berkala, (6) Protokol keamanan multi-lapis."
    },
    {
        title: "Pembagian Informasi",
        content: "Kami tidak menjual atau menyewakan data pribadi Anda kepada pihak ketiga. Data dapat dibagikan dalam situasi terbatas: (1) Dengan persetujuan eksplisit Anda, (2) Untuk kepatuhan hukum, (3) Untuk melindungi hak dan keamanan kami, (4) Dengan penyedia layanan yang terikat perjanjian kerahasiaan."
    },
    {
        title: "Hak Pengguna",
        content: "Anda memiliki hak untuk: (1) Mengakses data pribadi Anda, (2) Meminta koreksi data yang tidak akurat, (3) Meminta penghapusan akun dan data, (4) Membatasi atau menolak pemrosesan data, (5) Menarik persetujuan penggunaan data, (6) Mengajukan komplain kepada otoritas perlindungan data."
    },
    {
        title: "Cookies dan Teknologi Pelacakan",
        content: "Kami menggunakan cookies dan teknologi serupa untuk: (1) Mengoptimalkan kinerja platform, (2) Menganalisis pola penggunaan, (3) Menyediakan fitur personalisasi, (4) Mengumpulkan data analitik. Anda dapat mengatur preferensi cookies melalui pengaturan peramban Anda."
    },
    {
        title: "Keamanan Transaksi",
        content: "Untuk transaksi online, kami menerapkan: (1) Enkripsi end-to-end, (2) Autentikasi dua faktor, (3) Pemindaian keamanan berkala, (4) Perlindungan terhadap serangan cyber, (5) Protokol keamanan payment gateway terverifikasi."
    },
    {
        title: "Pembaruan Kebijakan",
        content: "Kebijakan privasi ini dapat diperbarui sewaktu-waktu. Perubahan signifikan akan diumumkan melalui platform. Penggunaan berkelanjutan setelah pengumuman perubahan dianggap sebagai persetujuan terhadap ketentuan baru."
    },
    {
        title: "Kontak dan Pertanyaan",
        content: "Jika Anda memiliki pertanyaan atau keberatan terkait privasi dan keamanan data, silakan hubungi tim kami melalui: (1) Email: privacy@namaplatform.com, (2) Nomor telepon dukungan, (3) Formulir kontak resmi di platform kami."
    }
]);
</script>

<style scoped>
/* Optional: Add any additional styling */
</style>
