import { ref } from "vue";

export function useLocations(initialProvinces) {
    const provinces = ref([]);
    const cities = ref([]);
    const districts = ref([]);

    const loadProvinces = async () => {
        try {
            const response = await axios.get(route('locations.provinces'));
            provinces.value = response.data;
        } catch (error) {
            console.error("Error loading provinces:", error);
        }
    };

    const loadCities = async (provinceId) => {
        try {
            const response = await axios.get(route('locations.cities', provinceId));
            cities.value = response.data;
            districts.value = [];
        } catch (error) {
            console.error("Error loading cities:", error);
        }
    };

    const loadDistricts = async (cityId) => {
        try {
            const response = await axios.get(route('locations.districts', cityId));
            districts.value = response.data;
        } catch (error) {
            console.error("Error loading districts:", error);
        }
    };

    return {
        provinces,
        cities,
        districts,
        loadProvinces,
        loadCities,
        loadDistricts
    };
}
