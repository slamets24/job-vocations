<template>
    <Head title="Buat Lowongan"/>

    <DashboardLayouts>
        <BlockUi message="Sedang Membuat Lowongan" :is-loading="form.processing" />

        <Alert
            v-if="errors.dbError || errors.generalError"
            :message="errors.dbError || errors.generalError"
            class="mt-5 alert-error"
            type="error"
        />

        <div>
            <h1 class="mt-5 text-2xl font-bold">Buat Lowongan</h1>
            <div class="breadcrumbs text-sm mt-1">
                <ul>
                    <li>
                        <Link :href="route('company.dashboard')">Dashboard</Link>
                    </li>
                    <li>
                        <Link :href="route('jobs.index')">Lowongan</Link>
                    </li>
                    <li>Buat Lowongan</li>
                </ul>
            </div>

            <form @submit.prevent="submit" autocomplete="on" class="space-y-6 mt-5">
                <div class="space-y-4">
                    <FormInput
                        id="title"
                        v-model="form.title"
                        label="Nama Lowongan"
                        placeholder="Fullstack Developer"
                        :error="form.errors.title"
                        required
                        autofocus
                        :disable="form.processing"
                    />
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    <FormSelect
                        id="education"
                        v-model="form.education"
                        label="Pendidikan"
                        :options="educationOptions"
                        :error="form.errors.education"
                        required
                        :disable="form.processing"
                    />

                    <FormSelect
                        id="job_type"
                        v-model="form.job_type"
                        label="Jenis Pekerjaan"
                        :options="jobTypeOptions"
                        :error="form.errors.job_type"
                        required
                        :disable="form.processing"
                    />

                    <FormInput
                        type="number"
                        id="salary"
                        v-model="form.salary"
                        label="Gaji"
                        placeholder="Contoh: 3000000"
                        :error="form.errors.salary"
                        :step="20000"
                        required
                        :disable="form.processing"
                    />
                </div>

                <FormInput
                    id="is_same_location"
                    v-model:checked="form.is_same_location"
                    type="checkbox"
                    @change="(e) => handleLocationChange(e.target.checked)"
                    label="Lokasi penempatan apakah sama dengan letak perusahaan?"
                    :error="form.errors.is_same_location"
                    :disable="form.processing"
                />

                <div class="grid md:grid-cols-3 gap-4">
                    <FormSelect
                        id="province"
                        v-model="form.selectedProvince"
                        label="Provinsi Penempatan"
                        :options="provinces"
                        :error="form.errors.selectedProvince"
                        @change="handleProvinceChange"
                        :disabled="form.is_same_location || form.processing"
                        required
                    />

                    <FormSelect
                        id="city"
                        v-model="form.selectedCity"
                        label="Kota/Kabupaten Penempatan"
                        :options="cities"
                        :error="form.errors.selectedCity"
                        @change="handleCityChange"
                        :disabled="!form.selectedProvince || form.is_same_location || form.processing"
                        required
                    />

                    <FormSelect
                        id="district"
                        v-model="form.selectedDistrict"
                        label="Kecamatan Penempatan"
                        :options="districts"
                        :error="form.errors.selectedDistrict"
                        :disabled="!form.selectedCity || form.is_same_location || form.processing"
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

                <FormTextarea
                    id="description"
                    v-model="form.description"
                    label="Deskripsi"
                    helpText="Silahkan isi deskripsi lowongan, lebih detail maka lebih bagus"
                    :error="form.errors.description"
                    required
                    rows="10"
                    :disable="form.processing"
                />

                <ButtonSubmit
                    class="w-full"
                    :is-submitting="isSubmitting"
                    :default-text="'Buat Lowongan'"
                    :loading-text="'Sedang Membuat Lowongan...'"
                />
            </form>
        </div>
    </DashboardLayouts>
</template>

