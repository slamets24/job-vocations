<template>
    <div class="flex flex-col justify-between border-2 shadow rounded-lg p-5 hover:border-second hover:border-2 w-full transition duration-300"
        :class="{ 'h-[360px] md:h-[220px] lg:h-[270px] xl:h-[230px]': user?.type !== 'company' }">
        <Link :href="route('jobs.show', job.slug)"
            class="flex md:items-center justify-between flex-col-reverse md:flex-row">

        <div class="space-y-1">
            <h1 class="text-xl font-semibold md:w-[450px] lg:w-80 text-pretty">{{ job.title }}
                <span v-if="!job.status" class="capitalize badge bg-red-500 text-white">ditutup</span>
            </h1>
            <p class="text-lg text-gray-500 break-all text-pretty">{{ job.company_name }} </p>
            <p class="text-sm text-gray-500 capitalize flex items-center gap-1">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        style="fill: rgba(0, 0, 0, 1);">
                        <path
                            d="M11.42 21.815a1.004 1.004 0 0 0 1.16 0C12.884 21.598 20.029 16.44 20 10c0-4.411-3.589-8-8-8S4 5.589 4 9.996c-.029 6.444 7.116 11.602 7.42 11.819zM12 4c3.309 0 6 2.691 6 6.004.021 4.438-4.388 8.423-6 9.731-1.611-1.308-6.021-5.293-6-9.735 0-3.309 2.691-6 6-6z">
                        </path>
                        <path d="M11 14h2v-3h3V9h-3V6h-2v3H8v2h3z"></path>
                    </svg>
                </span>{{ job.city_name }}, {{ job.province_name }}
            </p>
            <p class="text-base text-gray-500 flex items-center gap-1">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        style="fill: rgba(0, 0, 0, 1);">
                        <path
                            d="M21 4H3a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1zm-1 11a3 3 0 0 0-3 3H7a3 3 0 0 0-3-3V9a3 3 0 0 0 3-3h10a3 3 0 0 0 3 3v6z">
                        </path>
                        <path
                            d="M12 8c-2.206 0-4 1.794-4 4s1.794 4 4 4 4-1.794 4-4-1.794-4-4-4zm0 6c-1.103 0-2-.897-2-2s.897-2 2-2 2 .897 2 2-.897 2-2 2z">
                        </path>
                    </svg>
                </span>{{ formatRupiah(job.salary) }}
            </p>
        </div>
        <div class="avatar pb-4">
            <div class="w-24 rounded">
                <img :src="job.image" alt="Company Image" />
            </div>
        </div>
        </Link>

        <div class="flex justify-between">
            <p class="text-md text-gray-500 pt-1">{{ job.created_at }}</p>
            <ApplicationStatus v-if="job.application_status" :status="job.application_status" />
        </div>

        <div v-if="user?.type == 'company'">
            <div class="grid items-center justify-between grid-cols-2 card-actions mt-6">
                <Link :href="route('jobs.destroy', job.id)" method="delete" as="button"
                    class="btn btn-sm bg-red-500 text-white">Hapus
                </Link>
                <Link :href="route('jobs.edit', job.slug)" class="btn btn-sm bg-main text-white">Ubah
                </Link>
            </div>
            <div class="mt-2 grid items-center justify-between grid-cols-2 card-actions">
                <Link :href="route(job.has_tests ? 'cbt.edit' : 'cbt.create', job.slug)"
                    class="btn  w-full btn-sm bg-black text-white">
                {{ job.has_tests ? 'Ubah Soal' : 'Buat Soal' }}
                </Link>
                <Link :href="route('show.applicants', job.slug)" class="w-full btn btn-sm bg-second text-white">Check
                Pelamar
                </Link>
            </div>
        </div>

        <!-- Tampilkan saat login sebagai personal -->
        <div v-if="user?.type == 'personal' && job?.application_status === 'test' && job?.test_id">
            <Link :href="route('cbt.show', job.slug)" class="btn w-full btn-sm bg-second text-white">
            Mulai Test
            </Link>
        </div>


    </div>
</template>
<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { computed, defineProps } from "vue";
import ApplicationStatus from "@/Components/ApplicationStatus.vue";

const { props: pageProps } = usePage();
const user = computed(() => pageProps.auth.user);

const props = defineProps({
    job: {
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

const formatRupiah = (number) => {
    if (number == null) return "Rp. 0"; // Jika salary tidak tersedia
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(number);
};
</script>
<style lang=""></style>
