<script setup>
import { Head, Link, usePage } from "@inertiajs/vue3";
import DashboardLayouts from "@/Layouts/DashboardLayouts.vue";
import Hero from "@/Components/Hero.vue";
import CardJob from "@/Components/Card/CardJob.vue";
import Pagination from "@/Components/Pagination.vue";
import { computed, ref } from "vue";
import {
    BeakerIcon, BriefcaseIcon, BuildingIcon,
    CameraIcon,
    ChartBarIcon,
    CodeIcon,
    DatabaseIcon, DollarSignIcon, GlobeIcon,
    HeartIcon,
    PencilIcon, TrendingUpIcon,
    TruckIcon, UserIcon
} from "lucide-vue-next";

const props = defineProps({
    recommendedJobs: {
        type: Array,
        required: false,
    },
    jobOthers: {
        type: Array,
        required: false,
    },
    jobs: {
        type: Array,
        required: false,
    }
});

const page = usePage();
const appName = computed(() => page.props.appName);
const user = computed(() => page.props.auth.user)

const jobCategories = ref([{
    name: 'Technology',
    icon: CodeIcon,
},
    {
        name: 'Business',
        icon: ChartBarIcon,
    },
    {
        name: 'Design',
        icon: PencilIcon,
    },
    {
        name: 'Data Science',
        icon: DatabaseIcon,
    },
    {
        name: 'Marketing',
        icon: CameraIcon,
    },
    {
        name: 'Engineering',
        icon: BeakerIcon,
    },
    {
        name: 'Logistics',
        icon: TruckIcon,
    },
    {
        name: 'Healthcare',
        icon: HeartIcon,
    },
]);

const features = ref([{
    icon: BriefcaseIcon,
    title: 'Ribuan Pekerjaan',
    description: 'Akses berbagai peluang kerja dari perusahaan terkemuka di berbagai industri, diperbarui setiap hari.'
},
    {
        icon: TrendingUpIcon,
        title: 'Alat Pertumbuhan Karir',
        description: 'Manfaatkan pembuat resume, dan sumber saran karier kami untuk memajukan perjalanan profesional Anda.'
    },
    {
        icon: DollarSignIcon,
        title: 'Wawasan Gaji',
        description: 'Dapatkan informasi gaji dan tren kompensasi yang akurat untuk membantu Anda menegosiasikan tawaran pekerjaan yang lebih baik.'
    }
]);

const statistics = ref([{
    label: 'Daftar Pekerjaan Aktif',
    value: '150,000+',
    icon: BriefcaseIcon
},
    {
        label: 'Perekrutan Perusahaan',
        value: '50,000+',
        icon: BuildingIcon
    },
    {
        label: 'Pencari Kerja Terdaftar',
        value: '2M+',
        icon: UserIcon
    },
    {
        label: 'Provinsi yang Dicakup',
        value: '30+',
        icon: GlobeIcon
    },
]);
</script>

<template>
    <Head title="Lowongan Kerja"/>

    <DashboardLayouts>
        <Hero/>

        <!-- Memunculkan hasil dari pencarian ketika ada datanya -->
        <section v-if="jobs">
            <h1 class="mt-10 text-2xl font-bold">Hasil Pencarian Anda</h1>
            <div class="mt-3 space-y-5">
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2" v-if="jobs.data.length > 0">
                    <CardJob v-for="job in jobs.data" :key="job.id" :job="job"/>
                </div>
                <div v-else>
                    <p class="text-gray-500">Tidak ada lowongan yang dicari.</p>
                </div>
            </div>

            <Pagination class="mt-10" :pagination="jobs"/>
        </section>

        <!-- Recommendation job Section -->
        <section>
            <div v-if="recommendedJobs">
                <h1 class="mt-10 text-2xl font-bold">Direkomendasikan untuk kamu</h1>
                <div class="mt-3 space-y-5">
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2" v-if="recommendedJobs.length > 0">
                        <CardJob v-for="job in recommendedJobs" :key="job.id" :job="job"/>
                    </div>
                    <div v-else>
                        <p class="text-gray-500">Tidak ada lowongan yang direkomendasikan.</p>
                        <p class="text-gray-500">Pastikan anda telah mengisi Profil.</p>
                    </div>
                </div>
            </div>

            <div v-if="jobOthers">
                <h1 class="mt-10 text-2xl font-bold">Lowongan Lainnya</h1>
                <div class="mt-3 space-y-5">
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2" v-if="jobOthers.length > 0">
                        <CardJob v-for="job in jobOthers" :key="job.id" :job="job"/>
                    </div>
                    <div v-else>
                        <p class="text-gray-500">Tidak ada lowongan.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Job Categories Section -->
        <section v-if="!jobs" class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12">Ada Beberapa Kategori Pekerjaan</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <div v-for="category in jobCategories" :key="category.name"
                         class="bg-gray-100 rounded-lg p-6 text-center group hover:bg-second transition duration-300">
                        <component :is="category.icon"
                                   class="w-12 h-12 mx-auto mb-4 text-second group-hover:text-white"/>
                        <h3 class="font-semibold text-lg mb-2 group-hover:text-white">{{ category.name }}</h3>
                    </div>
                </div>
            </div>
        </section>

        <!-- Statistics Section -->
        <section v-if="!jobs" class="py-20 bg-second text-white rounded-md ">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-20">Keunggulan {{ appName }} </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div v-for="stat in statistics" :key="stat.label" class="text-center">
                        <component :is="stat.icon" class="w-12 h-12 mx-auto mb-4"/>
                        <div class="text-4xl font-bold mb-2">{{ stat.value }}</div>
                        <p class="text-xl">{{ stat.label }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section v-if="!jobs" class=" py-20">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12">Mengapa Memilih {{ appName }}?</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div v-for="feature in features" :key="feature.title" class="text-center">
                        <component :is="feature.icon" class="w-12 h-12 mx-auto mb-4 text-second"/>
                        <h3 class="text-xl font-semibold mb-2">{{ feature.title }}</h3>
                        <p class="text-gray-600">{{ feature.description }}</p>
                    </div>
                </div>
            </div>
        </section>
    </DashboardLayouts>

</template>

<style scoped>

</style>
