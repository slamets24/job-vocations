<template>
    <Head title="Profil Pelamar" />

    <DashboardLayouts>
        <BlockUi message="Sedang Mengubah Status" :is-loading="form.processing"/>

        <Alert
            v-if="errors.testExists || errors.hasTest"
            :message="errors.testExists || errors.hasTest"
            class="mt-5 mb-10 alert-error"
            type="error"
        />

        <div
            class="bg-second sticky top-16 z-10 p-4 gap-2 flex-col md:flex-row rounded-md flex items-center justify-between">
            <h1 class="text-white font-semibold">Telah Dilamar: {{formatDate(personal.application_date)}}</h1>
            <div class="relative">
                <div class="flex items-center border font-semibold rounded-md text-white">
                    <!-- Tombol utama -->
                    <button
                        v-for="(label, key) in mainButtons"
                        :key="key"
                        @click="updateStatus(key)"
                        class="border-r hover:bg-white hover:text-second px-1.5 md:px-2 lg:px-4"
                    >
                        {{ label }}
                    </button>

                    <button
                        class="border-r hover:bg-white rounded-r-md hover:text-second px-1.5 md:px-2 lg:px-4"
                        @click="toggleDropdown"
                        ref="ellipsisButton"
                        :class="{ 'bg-white text-second': isDropdownOpen }">
                        <EllipsisVertical />
                    </button>
                </div>

                <!-- Dropdown Menu -->
                <div
                    v-show="isDropdownOpen"
                    class="absolute right-0 mt-2 w-48 bg-white border overflow-auto rounded-md shadow-lg z-50"
                    @click.outside="closeDropdown">
                    <ul class="py-1">
                        <li
                            v-for="(label, key) in dropdownOptions"
                            :key="key"
                            @click="handleDropdownSelection(key)"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer">
                            {{ label }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto space-y-4 mt-10 lg:px-4">
            <Image :personal />

            <PersonalInformation :personal />

            <Biography :personal />

            <JobClassification :personal />

            <Sallary :personal />
        </div>

        <div class="space-y-5 mt-10">
            <p class="font-semibold text-lg lg:text-xl">Curriculum Vitae:</p>
            <iframe
                :src="personal.cv_path"
                width="100%"
                height="600px"
            ></iframe>
        </div>
    </DashboardLayouts>
</template>
<script setup>
// Import komponen dan library yang dibutuhkan
import {
    EllipsisVertical, // Ikon untuk menu dropdown
} from "lucide-vue-next"; // Ikon dari lucide
import { Head, useForm } from "@inertiajs/vue3"; // Inertia.js untuk form handling
import DashboardLayouts from "@/Layouts/DashboardLayouts.vue"; // Layout dashboard
import PersonalInformation from "@/Components/PersonalProfile/PersonalInformation.vue"; // Komponen informasi pribadi
import Biography from "@/Components/PersonalProfile/Biography.vue"; // Komponen biografi
import Image from "@/Components/PersonalProfile/Image.vue"; // Komponen untuk gambar profil
import JobClassification from "@/Components/PersonalProfile/JobClassification.vue"; // Komponen klasifikasi pekerjaan
import Sallary from "@/Components/PersonalProfile/Sallary.vue"; // Komponen informasi gaji
import { ref } from "vue"; // Vue reactivity
import BlockUi from "@/Components/BlockUi.vue"; // Komponen loading block
import Alert from "@/Components/Alert.vue"; // Komponen alert

// Props yang diterima dari server
const props = defineProps({
    personal: {
        type: Object, // Data pribadi dari aplikasi
        required: true, // Harus ada
    },
    errors: {
        type: Object, // Error dari server jika ada
    }
});

// State untuk membuka/menutup dropdown
const isDropdownOpen = ref(false);

// Fungsi untuk toggle (ganti) status dropdown
const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value; // Mengubah status dropdown
};

// Fungsi untuk menutup dropdown
const closeDropdown = () => {
    isDropdownOpen.value = false; // Menutup dropdown
};

// State form yang digunakan untuk mengirim data status
const form = useForm({
    status: "", // Status yang dipilih
});

// Tombol utama yang bisa dipilih untuk memperbarui status
const mainButtons = {
    test: "Test", // Status "Test"
    interview: "Interview", // Status "Interview"
    hired: "Diterima", // Status "Diterima"
    rejected: "Ditolak", // Status "Ditolak"
};

// Pilihan status lain dalam dropdown
const dropdownOptions = {
    under_review: "Sedang Direview", // Status "Sedang Direview"
    document_rejected: "Dokumen Ditolak", // Status "Dokumen Ditolak"
    test_passed: "Tes Lulus", // Status "Tes Lulus"
    test_failed: "Tes Gagal", // Status "Tes Gagal"
    interview_passed: "Interview Lulus", // Status "Interview Lulus"
    interview_failed: "Interview Gagal", // Status "Interview Gagal"
    offered: "Ditawari Posisi", // Status "Ditawari Posisi"
    offer_rejected: "Ditolak Posisi", // Status "Ditolak Posisi"
};

// Fungsi untuk memperbarui status menggunakan tombol utama
const updateStatus = (statusKey) => {
    form.status = statusKey; // Set status pada form

    // Mengirim permintaan untuk memperbarui status
    form.post(route("applications.updateStatus", props.personal.application_id), {
        onError: (errors) => {
            console.error(errors.status || "Gagal memperbarui status!"); // Menampilkan error jika ada
        },
    });
};

// Fungsi untuk memperbarui status dari dropdown
const handleDropdownSelection = (statusKey) => {
    updateStatus(statusKey); // Memperbarui status
    closeDropdown(); // Menutup dropdown setelah pemilihan
};

// Fungsi untuk memformat tanggal menjadi format Indonesia
const formatDate = (date) => {
    if (!date) return '-'; // Jika tanggal kosong, kembalikan '-'
    const options = { year: 'numeric', month: 'long', day: 'numeric' }; // Format tanggal
    return new Date(date).toLocaleDateString('id-ID', options); // Mengembalikan tanggal dalam format Indonesia
};
</script>
<style></style>
