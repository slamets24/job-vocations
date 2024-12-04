<template>
    <div class="">
        <Head title="Profile Perusahaan"/>
        <DashboardLayouts>
            <div class=" flex justify-center items-center mt-10 ">
                <div class="text-center">
                    <div class="avatar">
                        <div class=" ring-offset-base-100 w-24 rounded-full ring ring-second">
                            <img alt="Photo Profile"
                                 :src="company.image_profile"/>
                        </div>
                    </div>

                    <div class="mt-2">
                        <h1 class="text-xl font-semibold">{{ company.company_name }}</h1>
                        <a v-if="company.social_media" :href="company.social_media" target="_blank"
                           class="text-md flex items-center gap-2 justify-center underline">Media Sosial <span><svg
                            xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            style="fill: rgba(0, 0, 0, 1);"><path
                            d="m13 3 3.293 3.293-7 7 1.414 1.414 7-7L21 11V3z"></path><path
                            d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7z"></path></svg></span></a>
                        <div class="">
                            <p class="break-all lg:max-w-2xl">{{ company.company_address }}</p>
                            <p class="text-sm ">{{ company.city_name }}, {{ company.province_name }},
                                <span>INDONESIA</span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex  justify-center items-center">
                <div class="mt-4 border shadow  lg:w-[80%] p-4">
                    <div class="flex  justify-between items-center">
                        <h1 class="text-2xl font-semibold mb-2"> Detail Perusahaan</h1>

                        <!-- Menampilkan tombol "Edit Profil" secara kondisional berdasarkan tipe pengguna Company.-->
                        <Link v-if="user && user.type === 'company'"
                              class="btn btn-sm btn-outline px-10 text-second hover:border-second hover:bg-second"
                              :href="route('company.profile.edit', company.slug)">Edit Profil
                        </Link>

                    </div>
                    <p class="break-all" v-html="descriptionFormatted"></p>
                </div>
            </div>
        </DashboardLayouts>
    </div>
</template>

<script setup>
// Mengimpor komponen dan hook yang diperlukan
import CardBenefit from "@/Components/Card/CardBenefit.vue"; // Komponen untuk menampilkan informasi tentang manfaat perusahaan
import DashboardLayouts from "@/Layouts/DashboardLayouts.vue"; // Layout untuk halaman dashboard
import { Head, Link, usePage } from "@inertiajs/vue3"; // Inertia.js untuk navigasi dan akses data halaman
import { computed } from "vue"; // Mengimpor computed property dari Vue

// Mengambil data halaman saat ini dengan usePage dari Inertia.js
const page = usePage();
const user = computed(() => page.props.auth.user); // Mengambil data pengguna yang sedang login

// Menentukan props yang diterima oleh komponen ini
const props = defineProps({
    company: Object, // Data perusahaan yang diterima melalui props
});

// Mengubah format deskripsi perusahaan agar tanda baris baru (\n) diganti dengan <br> untuk pemformatan HTML
const descriptionFormatted = computed(() => {
    return props.company.description.replace(/\n/g, '<br>'); // Memformat deskripsi agar baris baru terlihat di HTML
});
</script>
<style lang=""></style>
