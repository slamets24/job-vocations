<template>
    <form @submit.prevent="submit" class="flex flex-col gap-2 lg:flex-row">
        <div class="lg:basis-1/2">
            <input
                v-model="form.search"
                type="text"
                placeholder="Masukkan kata kunci"
                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-second focus:ring-second focus:outline-none"
            />
        </div>

        <div class="lg:basis-1/2">
            <select
                v-model="form.classification"
                class="w-full px-4 py-3 rounded-lg border border-gray-300 appearance-none bg-white focus:border-second focus:ring-second focus:outline-none"
            >
                <option value="" disabled>Pilih Klasifikasi</option>
                <option
                    v-for="category in jobClassifications"
                    :key="category.id"
                    :value="category.id">
                    {{ category.name }}
                </option>
            </select>
        </div>

        <div class="relative lg:basis-1/2">
            <input
                v-model="citySearch"
                type="text"
                placeholder="Cukup masukan nama kota"
                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-second focus:ring-second focus:outline-none"
            />
            <ul v-if="locations.length > 0" class="absolute text-sm mt-2 border text-left border-gray-300 bg-white w-full rounded-lg shadow-md">
                <li
                    v-if="isLoading"
                    class="px-4 py-2 cursor-pointer hover:bg-gray-200">
                    Memuat data...
                </li>
                <li
                    v-if="!isLoading"
                    v-for="(location, index) in locations"
                    :key="index"
                    @click="selectLocation(location)"
                    class="px-4 py-2 cursor-pointer hover:bg-gray-200 capitalize"
                >
                    {{location.city_name}}, {{ location.province_name }}
                </li>
            </ul>
        </div>

        <!-- Search Button -->

        <button
            type="submit"
            class="bg-yellow-500 text-white px-6 py-3 rounded-md font-semibold hover:bg-yellow-400 transition duration-300">
                           Cari
        </button>
    </form>
</template>

<script setup>
import { onMounted, ref, watch } from "vue";
import { useDebounce, useJobClassifications } from "@/Composables/index.js";
import { useForm } from "@inertiajs/vue3";

const { jobClassifications, loadJobClassifications } = useJobClassifications();

const isLoading = ref(false);
const citySearch = ref('')
const locations = ref([]);

const form = useForm({
    search: '',
    classification: '',
    location: '',
});

// debounced search
const debouncedSearch = useDebounce(citySearch, 500);

watch(debouncedSearch, () => {
    isLoading.value = true;

    searchLocations();
});

// Fungsi untuk melakukan pencarian berdasarkan keyword
const searchLocations = async () => {
    if (citySearch.value.trim() === '') {
        locations.value = [];
        return;
    }

    try {
        const response = await axios.get(route('locations.cities.byName'), {
            params: { cityName: citySearch.value }
        });

        locations.value = response.data;
    } catch (error) {
        console.error('Error fetching locations:', error);
    } finally {
        isLoading.value = false;
    }
};

// Fungsi untuk memilih lokasi dan menyimpannya ke ref
const selectLocation = (city) => {
    form.location = `${city.city_code}, ${city.province_code}`;
    citySearch.value = `${city.city_name}, ${city.province_name}`;
    locations.value = [];
};

const submit = () => {
    form.get(route("home"));
};

onMounted(() => {
    loadJobClassifications();
})
</script>

<style scoped>

</style>
