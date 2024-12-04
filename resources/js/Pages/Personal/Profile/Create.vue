<template>
    <Head title="Profile"/>

    <DashboardLayouts>
        <BlockUi message="Sedang Membuat Profil" :is-loading="form.processing"/>

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
                        <Link :href="route('home')">Cari Lowongan</Link>
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
                        id="full_name"
                        v-model="form.full_name"
                        label="Nama Lengkap"
                        placeholder="John Doe"
                        :error="form.errors.full_name"
                        required
                        :disable="form.processing"
                    />

                    <FormInput
                        type="date"
                        id="birthDate"
                        v-model="form.birthDate"
                        label="Tanggal Lahir"
                        :error="form.errors.birthDate"
                        required
                        :disable="form.processing"
                    />

                    <FormInput
                        type="number"
                        id="phone"
                        v-model="form.phone"
                        label="Nomor Telepon"
                        placeholder="Contoh: 08123456789"
                        :error="form.errors.phone"
                        required
                        :disable="form.processing"
                    />

                    <FormInput
                        id="address"
                        v-model="form.address"
                        label="Alamat"
                        help-text="Tidak perlu memasukan provinsi, kota/kabupaten, dan kecamatan"
                        placeholder="Jl. Merdeka 45"
                        :error="form.errors.address"
                        required
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

                <FormSelect
                    id="job_classification"
                    v-model="form.job_classification"
                    label="Klasifikasi Bidang Pekerjaan"
                    :options="jobClassifications"
                    :error="form.errors.job_classification"
                    required
                    :disable="form.processing"
                />

                <FormInput
                    type="number"
                    id="salary"
                    v-model="form.salary"
                    label="Ekspektasi Gaji"
                    placeholder="Contoh: 3000000"
                    :error="form.errors.salary"
                    :step="20000"
                    required
                    :disable="form.processing"
                />

                <FormTextarea
                    id="biography"
                    v-model="form.biography"
                    label="Ringkasan Pribadi"
                    :error="form.errors.biography"
                    placeholder="Jelaskan tentang diri Anda ..."
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
// Mengimpor komponen dan hooks yang diperlukan
import { Head, Link } from "@inertiajs/vue3"; // Mengimpor Head dan Link dari Inertia.js untuk navigasi
import { useForm } from '@inertiajs/vue3'; // Menggunakan hook useForm untuk mempermudah pengelolaan form
import { ref, onMounted } from 'vue'; // Mengimpor reactive state dan lifecycle hooks dari Vue
import DashboardLayouts from "@/Layouts/DashboardLayouts.vue"; // Layout halaman dashboard
import FormInputFile from "@/Components/FormInputFile.vue"; // Komponen untuk input file
import Alert from "@/Components/Alert.vue"; // Komponen untuk menampilkan alert
import FormInput from "@/Components/FormInput.vue"; // Komponen untuk input teks
import FormSelect from "@/Components/FormSelect.vue"; // Komponen untuk dropdown select
import { useLocations, useJobClassifications } from "@/Composables/index.js"; // Hook untuk lokasi dan klasifikasi pekerjaan
import FormTextarea from "@/Components/FormTextarea.vue"; // Komponen untuk input teks area
import ButtonSubmit from "@/Components/ButtonSubmit.vue"; // Komponen untuk tombol submit
import BlockUi from "@/Components/BlockUi.vue"; // Komponen untuk blok UI saat loading

// Mendefinisikan props untuk komponen ini
const props = defineProps({
    errors: {
        type: Object, // Menyimpan objek error untuk ditampilkan pada form
    }
})

// Menggunakan hook untuk memuat data lokasi dan klasifikasi pekerjaan
const { provinces, cities, districts, loadProvinces, loadCities, loadDistricts } = useLocations();
const { jobClassifications, loadJobClassifications } = useJobClassifications();

// Menyiapkan state untuk menandakan status form sedang diproses
const isSubmitting = ref(false);

// Menyiapkan objek form menggunakan useForm dari Inertia.js
const form = useForm({
    imageProfile: null, // Input untuk gambar profil
    full_name: '', // Nama lengkap
    phone: '', // Nomor telepon
    biography: '', // Biografi
    birthDate: '', // Tanggal lahir
    address: '', // Alamat
    selectedProvince: null, // Provinsi yang dipilih
    selectedCity: null, // Kota yang dipilih
    selectedDistrict: null, // Kecamatan yang dipilih
    job_classification: '', // Klasifikasi pekerjaan
    salary: '', // Gaji
});

// Menangani perubahan provinsi untuk memuat kota-kota yang relevan
const handleProvinceChange = () => {
    if (form.selectedProvince) {
        loadCities(form.selectedProvince); // Memuat kota berdasarkan provinsi yang dipilih
        form.selectedCity = null; // Reset kota yang sudah dipilih
        form.selectedDistrict = null; // Reset kecamatan
    }
}

// Menangani perubahan kota untuk memuat kecamatan yang relevan
const handleCityChange = () => {
    if (form.selectedCity) {
        loadDistricts(form.selectedCity); // Memuat kecamatan berdasarkan kota yang dipilih
        form.selectedDistrict = null; // Reset kecamatan yang sudah dipilih
    }
};

// Fungsi untuk mengirimkan form ke server
const submit = () => {
    isSubmitting.value = true; // Set status submitting menjadi true
    form.post(route("personal.store"), { // Mengirimkan form ke route 'personal.store'
        onSuccess: () => {
            isSubmitting.value = false; // Reset status saat sukses
            form.reset(); // Reset form setelah berhasil disubmit
        },
        onError: (errors) => {
            isSubmitting.value = false; // Reset status saat error
        },
        onFinish: () => {
            isSubmitting.value = false; // Reset status setelah selesai
        },
    });
};

// Menggunakan lifecycle hook onMounted untuk memuat data provinsi dan klasifikasi pekerjaan
onMounted(() => {
    loadProvinces(); // Memuat provinsi saat komponen pertama kali dimuat
    loadJobClassifications(); // Memuat klasifikasi pekerjaan
});
</script>
<style lang=""></style>
