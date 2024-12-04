<script setup>
import Alert from "@/Components/Alert.vue";
import { Cake, MapPinned, Smartphone, Mail } from "lucide-vue-next";
import FormInput from "@/Components/FormInput.vue";
import FormSelect from "@/Components/FormSelect.vue";
import ButtonSubmit from "@/Components/ButtonSubmit.vue";
import Drawer from "@/Components/Drawer.vue";
import { useLocations } from "@/Composables/index.js";
import { computed, onMounted, ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";

const props = defineProps({
    personal: {
        type: Object,
        required: true
    }
})

const page = usePage();
const user = computed(() => page.props.auth.user)

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

<template>
    <Drawer v-if="user?.type === 'personal'" drawer-name="personal-information">
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


                <button type="button" v-if="formPersonalInformation.processing" :disabled="formPersonalInformation.processing" class="cursor-pointer font-semibold btn btn-outline w-32 text-second">
                    Batal
                </button>
                <label v-else  for="personal-information" aria-label="close sidebar"
                       class="drawer-overlay cursor-pointer font-semibold btn btn-outline w-32 text-second">
                    Batal
                </label>

            </div>


        </form>
    </Drawer>

    <div
        class="flex justify-between bg-second md:p-8 p-4 rounded-md text-white flex-col lg:flex-row">
        <div class="space-y-2 break-all">
            <h1 class="text-3xl font-bold break-all">
                {{ personal.full_name }}
            </h1>
            <div class="flex items-center gap-1">
                <Cake/>
                <p class="text-md font-medium">{{ personal.birthDateFormatted }}</p>
            </div>
            <div class="flex items-center gap-1">
                <Smartphone/>
                <p class="text-md font-medium">{{ personal.phone }}</p>
            </div>
            <div v-if="user?.type !== 'personal'" class="flex items-center gap-1">
                <Mail />
                <p class="text-md font-medium">{{ personal.email }}</p>
            </div>
            <div class="flex items-center gap-1">
                <MapPinned/>
                <address class="text-md font-medium">{{ personal.address }}</address>
            </div>
            <address class="text-md font-medium">{{ personal.city_name }}, {{ personal.province_name }}</address>
        </div>

        <div v-if="user?.type === 'personal'" class="drawer-content">
            <!-- Page content here -->
            <label
                for="personal-information"
                class="drawer-button btn btn-sm text-second hover:border-white hover:text-white hover:bg-second px-10 mt-4"
            >Ubah Profil</label
            >
        </div>
    </div>

</template>

<style scoped>

</style>