<script setup>
// Mengimpor komponen dan library yang diperlukan
import { useForm, Head, Link } from "@inertiajs/vue3"; // Inertia.js untuk mengelola form dan routing
import DashboardLayouts from "@/Layouts/DashboardLayouts.vue"; // Layout untuk dashboard
import { ref, computed, onMounted } from "vue"; // Vue.js reactivity
import Alert from "@/Components/Alert.vue"; // Komponen untuk menampilkan alert
import FormInput from "@/Components/FormInput.vue"; // Komponen input form
import FormSelect from "@/Components/FormSelect.vue"; // Komponen select form
import { useJobClassifications, useJobFormOptions, useLocations } from "@/Composables/index.js"; // Custom hooks untuk data form dan lokasi
import FormTextarea from "@/Components/FormTextarea.vue"; // Komponen textarea form
import ButtonSubmit from "@/Components/ButtonSubmit.vue"; // Komponen tombol submit
import BlockUi from "@/Components/BlockUi.vue"; // Komponen untuk menampilkan blok UI saat memuat data

// Props yang diterima, di sini hanya kesalahan (errors) dari server
const props = defineProps({
    errors: {
        type: Object,
    }
});

// Mendapatkan opsi pendidikan dan jenis pekerjaan dari hook kustom
const { educationOptions, jobTypeOptions } = useJobFormOptions();
// Mendapatkan data lokasi dan fungsi untuk memuat provinsi, kota, dan distrik
const { provinces, cities, districts, loadProvinces, loadCities, loadDistricts } = useLocations();
// Mendapatkan data klasifikasi pekerjaan dari hook kustom
const { jobClassifications, loadJobClassifications } = useJobClassifications();

// Form state, inisialisasi form dengan nilai default
const form = useForm({
    title: "", // Judul pekerjaan
    description: "", // Deskripsi pekerjaan
    salary: "", // Gaji pekerjaan
    education: "", // Pendidikan yang dibutuhkan
    job_type: "", // Jenis pekerjaan
    is_same_location: true, // Menentukan apakah lokasi perusahaan sama dengan lokasi pekerjaan
    selectedProvince: null, // Provinsi yang dipilih
    selectedCity: null, // Kota yang dipilih
    selectedDistrict: null, // Distrik yang dipilih
    job_classification: '', // Klasifikasi pekerjaan
});

// Menampilkan tanggal hari ini dalam format yang sesuai untuk input tipe tanggal
const today = computed(() => new Date().toISOString().split("T")[0]);
// Status apakah form sedang disubmit atau tidak
const isSubmitting = ref(false);

// Fungsi untuk menangani perubahan checkbox lokasi
const handleLocationChange = (checked) => {
    form.is_same_location = checked;

    // Jika lokasi sama, reset lokasi yang dipilih
    if (checked) {
        form.selectedProvince = null;
        form.selectedCity = null;
        form.selectedDistrict = null;
    }
}

// Fungsi untuk menangani perubahan provinsi
const handleProvinceChange = () => {
    if (form.selectedProvince) {
        loadCities(form.selectedProvince); // Memuat kota berdasarkan provinsi
        form.selectedCity = null;
        form.selectedDistrict = null;
    }
};

// Fungsi untuk menangani perubahan kota
const handleCityChange = () => {
    if (form.selectedCity) {
        loadDistricts(form.selectedCity); // Memuat distrik berdasarkan kota
        form.selectedDistrict = null;
    }
};

// Fungsi untuk mengirimkan data form
const submit = () => {
    isSubmitting.value = true; // Set status submitting
    form.post(route("jobs.store"), { // Mengirim data form ke server
        onSuccess: () => {
            isSubmitting.value = false;
            form.reset(); // Reset form jika berhasil
        },
        onError: (errors) => {
            isSubmitting.value = false;
        },
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
};

// Mengambil data provinsi dan klasifikasi pekerjaan saat komponen dimuat
onMounted(() => {
    loadProvinces(); // Memuat provinsi
    loadJobClassifications(); // Memuat klasifikasi pekerjaan
});
</script>

<style scoped></style>
