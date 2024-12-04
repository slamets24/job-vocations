<script setup>
import { computed } from 'vue'

const props = defineProps(
    {
        status: Number,
        message: String,
        user: Object
    })

const code = computed(() => {
    return {
        503: 503,
        500: 500,
        404: 404,
        403: 403,
        419: 419,
    }[props.status]
})

const title = computed(() => {
    return {
        503: 'Layanan Tidak Tersedia',
        500: 'Kesalahan Server',
        404: 'Halaman Tidak Ditemukan',
        403: 'Akses Ditolak',
        419: 'Halaman Kedaluwarsa',
    }[props.status]
})

const description = computed(() => {
    return {
        503: 'Maaf, kami sedang melakukan pemeliharaan. Silakan periksa kembali nanti.',
        500: 'Oops, terjadi kesalahan di server kami.',
        404: 'Maaf, halaman yang Anda cari tidak dapat ditemukan.',
        403: 'Maaf, Anda tidak diizinkan mengakses halaman ini.',
        419: 'Halaman ini sudah kedaluwarsa.',
    }[props.status]
})
</script>

<template>
    <div class="flex justify-center items-center h-screen px-4">
        <div class="space-y-2 text-center" >
            <h1 class="text-3xl font-bold text-second">{{ code }}</h1>
            <h2 class="text-2xl font-bold text-second">{{ title }}</h2>
            <p class="text-lg text-second">{{ description }}</p>
        </div>
    </div>
</template>
