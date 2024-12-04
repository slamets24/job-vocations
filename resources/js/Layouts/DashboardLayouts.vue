<template>
    <Navbar/>
    <section class="px-4 mx-auto mb-4 max-w-7xl min-h-dvh">

        <!-- Jika belum mengisi profile dan sudah login, tampilkan pesan -->
        <template v-if="!profileFilled && user">
            <Alert
                message="Anda belum mengisi profile, silahkan isi terlebih dahulu!"
                class="my-5 alert-error"
                type="error"
                :closable="true"
                @close="profileFilled = true"/>
        </template>

        <!-- Jika terdapat pesan 'success', maka tampilkan pesan -->
        <Alert
            v-if="success"
            :message="success"
            class="mt-5 alert-success"
            type="success"
            :closable="true"
            @close="success = null"/>

        <slot/>
    </section>
    <Footer/>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import Alert from "@/Components/Alert.vue";
import Navbar from "@/Components/Navbar.vue";
import { usePage } from "@inertiajs/vue3";
import Footer from "@/Components/Footer.vue";

const page = usePage();
const profileFilled = ref(true);

const user = computed(() => page.props.auth.user)
const success = ref(page.props.flash.success || null);

async function checkProfileFilled() {
    try {
        const response = await fetch(route('check.profile'));
        const data = await response.json();

        profileFilled.value = data.profile_filled;
    } catch (error) {
        console.error("Gagal memeriksa status profil:", error);
    }
}

onMounted(() => {
    checkProfileFilled();
});
</script>
