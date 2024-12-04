import { ref } from "vue";

export function useJobClassifications() {
    const jobClassifications = ref([]);
    const loadJobClassifications = async () => {
        try {
            const response = await axios.get(route('jobs.classifications'));
            jobClassifications.value = response.data;
        } catch (error) {
            console.error("Error loading job classifications:", error);
        }
    };

    return {
        jobClassifications,
        loadJobClassifications,
    };
}
