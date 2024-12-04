<template>

    <Head title="Edit Lowongan"/>
    <DashboardLayouts>
        <BlockUi message="Sedang Mengubah Lowongan" :is-loading="form.processing"/>

        <Alert
            v-if="errors.dbError || errors.generalError || errors.notFound"
            :message="errors.dbError || errors.generalError || errors.notFound"
            class="mt-5 alert-error"
            type="error"
        />

        <div class="">
            <h1 class="mt-5 text-2xl font-bold">Ubah Lowongan</h1>
            <div class="breadcrumbs text-sm mt-1">
                <ul>
                    <li>
                        <Link :href="route('company.dashboard')">Dashboard</Link>
                    </li>
                    <li>
                        <Link :href="route('jobs.index')">Lowongan</Link>
                    </li>
                    <li>Ubah Lowongan</li>
                </ul>
            </div>

            <form @submit.prevent="submit" autocomplete="on" class="space-y-6 mt-5">
                <div class="space-y-4">
                    <FormInput
                        id="is_closed"
                        v-model:checked="form.is_closed"
                        @change="(e) => handleClosedChange(e.target.checked)"
                        type="checkbox"
                        help-text="Silahkan ceklis jika lowongan sudah ditutup"
                        label="Apakah iklan lowongan ini sudah ditutup?"
                        :error="form.errors.is_closed"
                        :disable="form.processing"
                    />

                    <FormInput
                        id="title"
                        v-model="form.title"
                        label="Nama Lowongan"
                        placeholder="Fullstack Developer"
                        :error="form.errors.title"
                        required
                        autofocus
                        :disabled="form.processing"
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
                        label="Kecamatan"
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
                    rows="7"
                    required
                    :disable="form.processing"
                />

                <ButtonSubmit
                    class="w-full"
                    :is-submitting="isSubmitting"
                    :default-text="'Ubah Lowongan'"
                    :loading-text="'Sedang Mengubah Lowongan...'"
                />
            </form>
        </div>
    </DashboardLayouts>
</template>
<script setup>
// Mengimpor komponen dan library yang diperlukan
import { ref, onMounted, computed } from "vue"; // Vue reactivity API
import { Head, useForm, Link } from "@inertiajs/vue3"; // Inertia.js untuk form dan routing
import DashboardLayouts from "@/Layouts/DashboardLayouts.vue"; // Layout untuk dashboard
import FormInput from "@/Components/FormInput.vue"; // Komponen input form
import FormSelect from "@/Components/FormSelect.vue"; // Komponen select form
import { useJobClassifications, useJobFormOptions, useLocations } from "@/Composables/index.js"; // Hook kustom untuk opsi pekerjaan, lokasi, dan klasifikasi pekerjaan
import FormTextarea from "@/Components/FormTextarea.vue"; // Komponen textarea form
import ButtonSubmit from "@/Components/ButtonSubmit.vue"; // Komponen tombol submit
import Alert from "@/Components/Alert.vue"; // Komponen alert
import BlockUi from "@/Components/BlockUi.vue"; // Komponen UI blok saat memuat data

// Props yang diterima: pekerjaan yang akan diupdate dan errors dari server
const props = defineProps({
    job: {
        type: Object,
        required: true, // Pekerjaan yang akan diupdate
    },
    errors: {
        type: Object, // Pesan error yang akan ditampilkan
    }
});

// State untuk mengelola status submit form
const isSubmitting = ref(false);

// Mengambil tanggal hari ini dalam format yang sesuai
const today = computed(() => new Date().toISOString().split("T")[0]);

// Opsi untuk pendidikan dan jenis pekerjaan
const { educationOptions, jobTypeOptions } = useJobFormOptions();

// Data lokasi: provinsi, kota, distrik dan fungsi untuk memuatnya
const { provinces, cities, districts, loadProvinces, loadCities, loadDistricts } = useLocations();

// Data klasifikasi pekerjaan
const { jobClassifications, loadJobClassifications } = useJobClassifications();

// Form state: Inisialisasi form dengan data pekerjaan yang sudah ada
const form = useForm({
    title: props.job?.title || "",
    salary: props.job?.salary || "",
    education: props.job?.education || "",
    job_type: props.job?.job_type || "",
    is_same_location: false,
    is_closed: props.job?.status != 1, // Menandakan pekerjaan telah ditutup jika status tidak sama dengan 1
    description: props.job?.description || "",
    selectedProvince: props.job?.province_id || null,
    selectedCity: props.job?.city_id || null,
    selectedDistrict: props.job?.district_id || null,
    job_classification: props.job?.job_classification_id || null,
});

// Fungsi untuk menangani perubahan lokasi pekerjaan
const handleLocationChange = (checked) => {
    form.is_same_location = checked; // Menandakan apakah lokasi pekerjaan sama dengan lokasi perusahaan

    if (checked) {
        // Jika lokasi sama, reset provinsi, kota, dan distrik yang dipilih
        form.selectedProvince = null;
        form.selectedCity = null;
        form.selectedDistrict = null;
    }
}

// Fungsi untuk menangani perubahan status pekerjaan
const handleClosedChange = (checked) => {
    form.is_closed = checked;
}

// Fungsi untuk menangani perubahan provinsi
const handleProvinceChange = () => {
    if (form.selectedProvince) {
        loadCities(form.selectedProvince); // Memuat kota berdasarkan provinsi yang dipilih
        form.selectedCity = null;
        form.selectedDistrict = null;
    }
};

// Fungsi untuk menangani perubahan kota
const handleCityChange = () => {
    if (form.selectedCity) {
        loadDistricts(form.selectedCity); // Memuat distrik berdasarkan kota yang dipilih
        form.selectedDistrict = null;
    }
};

// Fungsi untuk mengirimkan data form
const submit = () => {
    isSubmitting.value = true; // Set status submitting menjadi true
    form.put(route("jobs.update", props.job.id), { // Mengirim data form untuk memperbarui pekerjaan
        onSuccess: () => {
            isSubmitting.value = false; // Set status submitting menjadi false
            form.reset(); // Reset form setelah sukses
        },
        onError: (errors) => {
            isSubmitting.value = false; // Set status submitting menjadi false
        },
        onFinish: () => {
            isSubmitting.value = false; // Set status submitting menjadi false
        },
    });
};

// Fungsi untuk menginisialisasi data lokasi (provinsi, kota, distrik)
const initializeLocationData = async () => {
    try {
        await loadProvinces(); // Memuat provinsi
        if (form.selectedProvince) {
            await loadCities(form.selectedProvince); // Memuat kota berdasarkan provinsi yang dipilih
        }

        if (form.selectedCity) {
            await loadDistricts(form.selectedCity); // Memuat distrik berdasarkan kota yang dipilih
        }
    } catch (error) {
        console.error("Error initializing location data:", error); // Menampilkan error jika ada masalah
    }
};

// Menjalankan fungsi initializeLocationData saat komponen dimuat
onMounted(() => {
    initializeLocationData();
    loadJobClassifications(); // Memuat klasifikasi pekerjaan
});
</script>
<style lang="">
</style>
