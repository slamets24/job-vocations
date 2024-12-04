<template lang="">
    <Head title="Lowongan Kerja " />
    <DashboardLayouts>
    <div  class="mt-10" v-if="userType === 'personal'">
        <form class="flex flex-col w-full gap-4 md:flex-row">
                <input type="search" id="search"  class="w-full input input-bordered" placeholder="Search" required>
                <select type="search" id="search"  class="w-full input input-bordered" placeholder="Masukan Pekerjaan yang diinginkan" required>
                    <option value="Pekerjaan 1">Pekerjaan 1</option>
                    <option value="Pekerjaan 2">Pekerjaan 2</option>
                </select>
                <input type="search" id="search" class="w-full input input-bordered" placeholder="Masukan Kota yang diinginkan" required>
            <div>
            <button type="submit" class="w-full px-4 text-sm text-white btn bg-second"> Cari </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 mt-10" v-if="userType === 'personal'">
    <CardJob v-for="job in jobs"
        :key="job.id"
        :job="job"
        :company="{
            name: job.company_name,
            slug: job.company_slug,
            image_profile: job.image_profile
        }" />

    </div>
    <div
        class="mt-10 text-center lg:text-start">
        <Pagination />
    </div>

    </DashboardLayouts>
</template>
<script setup>
import Pagination from "@/Components/Pagination.vue";
import CardJob from "@/Components/Card/CardJob.vue";
import DashboardLayouts from "@/Layouts/DashboardLayouts.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";
const { props: pageProps } = usePage();
const userType = pageProps.auth.user.type;

const props = defineProps({
    jobs: {
        type: Object,
        required: true,
    },
    company: {
        type: Object,
        required: false,
        default: () => ({
            name: "Unknown Company",
            slug: "",
            image_profile: "https://via.placeholder.com/150",
        }),
    },
});

</script>
<style lang="">
</style>
