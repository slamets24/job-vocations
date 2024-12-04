<template>

    <Head title="Cek Status Pelamar"/>
    <DashboardLayouts>
        <div class="mt-10">
            <h1 class="text-2xl font-semibold">Cek Status Pelamar</h1>
        </div>
        <div class="mt-6">
            <div class="mt-6 md:mt-0 mb-2 text-end">
                <input v-model="searchQuery" type="text" placeholder="Cari Pelamar"
                       class="ring-second block md:w-96 w-full border-2 rounded-md border-gray-300 shadow-sm focus:border-second focus:ring-second sm:text-sm"/>
            </div>
            <div class="overflow-x-auto">
                <table class="table table-md">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Tanggal Melamar</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="filteredApplicants.length === 0">
                            <td colspan="7" class="text-center py-4">Belum ada yang mendaftar di lowongan kerja ini</td>
                        </tr>
                        <tr v-else v-for="(applicant, index) in filteredApplicants" :key="index">
                            <td>{{ index + 1 }}</td>
                            <td>{{ applicant.full_name }}</td>
                            <td>{{ applicant.user_email }}</td>
                            <td>
                                <ApplicationStatus
                                    :status="applicant.status"
                                    class="text-xs"
                                />
                            </td>
                            <td>{{ formatDate(applicant.application_date) }}</td>
                            <td>
                                <Link class="btn btn-sm px-4 bg-second text-white" :href="route('applications.detailApplicant', [applicant.slugJob, applicant.slugApplicant])">
                                    Lihat
                                </Link>

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </DashboardLayouts>
</template>


<script setup>
// Import komponen dan library yang dibutuhkan
import { ref, computed } from 'vue'; // Vue.js reactivity
import { Head, Link } from '@inertiajs/vue3'; // Inertia.js untuk routing dan header
import DashboardLayouts from '@/Layouts/DashboardLayouts.vue'; // Layout dashboard
import ApplicationStatus from "@/Components/ApplicationStatus.vue"; // Komponen untuk menampilkan status aplikasi

// Props yang diterima dari server, berisi data pelamar
const props = defineProps(['applicants']); // Menerima data pelamar dari parent component

// State untuk pencarian berdasarkan nama pelamar
const searchQuery = ref(''); // Menyimpan query pencarian
const selectedApplicant = ref(null); // Menyimpan pelamar yang dipilih

// Filter pelamar berdasarkan pencarian
const filteredApplicants = computed(() =>
    (props.applicants || []).filter((applicant) =>
        applicant.full_name.toLowerCase().includes(searchQuery.value.toLowerCase()) // Pencarian berdasarkan nama pelamar
    )
);

// Fungsi untuk format tanggal menjadi format Indonesia
const formatDate = (date) => {
    if (!date) return '-'; // Jika tanggal tidak ada, kembalikan tanda '-'
    const options = { year: 'numeric', month: 'long', day: 'numeric' }; // Format tanggal
    return new Date(date).toLocaleDateString('id-ID', options); // Mengembalikan tanggal dalam format Indonesia
};
</script>
