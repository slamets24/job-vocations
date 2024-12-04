<template>

    <Head title="Dashboard Perusahaan" />

    <!-- Pastikan hanya perusahaan yang dapat melihat dashboard ini -->
    <DashboardLayouts>
        <div v-if="userType === 'company'">
            <div class="py-10 space-y-3">
                <h1 class="text-3xl font-bold">Selamat Datang</h1>
                <p class="font-semibold text-gray-500 text-md">{{ currentDate }}</p>
            </div>
            <div class="flex flex-col justify-between gap-4 lg:flex-row">
                <CardDashboard title="Lowongan Diposting" :total="totalPostedJobs" class="bg-[#C6F5C1]" />
                <CardDashboard title="Lowongan Ditutup" :total="totalClosedJobs" class="bg-[#FFBAD9]" />
                <CardDashboard title="Lowongan Diambil" :total="totalTakenJobs" class="bg-[#D6D0FE]" />
            </div>
            <div class="mt-10 space-y-4">
                <div class="flex justify-between flex-col md:flex-row">
                    <h1 class="text-xl font-bold">
                        Daftar Lowongan Pekerjaan yang Diposting
                    </h1>
                    <div class=" mt-6 md:mt-0  ">
                        <input type="text" v-model="search" placeholder="Cari Lowongan"
                            class="ring-second block md:w-96 w-full border-2 rounded-md border-gray-300 shadow-sm focus:border-second focus:ring-second sm:text-sm" />
                    </div>
                </div>
                <div v-if="jobs.length > 0">
                    <div class="overflow-x-auto">
                        <table class="table table-zebra">
                            <!-- head -->
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Lowongan</th>
                                    <th>Tanggal Diposting</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(job, index) in filteredJobs" :key="job.id">
                                    <th>{{ index + 1 }}</th>
                                    <td>{{ job.title }}</td>
                                    <td>{{ getCurrentDate(job.created_at) }} </td>
                                    <td>
                                        <Link :href="route('show.applicants', job.slug)" class="w-full btn btn-sm bg-second text-white">Check
                                        Pelamar
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="filteredJobs.length > 4" class="join mt-4">
                        <button class="join-item btn-sm">«</button>
                        <button class="join-item btn btn-sm">1</button>
                        <button class="join-item btn btn-sm ">2</button>
                        <button class="join-item btn btn-sm btn-disabled">...</button>
                        <button class="join-item btn btn-sm">4</button>
                        <button class="join-item btn-sm">»</button>
                    </div>
                </div>
                <div v-else>
                    <p class="text-gray-500">Tidak ada pekerjaan yang diposting.</p>
                </div>
            </div>
        </div>
    </DashboardLayouts>
    <!-- Tampilkan pesan jika bukan perusahaan -->
</template>


<script setup>
// Import komponen dan library yang diperlukan
import CardDashboard from "@/Components/Card/CardDashboard.vue"; // Komponen kartu untuk dashboard
import DashboardLayouts from "@/Layouts/DashboardLayouts.vue"; // Layout dashboard
import Button from "@/Components/Button/Button.vue"; // Komponen tombol
import { Head, usePage, Link } from "@inertiajs/vue3"; // Komponen Inertia.js
import { ref, onMounted, computed } from "vue"; // API reaktif dan lifecycle dari Vue

// Ambil data dari halaman melalui props
const { props } = usePage();
const user = props.auth.user; // Data pengguna yang sedang login
const userType = user?.type; // Tipe pengguna

// Ambil data pekerjaan yang dihitung dari server
const totalPostedJobs = props.totalPostedJobs || 0; // Total pekerjaan yang diposting
const totalClosedJobs = props.totalClosedJobs || 0; // Total pekerjaan yang ditutup
const totalTakenJobs = props.totalTakenJobs || 0; // Total pekerjaan yang diambil

// State untuk menyimpan tanggal saat ini
const currentDate = ref("");

// State untuk input pencarian
const search = ref("");

// Data pekerjaan yang diterima dari server
const jobs = ref(props.jobs || []); // Daftar pekerjaan

// Properti terhitung untuk menyaring pekerjaan berdasarkan input pencarian
const filteredJobs = computed(() => {
    if (!search.value) {
        return jobs.value; // Jika tidak ada input pencarian, tampilkan semua pekerjaan
    }
    return jobs.value.filter((job) =>
        job.title.toLowerCase().includes(search.value.toLowerCase()) // Filter pekerjaan berdasarkan judul
    );
});

// Fungsi untuk mendapatkan tanggal saat ini dalam format lokal
const getCurrentDate = () => {
    const options = {
        weekday: "long", // Nama hari (Senin, Selasa, dll.)
        year: "numeric", // Tahun (2024, dll.)
        month: "long", // Nama bulan lengkap
        day: "numeric", // Tanggal (1, 2, dll.)
    };
    const today = new Date();
    return today.toLocaleDateString("id-ID", options); // Format ke lokal Indonesia
};

// Lifecycle hook yang dijalankan saat komponen di-mount
onMounted(() => {
    currentDate.value = getCurrentDate(); // Tetapkan tanggal saat ini ke state
});
</script>

<style scoped></style>
