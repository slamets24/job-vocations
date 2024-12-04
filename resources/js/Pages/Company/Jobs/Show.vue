<template>

    <Head title="Detail Lowongan"/>
    <DashboardLayouts>
        <div class="breadcrumbs text-sm mt-1">
            <ul v-if="user?.type === 'company'">
                <li>
                    <Link :href="route('company.dashboard')">Dashboard</Link>
                </li>
                <li>
                    <Link :href="route('jobs.index')">Lowongan</Link>
                </li>
                <li>Detail Lowongan</li>
            </ul>

            <ul v-else>
                <li>
                    <Link :href="route('home')">Cari Lowongan</Link>
                </li>
                <li>Detail Lowongan</li>
            </ul>
        </div>

        <div class="mt-4">
            <div class="flex items-center md:items-start justify-between w-full">

                <div class="avatar">
                    <div class="w-24 rounded">
                        <img
                            :src="job.image"/>
                    </div>
                </div>
                <div class="" v-if="user?.type === 'company'">
                    <Link class="btn btn-sm btn-outline px-10 text-second hover:border-second hover:bg-second"
                          :href="route('jobs.edit', job.slug)">Ubah Lowongan
                    </Link>
                </div>
            </div>
            <div class=" mt-4">
                <h1 class="text-2xl font-semibold">{{ job.title }}
                    <span v-if="!job.status" class="capitalize badge bg-red-500 text-white">ditutup</span>
                </h1>
                <p class="text-lg ">{{ job.company_name }}</p>
                <div class="mt-4 space-y-3">
                    <p class="text-md flex items-center gap-2"><span><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                          height="24" viewBox="0 0 24 24"
                                                                          style="fill: rgba(0, 0, 0, 1);">
                                <path
                                    d="M11.42 21.815a1.004 1.004 0 0 0 1.16 0C12.884 21.598 20.029 16.44 20 10c0-4.411-3.589-8-8-8S4 5.589 4 9.996c-.029 6.444 7.116 11.602 7.42 11.819zM12 4c3.309 0 6 2.691 6 6.004.021 4.438-4.388 8.423-6 9.731-1.611-1.308-6.021-5.293-6-9.735 0-3.309 2.691-6 6-6z">
                                </path>
                                <path d="M11 14h2v-3h3V9h-3V6h-2v3H8v2h3z"></path>
                            </svg></span>{{ job.city_name }}, {{ job.province_name }}
                    </p>

                    <p class="capitalize flex gap-2 items-center text-md"><span><svg xmlns="http://www.w3.org/2000/svg"
                                                                                     width="24" height="24"
                                                                                     viewBox="0 0 24 24"
                                                                                     style="fill: rgba(0, 0, 0, 1);">
                                <path
                                    d="M21 10h-2V4h1V2H4v2h1v6H3a1 1 0 0 0-1 1v9h20v-9a1 1 0 0 0-1-1zm-7 8v-4h-4v4H7V4h10v14h-3z">
                                </path>
                                <path d="M9 6h2v2H9zm4 0h2v2h-2zm-4 4h2v2H9zm4 0h2v2h-2z"></path>
                            </svg></span>{{ job.education }}
                    </p>
                    <p class="text-md flex items-center gap-2  capitalize"><span><svg xmlns="http://www.w3.org/2000/svg"
                                                                                      width="24" height="24"
                                                                                      viewBox="0 0 24 24"
                                                                                      style="fill: rgba(0, 0, 0, 1);">
                                <path
                                    d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z">
                                </path>
                                <path d="M13 7h-2v6h6v-2h-4z"></path>
                            </svg></span>{{ job.job_type }}
                    </p>
                    <p class="text-md flex gap-2 items-center"><span><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                          height="24"
                                                                          viewBox="0 0 24 24"
                                                                          style="fill: rgba(0, 0, 0, 1);">
                                <path
                                    d="M21 4H3a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1zm-1 11a3 3 0 0 0-3 3H7a3 3 0 0 0-3-3V9a3 3 0 0 0 3-3h10a3 3 0 0 0 3 3v6z">
                                </path>
                                <path
                                    d="M12 8c-2.206 0-4 1.794-4 4s1.794 4 4 4 4-1.794 4-4-1.794-4-4-4zm0 6c-1.103 0-2-.897-2-2s.897-2 2-2 2 .897 2 2-.897 2-2 2z">
                                </path>
                            </svg></span>{{ formatRupiah(job.salary) }} per bulan
                    </p>
                    <p class="text-md flex gap-2 items-center">
                        <span>
                           <i class='bx bxs-buildings bx-sm'></i>
                        </span>{{ job.job_classification_name }}</p>
                </div>
                <p class="text-md font-bold text-second pt-2"> Diunggah pada {{ job.created_at }}</p>
            </div>

            <div v-if="job.application_status" class="inline-block space-y-2 mt-10">
                <p class="font-semibold text-lg">Status Lamaran:</p>
                <ApplicationStatus class="bg-blue-500 text-white p-4 rounded" :status="job.application_status"/>
            </div>

            <!-- tampilkan saat tidak login atau login sebagai personal -->
            <div v-if="user?.type !== 'company' && !job.application_status" class="flex gap-4 mt-5">
                <Link :href="route('applications.create', job.slug)" class="px-10 text-white btn bg-second">
                    Lamar Sekarang
                </Link>
            </div>

            <div class="mt-10 space-y-1">
                <h1 class="text-2xl font-semibold">Deskripsi Pekerjaan</h1>
                <p class="text-base" v-html="descriptionFormatted"></p>
            </div>
        </div>

        <!-- Tampilkan saat tidak login atau login sebagai personal-->
        <div v-if="user?.type !== 'company'" class="mt-10 space-y-4">
            <div class="">
                <h1 class="text-2xl font-semibold">Profil Perusahaan</h1>
            </div>
            <CompanyProfile :job/>
        </div>

        <!-- Tampilkan saat tidak login atau login sebagai personal-->
        <div v-if="user?.type !== 'company'" class="my-10">
            <h3 class="text-xl font-semibold">Hati - Hati</h3>
            <p class="mt-2 text-gray-500">Jangan berikan detail bank atau kartu kredit kamu saat mengirimkan lamaran
                kerja.</p>
        </div>
    </DashboardLayouts>
