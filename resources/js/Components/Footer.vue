<template>
    <footer class="mt-10 bg-second text-white">
        <div class="footer gap-3 p-4 max-w-7xl mx-auto">
            <aside class="grid-flow-col items-center">
                <h3 class="text-3xl font-bold">{{appName}}</h3>
            </aside>
            <nav
                class="md:grid-flow-col grid-cols-1 gap-2 md:gap-6 md:place-self-center md:justify-self-end"
            >
                <Link :href="route('terms')"> Persyaratan & Ketentuan </Link>
                <Link :href="route('security-privacy')"> Kemanan & Privasi </Link>
                <p> Copyright © 2024 <span class="font-semibold">{{appName}}</span> </p>
            </nav>
        </div>
    </footer>
</template>

<script setup>
import { usePage, Link } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage()
const appName = computed(() => page.props.appName);
</script>
