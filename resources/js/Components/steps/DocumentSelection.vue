<template>
    <Drawer drawer-name="personal-information">
        <!-- Alert Success -->
        <Alert
            v-if="isSuccess"
            :message="alertMessage"
            type="success"
            class="alert-success"
            :closable="true"
            @close="isSuccess = false"
        />

        <!-- Alert Error -->
        <Alert
            v-if="isError"
            :message="alertMessage"
            type="error"
            class="alert-error"
            :closable="true"
            @close="isError = false"
        />

        <h1 class="text-2xl font-semibold">Informasi Pribadi</h1>

        <form @submit.prevent="submit" class="space-y-6 mt-5">

            <div class="space-y-4">
                <FormInput
                    id="full_name"
                    v-model="formPersonalInformation.full_name"
                    label="Nama Lengkap"
                    placeholder="John Doe"
                    :error="formPersonalInformation.errors.full_name"
                    :disabled="formPersonalInformation.processing"
                    required
                />

                <FormInput
                    type="date"
                    id="birthDate"
                    v-model="formPersonalInformation.birthDate"
                    label="Tanggal Lahir"
                    :error="formPersonalInformation.errors.birthDate"
                    :disabled="formPersonalInformation.processing"
                    required
                />

                <FormInput
                    type="number"
                    id="phone"
                    v-model="formPersonalInformation.phone"
                    label="Nomor Telepon"
                    placeholder="Contoh: 08123456789"
                    :error="formPersonalInformation.errors.phone"
                    :disabled="formPersonalInformation.processing"
                    required
                />

                <FormInput
                    id="address"
                    v-model="formPersonalInformation.address"
                    label="Alamat"
                    help-text="Tidak perlu memasukan provinsi, kota/kabupaten, dan kecamatan"
                    placeholder="Jl. Merdeka 45"
                    :error="formPersonalInformation.errors.address"
                    :disabled="formPersonalInformation.processing"
                    required
                />
            </div>

            <div class="grid md:grid-cols-3 gap-4">
                <FormSelect
                    id="province"
                    v-model="formPersonalInformation.selectedProvince"
                    label="Provinsi"
                    :options="provinces"
                    :error="formPersonalInformation.errors.selectedProvince"
                    @change="handleProvinceChange"
                    :disabled="formPersonalInformation.processing"
                    required
                />

                <FormSelect
                    id="city"
                    v-model="formPersonalInformation.selectedCity"
                    label="Kota/Kabupaten"
                    :options="cities"
                    :error="formPersonalInformation.errors.selectedCity"
                    @change="handleCityChange"
                    :disabled="!formPersonalInformation.selectedProvince || formPersonalInformation.processing"
                    required
                />

                <FormSelect
                    id="district"
                    v-model="formPersonalInformation.selectedDistrict"
                    label="Kecamatan"
                    :options="districts"
                    :error="formPersonalInformation.errors.selectedDistrict"
                    :disabled="!formPersonalInformation.selectedCity || formPersonalInformation.processing"
                    required
                />
            </div>


            <div class="flex flex-row gap-2">
                <ButtonSubmit
                    class=""
                    :is-submitting="isSubmitting"
                    :default-text="'Ubah Profil'"
                    :loading-text="'Sedang Mengubah Profil...'"
                />

                <label for="personal-information" aria-label="close sidebar"
                       class="drawer-overlay cursor-pointer font-semibold btn btn-outline w-32 text-second">
                    Batal
                </label>
            </div>


        </form>
    </Drawer>

    <div class="flex items-start mb-3 mt-12 gap-2 max-w-lg">
        <Info color="#3b82f6"/>
        <p class="text-blue-500 font-semibold">
            Profil kamu adalah bagian dari lamaran kamu. Pastikan profil kamu sudah diperbarui yaa.
        </p>
    </div>

    <div class="bg-second text-white p-5 rounded-lg max-w-xl">
        <div class="space-y-2 xl:space-y-3">
            <h3 class="text-2xl xl:text-3xl font-semibold">{{ personal.full_name }}</h3>

            <div class="flex items-center gap-1">
                <Cake/>
                <p class="text-sm xl:text-lg">{{ personal.birthDateFormatted }}</p>
            </div>

            <div class="flex items-center gap-1">
                <Smartphone/>
                <p class="text-sm xl:text-lg">{{ personal.phone }}</p>
            </div>

            <div class="flex items-center gap-1">
                <MapPinned/>
                <address class="text-sm xl:text-lg">{{ personal.address }}</address>
            </div>

            <address class="text-sm xl:text-lg">{{ personal.city_name }}, {{ personal.province_name }}</address>
        </div>

        <div class="drawer-content">
            <label for="personal-information" class="cursor-pointer font-semibold btn btn-sm btn-outline hover:bg-second hover:border-white bg-white w-32 mt-5 text-second">
                Ubah
            </label>
        </div>
    </div>
