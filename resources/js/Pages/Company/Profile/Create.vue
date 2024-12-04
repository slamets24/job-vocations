<template>
    <Head title="Buat Profil"/>

    <DashboardLayouts>
        <BlockUi message="Sedang Membuat Profil" :is-loading="form.processing" />

        <Alert
            v-if="errors.dbError || errors.generalError"
            :message="errors.dbError || errors.generalError"
            class="mt-5 alert-error"
            type="error"
        />

        <div>
            <h1 class="mt-5 text-2xl font-bold">Buat Profil</h1>
            <div class="breadcrumbs text-sm mt-1">
                <ul>
                    <li>
                        <Link :href="route('company.dashboard')">Dashboard</Link>
                    </li>
                    <li>
                        <Link :href="route('company.profile.index')">Profil Perusahaan</Link>
                    </li>
                    <li>Buat Profil</li>
                </ul>
            </div>

            <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-6 mt-5" autocomplete="on">
                <div class="space-y-4">
                    <FormInputFile
                        id="imageProfile"
                        label="Upload Profil"
                        accept="image/*"
                        help-text="Pastikan file yang diupload adalah gambar"
                        v-model="form.imageProfile"
                        :error="form.errors.imageProfile"
                        required
                        :disable="form.processing"
                    />

                    <FormInput
                        id="companyName"
                        v-model="form.companyName"
                        label="Nama Perusahaan"
                        placeholder="PT. XYZ"
                        :error="form.errors.companyName"
                        required
                        :disable="form.processing"
                    />

                    <FormInput
                        id="companyAddress"
                        v-model="form.companyAddress"
                        label="Alamat Perusahaan"
                        help-text="Tidak perlu memasukan provinsi, kota/kabupaten, dan kecamatan"
                        placeholder="Jl. Merdeka 45"
                        :error="form.errors.companyAddress"
                        required
                        :disable="form.processing"
                    />

                    <FormInput
                        id="media"
                        type="url"
                        v-model="form.media"
                        label="Sosial Media"
                        placeholder="https://www.linkedin.com/nama-perusahaan"
                        help-text="Silahkan masukkan link sosial media perusahaan seperti Linkedin, instagram, dll. Masukkan satu link saja."
                        :error="form.errors.media"
                        :disable="form.processing"
                    />
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    <FormSelect
                        id="province"
                        v-model="form.selectedProvince"
                        label="Provinsi"
                        :options="provinces"
                        :error="form.errors.selectedProvince"
                        @change="handleProvinceChange"
                        required
                        :disable="form.processing"
                    />

                    <FormSelect
                        id="city"
                        v-model="form.selectedCity"
                        label="Kota/Kabupaten"
                        :options="cities"
                        :error="form.errors.selectedCity"
                        @change="handleCityChange"
                        :disabled="!form.selectedProvince || form.processing"
                        required
                    />

                    <FormSelect
                        id="district"
                        v-model="form.selectedDistrict"
                        label="Kecamatan"
                        :options="districts"
                        :error="form.errors.selectedDistrict"
                        :disabled="!form.selectedCity || form.processing"
                        required
                    />
                </div>

                <FormTextarea
                    id="description"
                    v-model="form.description"
                    label="Deskripsi"
                    helpText="Semakin lengkap, semakin bagus"
                    :error="form.errors.description"
                    placeholder="Jelaskan profil perusahaan dan informasi tambahan"
                    required
                    rows="6"
                    :disable="form.processing"
                />

                <ButtonSubmit
                    class="w-full"
                    :is-submitting="isSubmitting"
                    :default-text="'Buat Profil'"
                    :loading-text="'Sedang Membuat Profil...'"
                />
            </form>
        </div>
    </DashboardLayouts>
</template>

<script setup>
// Mengimpor hook Vue dan komponen-komponen yang diperlukan
import { onMounted, ref } from "vue"; // Menggunakan onMounted dan ref dari Vue untuk hook dan reactive data
import { Head, Link, useForm } from "@inertiajs/vue3"; // Menggunakan Inertia.js untuk manajemen halaman dan form
import DashboardLayouts from "@/Layouts/DashboardLayouts.vue"; // Layout untuk halaman dashboard
import Alert from "@/Components/Alert.vue"; // Komponen untuk menampilkan alert/error
import FormInput from "@/Components/FormInput.vue"; // Komponen input form
import { useLocations } from "@/Composables/index.js"; // Custom composable untuk mengelola lokasi
import FormSelect from "@/Components/FormSelect.vue"; // Komponen select untuk memilih data dari dropdown
import FormTextarea from "@/Components/FormTextarea.vue"; // Komponen textarea untuk deskripsi
import ButtonSubmit from "@/Components/ButtonSubmit.vue"; // Komponen tombol submit
import FormInputFile from "@/Components/FormInputFile.vue"; // Komponen input file untuk gambar profil
import BlockUi from "@/Components/BlockUi.vue"; // Komponen Block UI (untuk menampilkan loading atau blok UI)

// Menentukan props yang diterima oleh komponen ini
const props = defineProps({
    errors: {
        type: Object, // Digunakan untuk menampilkan error jika ada
    }
});

// Menggunakan composable untuk lokasi (provinces, cities, districts) dan fungsi terkait
const { provinces, cities, districts, loadProvinces, loadCities, loadDistricts } = useLocations();

// State untuk status pengiriman form (untuk menunjukkan loading state)
const isSubmitting = ref(false);

// Inisialisasi form dengan nilai default untuk data perusahaan
const form = useForm({
    imageProfile: null, // Gambar profil perusahaan
    companyName: "", // Nama perusahaan
    companyAddress: "", // Alamat perusahaan
    media: "", // Media perusahaan
    selectedProvince: null, // Provinsi yang dipilih
    selectedCity: null, // Kota yang dipilih
    selectedDistrict: null, // Kecamatan yang dipilih
    description: "", // Deskripsi perusahaan
});

// Fungsi untuk menangani perubahan provinsi
const handleProvinceChange = () => {
    if (form.selectedProvince) {
        loadCities(form.selectedProvince); // Memuat kota berdasarkan provinsi yang dipilih
        form.selectedCity = null; // Reset kota
        form.selectedDistrict = null; // Reset kecamatan
    }
};

// Fungsi untuk menangani perubahan kota
const handleCityChange = () => {
    if (form.selectedCity) {
        loadDistricts(form.selectedCity); // Memuat kecamatan berdasarkan kota yang dipilih
        form.selectedDistrict = null; // Reset kecamatan
    }
};

// Fungsi untuk mengirimkan form
const submit = () => {
    isSubmitting.value = true; // Menandai form sedang dikirim
    form.post(route("company.profile.store"), { // Mengirimkan data form ke backend
        onSuccess: () => {
            isSubmitting.value = false; // Menandai form selesai dikirim
            form.reset(); // Reset form setelah berhasil
        },
        onError: (errors) => {
            isSubmitting.value = false; // Menandai form selesai dikirim meskipun ada error
        },
        onFinish: () => {
            isSubmitting.value = false; // Menandai form selesai dikirim
        },
    });
};

// Hook untuk memuat data provinsi saat komponen dimuat
onMounted(() => {
    loadProvinces(); // Memuat provinsi saat komponen pertama kali dimuat
});
</script>
