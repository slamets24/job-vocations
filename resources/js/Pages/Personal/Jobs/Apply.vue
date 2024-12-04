<template>
    <Head title="Pilih Dokumen"/>
    <DashboardLayouts>
        <BlockUi message="Sedang Melamar" :is-loading="form.processing" />

        <Alert
            v-if="errors.dbError || errors.generalError"
            :message="errors.dbError || errors.generalError"
            class="mt-5 alert-error"
            type="error"
        />

        <!-- Drawer untuk detail pekerjaan -->
        <Drawer drawer-name="detail-job">
            <h1 class="text-2xl xl:text-3xl font-semibold">{{ job.title }}</h1>
            <p class="text-lg xl:text-xl">{{ job.company_name }}</p>
            <p class="text-md xl:text-base text-second"> Diunggah pada {{ job.created_at }}</p>
            <div class="space-y-2 overflow-hidden">
                <h2 class="text-lg xl:text-xl font-semibold">Deskripsi Pekerjaan:</h2>
                <p class="break-all text-pretty xl:text-md">{{ job.description }}</p>
            </div>
        </Drawer>

        <!-- Header Info -->
        <div class="flex flex-col md:flex-row items-start lg:items-center gap-6 mt-10">
            <div class="avatar">
                <div class="size-20 md:size-32 rounded-md">
                    <img
                        :src="job.image"
                        :alt="job.company_name"/>
                </div>
            </div>
            <div class="space-y-2">
                <p>Melamar Untuk</p>
                <div class="space-y-2">
                    <h2 class="text-2xl font-semibold">{{ job.title }}</h2>
                    <p>{{ job.company_name }}</p>
                </div>
                <div class="drawer-content">
                    <label for="detail-job" class="underline cursor-pointer font-semibold">
                        Lihat Detail Pekerjaan
                    </label>
                </div>
            </div>
        </div>

        <DocumentSelection :personal/>

        <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-6 mt-10" autocomplete="on">
            <FormInputFile
                id="resume"
                label="Upload Resume"
                accept=".pdf"
                help-text="Pastikan file yang diupload adalah pdf dan jangan lebih dari 2 MB"
                v-model="form.resume"
                :error="form.errors.resume"
                :disabled="form.processing"
                required/>

            <div class="mt-8 max-w-xl">
                <p>Jaga diri Anda. Jangan sertakan informasi sensitif dalam dokumen Anda.</p>
                <p>Hanya bagikan informasi yang diperlukan. Jangan sertakan konten seperti dokumen identitas (misalnya paspor,
                    SIM), informasi keuangan (misalnya NPWP), ras, agama, atau informasi kesehatan.</p>
            </div>

            <ButtonSubmit
                :is-submitting="isSubmitting"
                :default-text="'Lamar Sekarang'"
                :loading-text="'Sedang proses...'"
            />
        </form>

    </DashboardLayouts>
</template>

<script setup>
// Mengimpor hook dan komponen yang diperlukan
import { ref } from 'vue'; // Hook untuk mendeklarasikan reactive state
import { useForm, Head } from '@inertiajs/vue3'; // Inertia.js untuk menangani form dan navigasi
import DashboardLayouts from "@/Layouts/DashboardLayouts.vue"; // Layout untuk halaman dashboard
import DocumentSelection from "@/Components/steps/DocumentSelection.vue"; // Komponen untuk memilih dokumen
import Drawer from "@/Components/Drawer.vue"; // Komponen drawer untuk navigasi atau konten samping
import FormInputFile from "@/Components/FormInputFile.vue"; // Komponen untuk input file
import ButtonSubmit from "@/Components/ButtonSubmit.vue"; // Komponen tombol untuk submit form
import BlockUi from "@/Components/BlockUi.vue"; // Komponen untuk blokir UI saat sedang loading
import Alert from "@/Components/Alert.vue"; // Komponen untuk menampilkan alert atau pesan kesalahan

// Mendefinisikan props yang diterima oleh komponen ini
const props = defineProps({
    job: { type: Object, required: true }, // Job yang sedang dilamar, harus diberikan
    personal: {
        type: Object,
        default: () => ({}), // Data pribadi jika ada
    },
    errors: { type: Object }, // Error yang muncul selama proses pengiriman form
});

// State untuk menandai apakah form sedang disubmit
const isSubmitting = ref(false);

// Mendefinisikan form yang akan dikirimkan
const form = useForm({
    resume: null, // File resume yang akan diunggah
});

// Fungsi untuk mengirimkan form
const submit = () => {
    isSubmitting.value = true; // Menandakan bahwa form sedang disubmit

    form.post(route("applications.store", props.job.slug), { // Mengirimkan form ke route tertentu dengan slug pekerjaan
        onSuccess: () => {
            isSubmitting.value = false; // Mengubah status setelah berhasil
            form.reset(); // Mereset form setelah sukses
        },
        onError: (errors) => {
            isSubmitting.value = false; // Mengubah status jika terjadi kesalahan
        },
        onFinish: () => {
            isSubmitting.value = false; // Mengubah status setelah proses selesai
        },
    });
};

</script>