</template>

<script setup>
import { Cake, X } from 'lucide-vue-next';
import { MapPinned } from 'lucide-vue-next';
import { Smartphone } from 'lucide-vue-next';
import Drawer from "@/Components/Drawer.vue";
import FormInput from "@/Components/FormInput.vue";
import { useForm } from "@inertiajs/vue3";
import ButtonSubmit from "@/Components/ButtonSubmit.vue";
import { computed, onMounted, ref } from "vue";
import Alert from "@/Components/Alert.vue";
import FormSelect from "@/Components/FormSelect.vue";
import { useLocations } from "@/Composables/index.js";
import { Info } from 'lucide-vue-next';

const props = defineProps({
    personal: {
        type: Object,
        required: true
    }
})

const { provinces, cities, districts, loadProvinces, loadCities, loadDistricts } = useLocations()
const isSubmitting = ref(false);
const alertMessage = ref(null);
const isError = ref(false)
const isSuccess = ref(false)

const formPersonalInformation = useForm({
    full_name: props.personal?.full_name || "",
    phone: props.personal?.phone || "",
    birthDate: props.personal?.birthDate || "",
    address: props.personal?.address || "",
    selectedProvince: props.personal?.province_id || null,
    selectedCity: props.personal?.city_id || null,
    selectedDistrict: props.personal?.district_id || null,
})

const handleProvinceChange = () => {
    if (formPersonalInformation.selectedProvince) {
        loadCities(formPersonalInformation.selectedProvince);
        formPersonalInformation.selectedCity = null;
        formPersonalInformation.selectedDistrict = null;
    }
};

const handleCityChange = () => {
    if (formPersonalInformation.selectedCity) {
        loadDistricts(formPersonalInformation.selectedCity);
        formPersonalInformation.selectedDistrict = null;
    }
};

const submit = () => {
    isSubmitting.value = true;

    formPersonalInformation.put(route("personal.center.update", props.personal.slug), {
        onSuccess: (response) => {
            isSubmitting.value = false;
            isSuccess.value = true;
            alertMessage.value = response.message || "Profil berhasil diperbarui.";
        },
        onError: (errors) => {
            isSubmitting.value = false;
            isError.value = true;

            if (errors.notFound) {
                alertMessage.value = errors.notFound;
            } else if (errors.dbError) {
                alertMessage.value = errors.dbError;
            } else if (errors.generalError) {
                alertMessage.value = errors.generalError;
            } else {
                isError.value = false
            }
        },
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
};

const initializeLocationData = async () => {
    try {
        await loadProvinces();

        if (formPersonalInformation.selectedProvince) {
            await loadCities(formPersonalInformation.selectedProvince);
        }

        if (formPersonalInformation.selectedCity) {
            await loadDistricts(formPersonalInformation.selectedCity);
        }
    } catch (error) {
        console.error("Error initializing location data:", error);
    }
};

onMounted(() => {
    initializeLocationData();
});
</script>
