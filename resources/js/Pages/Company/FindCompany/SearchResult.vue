<template>
    <Head title="Cari Perusahaan"/>

    <DashboardLayouts>
        <div class="lg:px-8 py-8 min-h-dvh">

            <!-- Jika ada query, tampilkan judul 'Hasil pencarian dari {{ query }}'-->
            <h1 v-if="query" class="text-2xl sm:text-3xl font-semibold text-gray-900 mb-6">
                Hasil pencarian dari '{{ query }}'
            </h1>

            <div v-else class="text-2xl sm:text-3xl font-semibold text-gray-900 mb-6">
                Cari Perusahaan
            </div>

            <!-- Search Input -->
            <div class="relative mb-12">
                <input
                    type="text"
                    placeholder="Cari Perusahaan dengan nama"
                    v-model="query"
                    class="w-full px-4 py-3 pl-10 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-second focus:border-transparent"
                />
                <span
                    class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
                >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
            </span>
            </div>

            <div v-if="isLoading" class="flex h-40 justify-center items-center">
                <span class="loading loading-dots loading-lg"></span>
            </div>

            <!-- Search Results -->
            <div v-if="companies.length > 0 && !isLoading">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">
                    Menemukan Perusahaan:
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <CardCompany
                        v-for="company in companies"
                        :key="company.id"
                        :company
                    />
                </div>
            </div>

            <!-- No Results State -->
            <div v-if="companies.length === 0 && !isLoading">
                <!-- Illustration -->
                <div class="mb-6 relative w-48 h-48 mx-auto">
                    <!-- Hand -->
                    <div
                        class="absolute right-12 top-12 w-16 h-16 transform -rotate-12"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="w-full h-full text-[#FFA07A]"
                            fill="currentColor"
                        >
                            <path
                                d="M21.87 10.42c-.35-.37-.85-.6-1.37-.6h-5V8.5c0-1.38-1.12-2.5-2.5-2.5S10.5 7.12 10.5 8.5v1.32H9.37c-.52 0-1.02.23-1.37.6-.34.36-.53.84-.53 1.33v7.75c0 1.38 1.12 2.5 2.5 2.5h10c1.38 0 2.5-1.12 2.5-2.5v-7.75c0-.49-.19-.97-.53-1.33z"
                            />
                        </svg>
                    </div>

                    <!-- Puzzle Pieces -->
                    <div class="absolute left-12 bottom-12 flex">
                        <div
                            class="w-12 h-12 bg-[#FF69B4] rounded-lg transform rotate-45 mr-[-0.5rem]"
                        ></div>
                        <div
                            class="w-12 h-12 bg-[#87CEEB] rounded-lg transform rotate-12"
                        ></div>
                    </div>

                    <!-- Decorative Elements -->
                    <div
                        class="absolute left-8 top-8 w-4 h-4 bg-[#98FB98] rounded-full opacity-50"
                    ></div>
                </div>

                <!-- Message -->
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    Tidak ada yang cocok dari kata kunci
                </h2>
            </div>
        </div>

    </DashboardLayouts>
</template>

<script setup>
// Import library dan komponen yang dibutuhkan
import { ref, computed, watch } from "vue"; // API reaktif dan watch dari Vue
import CardCompany from "@/Components/Card/CardCompany.vue"; // Komponen untuk menampilkan data perusahaan dalam bentuk kartu
import { Head, router } from "@inertiajs/vue3"; // Komponen dan fungsi dari Inertia.js
import Footer from "@/Components/Footer.vue"; // Komponen footer
import Navbar from "@/Components/Navbar.vue"; // Komponen navbar
import DashboardLayouts from "@/Layouts/DashboardLayouts.vue"; // Layout untuk halaman dashboard
import { useDebounce } from "@/Composables/index.js"; // Fungsi debounce untuk menghindari terlalu banyak panggilan API selama pencarian

// Props yang diterima dari server
const props = defineProps({
    companies: {
        type: Object, // Data perusahaan
        required: true, // Data ini wajib ada
    },
    query: {
        type: String, // Kata kunci pencarian awal
        required: true, // Data ini wajib ada
    }
});

// State untuk menandai proses loading
const isLoading = ref(false);

// State untuk menyimpan input pencarian pengguna
const query = ref(props.query || ''); // Default diambil dari props atau string kosong

// Menggunakan debounce untuk menunda pengiriman pencarian sampai input stabil
const debouncedSearch = useDebounce(query, 500); // Fungsi pencarian baru akan dipanggil setelah 500ms sejak input terakhir

// Mengamati perubahan pada debouncedSearch (hasil debounce query)
watch(debouncedSearch, (newSearch) => {
    isLoading.value = true; // Tandai bahwa data sedang dimuat

    // Mengirim permintaan pencarian ke server
    router.get(
        route('companies.search'), // Endpoint untuk pencarian perusahaan
        { search: newSearch }, // Kirim parameter pencarian
        {
            preserveState: true, // Menjaga state halaman agar tidak berubah
            replace: true, // Mengganti URL tanpa menambah riwayat baru
            onFinish: () => {
                isLoading.value = false; // Proses pemuatan selesai
            }
        }
    );
});
</script>
