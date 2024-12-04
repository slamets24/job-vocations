<template>
    <Head title="Cari Perusahaan"/>
    <DashboardLayouts>
        <section>
            <div class="mt-5">
                <div
                    class="space-y-4 text-white bg-second rounded-lg px-6 py-7 md:p-10 lg:p-14">

                    <div class="space-y-4">
                        <h1 class="text-3xl font-bold">
                            Temukan perusahaan yang tepat untuk Anda
                        </h1>
                        <p>
                            Semua yang perlu diketahui tentang perusahaan, di satu
                            tempat
                        </p>
                    </div>

                    <!-- Search Form Perusahaan -->
                    <form @submit.prevent="submit" class="flex items-center gap-2">
                        <input
                            v-model="form.search"
                            type="text"
                            class="input input-bordered w-full max-w-xs text-black"
                            placeholder="Cari perusahaan"
                        />
                        <button
                            :disabled="form.processing"
                            type="submit"
                            class="bg-yellow-500 text-white px-6 py-3 rounded-md font-semibold hover:bg-yellow-400 transition duration-300">
                            Cari
                        </button>
                    </form>

                </div>

                <div class="mt-10">
                    <div>
                        <h1 class="text-2xl font-semibold">Explore Perusahaan</h1>
                        <p class="text-gray-500">
                            Temukan lowongan baru, budaya perusahaan,
                            fasilitas, dan tunjangan.
                        </p>
                    </div>

                    <!-- Card Slider -->
                    <div class="mt-5">
                        <CardSlider :companies />
                    </div>
                </div>
            </div>
        </section>
        <div class="mt-10">
            <CompanyInfoSection/>
        </div>
        <section></section>
    </DashboardLayouts>
</template>
<script setup>
import Footer from "@/Components/Footer.vue";
import Navbar from "@/Components/Navbar.vue";
import CompanyInfoSection from "@/Components/CompanyInfoSection.vue";
import { Head, useForm } from "@inertiajs/vue3";
import CardSlider from "@/Components/CardSlider.vue";
import DashboardLayouts from "@/Layouts/DashboardLayouts.vue";

const props = defineProps({
    companies: {
        type: Object,
        required: true
    }
})

const form  =useForm({
    search:''
})

const submit = () => {
    form.get(route("companies.search"));
};
</script>

<style scoped></style>
