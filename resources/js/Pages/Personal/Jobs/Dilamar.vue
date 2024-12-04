<template>

    <Head title="Lamaran Saya"/>
    <DashboardLayouts>
        <Alert
            v-if="errors.dbError || errors.generalError"
            :message="errors.dbError || errors.generalError"
            class="mt-5 alert-error"
            type="error"/>

        <div class="mt-10 space-y-8">
            <div class="flex items-center justify-between flex-col md:flex-row">
                <div class="">
                    <h1 class="text-2xl font-bold">Lamaran Saya</h1>
                </div>
                <div class=" mt-6 md:mt-0 ">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari Lamaran"
                        class="ring-second block md:w-96 w-60 border-2 rounded-md border-gray-300 shadow-sm focus:border-second focus:ring-second sm:text-sm"
                    />
                </div>
            </div>

            <!-- Loading indicator -->
            <div v-if="isLoading" class="flex h-40 justify-center items-center">
                <span class="loading loading-dots loading-lg"></span>
            </div>

            <div class="" v-if="!isLoading">
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 " v-if="jobs.data.length > 0">
                    <CardJob v-for="job in jobs.data" :key="job.id" :job="job"/>
                </div>
                <div v-else>
                    <p class="text-gray-500">Tidak ada data.</p>
                </div>
            </div>

        </div>
        <div class=" mt-10 text-center lg:text-start ">
            <Pagination :pagination="jobs"/>
        </div>
    </DashboardLayouts>
</template>

<script setup>
// Mengimpor komponen dan hooks yang diperlukan
import CardJob from "@/Components/Card/CardJob.vue"; // Komponen untuk menampilkan kartu pekerjaan
import DashboardLayouts from "@/Layouts/DashboardLayouts.vue"; // Layout untuk halaman dashboard
import { useDebounce } from "@/Composables/index.js"; // Hook untuk debounce pencarian
import { Head, Link, router } from "@inertiajs/vue3"; // Inertia.js untuk menangani navigasi dan routing
import Alert from "@/Components/Alert.vue"; // Komponen untuk menampilkan alert atau pesan kesalahan
import Pagination from "@/Components/Pagination.vue"; // Komponen untuk pagination
import { ref, watch } from "vue"; // Import ref dan watch dari Vue

// Mendefinisikan props yang diterima oleh komponen ini
const props = defineProps({
    jobs: {
        type: Object,
        required: true, // Harus ada daftar pekerjaan yang diberikan
    },
    search: {
        type: String, // Mencari pekerjaan berdasarkan string
    },
    errors: {
        type: Object, // Menyimpan error yang muncul
    }
});

// Mengatur state untuk pencarian dan status loading
const search = ref(props.search || ''); // Variabel reaktif untuk pencarian
const isLoading = ref(false); // Variabel untuk menandakan status loading

// Menggunakan debounce untuk menghindari pencarian yang berlebihan saat mengetik
const debouncedSearch = useDebounce(search, 500); // Pencarian yang ditunda selama 500ms

// Mengamati perubahan pada pencarian yang dibersihkan dengan debounce
watch(debouncedSearch, (newSearch) => {
    isLoading.value = true; // Menandakan bahwa pencarian sedang dilakukan

    // Melakukan permintaan GET untuk memuat data berdasarkan pencarian
    router.get(
        route('applications.proposed'), // Route untuk mendapatkan aplikasi yang diusulkan
        { search: newSearch }, // Mengirimkan parameter pencarian
        {
            preserveState: true, // Mempertahankan status halaman saat navigasi
            replace: true, // Mengganti URL tanpa menambahkannya ke riwayat
            onFinish: () => {
                isLoading.value = false; // Menandakan pencarian selesai
            }
        }
    );
});
</script>

<style scoped></style>
