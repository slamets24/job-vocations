<template>
    <Head title="Profil Saya"/>

    <DashboardLayouts>
        <div class="max-w-7xl mx-auto space-y-4 mt-10 lg:px-4">

            <Image :personal/>

            <PersonalInformation :personal />

            <Biography :personal />

            <JobClassification :personal />

            <Sallary :personal />

        </div>

    </DashboardLayouts>
</template>
<script setup>
// Mengimpor ikon dari library lucide-vue-next
import { X, Pencil, Cake, Smartphone, MapPinned } from "lucide-vue-next"; // Ikon untuk UI

// Mengimpor komponen-komponen yang akan digunakan dalam template
import Footer from "@/Components/Footer.vue"; // Komponen footer
import Navbar from "@/Components/Navbar.vue"; // Komponen navbar
import { Head, Link } from "@inertiajs/vue3"; // Menggunakan Head dan Link dari Inertia.js untuk mengatur metadata dan navigasi
import DashboardLayouts from "@/Layouts/DashboardLayouts.vue"; // Layout untuk dashboard
import Drawer from "@/Components/Drawer.vue"; // Komponen untuk drawer (sidebar)
import ButtonSubmit from "@/Components/ButtonSubmit.vue"; // Komponen tombol submit
import FormInput from "@/Components/FormInput.vue"; // Komponen input teks
import Alert from "@/Components/Alert.vue"; // Komponen alert untuk notifikasi
import FormSelect from "@/Components/FormSelect.vue"; // Komponen select (dropdown)
import PersonalInformation from "@/Components/PersonalProfile/PersonalInformation.vue"; // Komponen untuk informasi pribadi
import Biography from "@/Components/PersonalProfile/Biography.vue"; // Komponen untuk biografi
import Image from "@/Components/PersonalProfile/Image.vue"; // Komponen untuk menampilkan gambar profil
import JobClassification from "@/Components/PersonalProfile/JobClassification.vue"; // Komponen untuk klasifikasi pekerjaan
import Sallary from "@/Components/PersonalProfile/Sallary.vue"; // Komponen untuk gaji

// Mendefinisikan props untuk komponen ini
const props = defineProps({
    personal: {
        type: Object, // Menyimpan objek data personal yang diterima sebagai props
        required: true // Menandakan bahwa props ini wajib ada
    }
})
</script>
<style></style>
