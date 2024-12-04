<script setup>
import { Head } from "@inertiajs/vue3";
import DashboardLayouts from "@/Layouts/DashboardLayouts.vue";

const props = defineProps({
    personal: {
        type: Object,
        required: true,
    },
});

</script>

<template>
    <Head title="Profile" />

    <DashboardLayouts>
        <article class="pb-3">
            <div class="h-44 relative">
                <div class="w-full h-24 bg-second"></div>
                <div class="absolute top-[15%] flex items-center gap-3 justify-between px-3">
                    <div class="relative">
                        <img :src="personal.image_profile" class="w-[120px] aspect-square rounded-full object-cover object-top" alt="Photo Profile">
                    </div>
                </div>
            </div>

            <div class="px-[10px]">
                <h1 class="text-xl font-bold">{{ personal.full_name }}</h1>
                <h2>Frontend Developer (React JS) | Junior Web Developer (Laravel/Express JS)</h2>
                <h3>{{ personal.address }}</h3>
                <h3>{{ personal.province_name }}<span>500+ connections</span></h3>
            </div>

            <div class="job-opportunities">
                <div>
                    <h3>Open to job opportunities</h3>
                    <p>Frontend Developer, Web Developer, Javascript Developer, PHP Developer roles</p>
                    <a href="#">See all details</a>
                </div>
                <div>
                    <i class='bx bxs-pencil'></i>
                </div>
            </div>
        </article>
    </DashboardLayouts>

</template>

<style scoped>

</style>
