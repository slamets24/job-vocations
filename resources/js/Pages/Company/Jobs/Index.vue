<template>

    <Head title="Lowongan Diposting"/>
    <DashboardLayouts>
        <Alert
            v-if="errors.dbError || errors.generalError"
            :message="errors.dbError || errors.generalError"
            class="mt-5 alert-error"
            type="error"/>

        <div class="mt-10 space-y-8">
            <div class="flex items-center justify-between flex-col md:flex-row">
                <div class="">
                    <h1 class="text-2xl font-bold">Lowongan Diposting</h1>
                </div>
                <div class=" mt-6 md:mt-0 ">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari Lowongan"
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
                    <p class="text-gray-500">Tidak ada pekerjaan yang diposting.</p>
                </div>
            </div>

        </div>
        <div class=" mt-10 text-center lg:text-start ">
            <Pagination :pagination="jobs"/>
        </div>
    </DashboardLayouts>
</template>

<script setup>
// Import komponen dan utilitas
import CardJob from "@/Components/Card/CardJob.vue"; // Komponen untuk menampilkan detail pekerjaan
import DashboardLayouts from "@/Layouts/DashboardLayouts.vue"; // Layout untuk dashboard
import { useDebounce } from "@/Composables/index.js"; // Composable kustom untuk mendebounce input pencarian
import { Head, Link, router } from "@inertiajs/vue3"; // Inertia.js untuk menangani routing dan data halaman
import Alert from "@/Components/Alert.vue"; // Komponen untuk menampilkan notifikasi
import Pagination from "@/Components/Pagination.vue"; // Komponen untuk menampilkan pagination daftar pekerjaan
import { ref, watch } from "vue"; // Utilitas dari Vue Composition API untuk reaktivitas dan memantau perubahan state

// Mendefinisikan props untuk menerima data dari komponen induk
const props = defineProps({
    jobs: { // Daftar pekerjaan yang akan ditampilkan
        type: Object,
        required: true,
    },
    search: { // String query pencarian yang diteruskan dari komponen induk
        type: String,
    },
    errors: { // Error validasi (jika ada) yang diteruskan dari komponen induk
        type: Object
    }
});

// State reaktif untuk input pencarian dan status loading
const search = ref(props.search || ''); // Menginisialisasi pencarian dengan nilai prop yang diteruskan atau string kosong
const isLoading = ref(false); // Flag untuk menandakan status loading saat melakukan permintaan API

// Menggunakan debounce untuk membatasi frekuensi pembaruan query pencarian
const debouncedSearch = useDebounce(search, 500); // Mendebounce input pencarian selama 500ms

// Memantau perubahan pada debouncedSearch dan melakukan pencarian baru
watch(debouncedSearch, (newSearch) => {
    isLoading.value = true; // Mengatur status loading menjadi true ketika memulai pencarian baru

    // Melakukan permintaan GET dengan nilai pencarian yang diperbarui menggunakan router Inertia.js
    router.get(
        route('jobs.index'), // Rute untuk mengambil daftar pekerjaan
        { search: newSearch }, // Meneruskan query pencarian ke rute
        {
            preserveState: true, // Mempertahankan state halaman (misalnya parameter query URL)
            replace: true, // Mengganti URL saat ini tanpa menambah entri baru di riwayat
            onFinish: () => {
                isLoading.value = false; // Mengatur status loading menjadi false setelah permintaan selesai
            }
        }
    );
});
</script>

<style scoped></style>
