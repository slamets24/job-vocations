<template>
    <Link :href="route('companies.show', company.slug)" class="w-50 p-4 px-4  overflow-hidden card space-y-4 bg-base-100 h-52  border">
        <div class="avatar">
            <div class="w-20 rounded">
                <img
                    :src="company.image"
                    alt="Perusahaan Gambar"
                />
            </div>
        </div>
        <div class="truncate overflow-hidden">
            <h1>{{ company.company_name }}</h1>
        </div>
    </Link>
</template>
<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    company: {
        type: Object
    },
})
</script>
<style></style>