</template>
<script setup>
// Mengimpor komponen dan utilitas yang diperlukan
import DashboardLayouts from '@/Layouts/DashboardLayouts.vue'; // Layout untuk dashboard
import { Head, Link, usePage } from '@inertiajs/vue3'; // Menggunakan Inertia.js untuk manajemen halaman dan link
import { computed } from "vue"; // Menggunakan Composition API dari Vue
import CompanyProfile from "@/Components/Company/CompanyProfile.vue"; // Komponen profil perusahaan
import ApplicationStatus from "@/Components/ApplicationStatus.vue"; // Komponen status aplikasi

// Mengambil data halaman menggunakan usePage dan menyimpan data pengguna (auth user)
const page = usePage();
const user = computed(() => page.props.auth.user); // Menyimpan informasi pengguna yang sedang login dalam bentuk reaktif

// Mendefinisikan props yang diterima oleh komponen ini
const props = defineProps({
    job: { // Data pekerjaan yang diteruskan dari komponen induk
        type: Object,
        required: true,
    },
    company: { // Data perusahaan yang diteruskan, bersifat opsional
        type: Object,
        required: false,
        default: () => ({ // Jika tidak ada data perusahaan, menggunakan nilai default
            name: "Unknown Company", // Nama perusahaan default
            slug: "", // Slug perusahaan default
            image_profile: "https://via.placeholder.com/150", // Gambar profil default
        }),
    },
});

// Fungsi untuk memformat gaji menjadi format mata uang Rupiah
const formatRupiah = (number) => {
    if (number == null) return "Rp. 0"; // Jika salary tidak tersedia, tampilkan Rp. 0
    return new Intl.NumberFormat("id-ID", { // Menggunakan Intl.NumberFormat untuk format uang Indonesia
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0, // Tidak menampilkan pecahan desimal
    }).format(number);
};

// Menghitung dan memformat deskripsi pekerjaan, mengganti newline dengan <br> untuk elemen HTML
const descriptionFormatted = computed(() => {
    return props.job.description.replace(/\n/g, '<br>'); // Mengganti setiap karakter newline dengan tag <br>
});
</script>
