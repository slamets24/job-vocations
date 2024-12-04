<template>
    <Head title="Edit Profil" />

    <DashboardLayouts>
        <BlockUi message="Sedang Mengubah Profil" :is-loading="form.processing" />

        <Alert
            v-if="errors.dbError || errors.generalError"
            :message="errors.dbError || errors.generalError"
            class="mt-5 alert-error"
            type="error"
        />

        <div>
            <h1 class="mt-5 text-2xl font-bold">Ubah Profil</h1>
            <div class="breadcrumbs text-sm mt-1">
                <ul>
                    <li>
                        <Link :href="route('company.dashboard')">Dashboard</Link>
                    </li>
                    <li>
                        <Link :href="route('company.profile.index')">Profil Perusahaan</Link>
                    </li>
                    <li>Ubah Profil</li>
                </ul>
            </div>

            <form @submit.prevent="submit" class="space-y-6 mt-5">
                <div class="space-y-4">
                    <div class="avatar">
                        <div class=" ring-offset-base-100 w-24 rounded-full ring ring-second">
                            <img alt="Photo Profile" :src="company.image_profile" />
                        </div>
                    </div>

                    <FormInputFile
                        id="imageProfile"
                        label="Upload Profil"
                        accept="image/*"
                        help-text="Pastikan file yang diupload adalah gambar"
                        v-model="form.imageProfile"
                        :error="form.errors.imageProfile"
                        @input="form.setData('imageProfile', $event)"
                        :disable="form.processing"
                    />

                    <FormInput
                        id="companyName"
                        v-model="form.companyName"
                        label="Nama Perusahaan"
                        placeholder="PT. XYZ"
                        :error="form.errors.companyName"
                        :disable="form.processing"
                        required
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
                    :default-text="'Ubah Profil'"
                    :loading-text="'Sedang Mengubah Profil...'"
                />
            </form>
        </div>
    </DashboardLayouts>
</template>

<script setup>
// Mengimpor hook dan komponen yang diperlukan
import { ref, reactive, onMounted } from "vue"; // Reactive state management dan lifecycle hooks
import { Head, Link, router, useForm } from "@inertiajs/vue3"; // Inertia.js untuk navigasi dan form handling
import DashboardLayouts from "@/Layouts/DashboardLayouts.vue"; // Layout untuk halaman dashboard
import Alert from "@/Components/Alert.vue"; // Komponen alert untuk pesan kesalahan atau sukses
import { useLocations } from "@/Composables/index.js"; // Fungsi untuk memuat lokasi seperti provinsi, kota, dan distrik
import FormInput from "@/Components/FormInput.vue"; // Komponen input teks
import FormSelect from "@/Components/FormSelect.vue"; // Komponen select dropdown
import FormTextarea from "@/Components/FormTextarea.vue"; // Komponen textarea
import ButtonSubmit from "@/Components/ButtonSubmit.vue"; // Tombol untuk submit form
import FormInputFile from "@/Components/FormInputFile.vue"; // Komponen untuk mengunggah file
import BlockUi from "@/Components/BlockUi.vue"; // Komponen UI untuk blokir saat proses pengiriman data

// Mendefinisikan props yang diterima oleh komponen ini
const props = defineProps({
    company: {
        type: Object,
        required: true, // Perusahaan yang sedang diedit harus diberikan
    },
    errors: {
        type: Object, // Kesalahan yang mungkin terjadi saat pengiriman form
    }
});

// Mengambil fungsi lokasi dari Composables untuk memuat provinsi, kota, dan distrik
const { provinces, cities, districts, loadProvinces, loadCities, loadDistricts } = useLocations();

// State untuk menandai apakah form sedang disubmit
const isSubmitting = ref(false);

// Mendefinisikan form dengan data perusahaan yang ada saat ini
const form = useForm({
    _method: 'PUT', // Metode HTTP untuk pembaruan
    imageProfile: null, // Gambar profil perusahaan
    companyName: props.company?.company_name || "", // Nama perusahaan
    companyAddress: props.company?.company_address || "", // Alamat perusahaan
    media: props.company?.social_media || "", // Media sosial perusahaan
    selectedProvince: props.company?.province_id || null, // Provinsi yang dipilih
    selectedCity: props.company?.city_id || null, // Kota yang dipilih
    selectedDistrict: props.company?.district_id || null, // Distrik yang dipilih
    description: props.company?.description || "", // Deskripsi perusahaan
}, {
    forceFormData: true // Memastikan data form dikirim sebagai FormData (termasuk file)
});

// Fungsi untuk menangani perubahan provinsi
const handleProvinceChange = () => {
    if (form.selectedProvince) {
        loadCities(form.selectedProvince); // Memuat kota berdasarkan provinsi
        form.selectedCity = null; // Mengatur ulang kota dan distrik
        form.selectedDistrict = null;
    }
};

// Fungsi untuk menangani perubahan kota
const handleCityChange = () => {
    if (form.selectedCity) {
        loadDistricts(form.selectedCity); // Memuat distrik berdasarkan kota
        form.selectedDistrict = null; // Mengatur ulang distrik
    }
};

// Fungsi untuk mengirimkan form
const submit = () => {
    isSubmitting.value = true; // Menandai bahwa form sedang disubmit

    form.post(route("company.profile.update", props.company.id), {
        _method: 'PUT', // Menggunakan metode PUT untuk pembaruan
        onSuccess: () => {
            isSubmitting.value = false; // Mengubah status setelah berhasil
            form.reset(); // Mereset form setelah sukses
        },
        onError: (errors) => {
            isSubmitting.value = false; // Mengubah status jika terjadi kesalahan
        },
        onFinish: () => {
            isSubmitting.value = false; // Mengubah status setelah selesai
        },
        forceFormData: true // Mengirim data sebagai FormData
    });
};

// Fungsi untuk memulai data lokasi seperti provinsi, kota, dan distrik
const initializeLocationData = async () => {
    try {
        await loadProvinces(); // Memuat provinsi

        if (form.selectedProvince) {
            await loadCities(form.selectedProvince); // Memuat kota berdasarkan provinsi
        }

        if (form.selectedCity) {
            await loadDistricts(form.selectedCity); // Memuat distrik berdasarkan kota
        }
    } catch (error) {
        console.error("Error initializing location data:", error); // Menangani error jika data gagal dimuat
    }
};

// Menjalankan fungsi initializeLocationData setelah komponen dimuat
onMounted(() => {
    initializeLocationData();
});
</script>
