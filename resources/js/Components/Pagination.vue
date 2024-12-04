<template>
    <div class="join" v-if="pagination.last_page > 1">
        <!-- Previous Page Link -->
        <Link
            :href="pagination.prev_page_url ?? ''"
            class="join-item btn"
            :class="{'btn-disabled': pagination.current_page === 1}">«
        </Link>

        <!-- First Page Button -->
        <Link
            v-if="showFirstPage"
            :href="pagination.first_page_url ?? ''"
            class="join-item btn">1
        </Link>

        <!-- Ellipsis Left -->
        <Link
            v-if="showLeftEllipsis"
            class="join-item btn btn-disabled">...
        </Link>

        <!-- Dynamic Page Buttons -->
        <Link
            v-for="page in dynamicPages"
            :key="page"
            :href="getPageUrl(page)"
            class="join-item btn"
            :class="{'bg-second text-white': page === pagination.current_page}"
        >
            {{ page }}
        </Link>

        <!-- Ellipsis Right -->
        <Link
            v-if="showRightEllipsis"
            class="join-item btn btn-disabled">...
        </Link>

        <!-- Last Page Button -->
        <Link
            v-if="showLastPage"
            :href="pagination.last_page_url ?? ''"
            class="join-item btn">{{ pagination.last_page }}
        </Link>

        <!-- Next Button -->
        <Link
            :href="pagination.next_page_url ?? ''"
            class="join-item btn"
            :class="{'btn-disabled': pagination.current_page === pagination.last_page}">»
        </Link>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    pagination: {
        type: Object,
        required: true
    }
})

// jumlah halaman di sekitar halaman saat ini
const RANGE = 2

// halaman dinamis yang akan ditampilkan
const dynamicPages = computed(() => {
    const current = props.pagination.current_page
    const total = props.pagination.last_page

    // Hitung rentang halaman
    let start = Math.max(1, current - RANGE)
    let end = Math.min(total, current + RANGE)

    // Buat array halaman
    const pages = []
    for (let i = start; i <= end; i++) {
        pages.push(i)
    }

    return pages
})

// Tampilkan tombol halaman pertama
const showFirstPage = computed(() => {
    return props.pagination.current_page > RANGE + 1
})

// Tampilkan ellipsis kiri
const showLeftEllipsis = computed(() => {
    return props.pagination.current_page > RANGE + 1
})

// Tampilkan ellipsis kanan
const showRightEllipsis = computed(() => {
    return props.pagination.current_page < props.pagination.last_page - RANGE
})

// Tampilkan tombol halaman terakhir
const showLastPage = computed(() => {
    return props.pagination.current_page < props.pagination.last_page - RANGE
})

// Dapatkan URL untuk halaman tertentu
const getPageUrl = (page) => {
    const pageLink = props.pagination.links.find(link =>
        link.label === String(page)
    )
    return pageLink ? pageLink.url : ''
}
</script>
