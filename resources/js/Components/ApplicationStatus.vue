<ApplicationStatus class="text-xs" :status="applicant.status"/>

<script setup>
import { computed } from "vue";

const props = defineProps({
    status: {
        type: String,
        required: true
    }
});

// Mapping status menjadi teks yang relevan
const statusMapping = {
    applied: "Baru Melamar",
    under_review: 'Sedang Direview',
    document_rejected: "Dokumen Ditolak",
    test_passed: "Tes Lulus",
    test_failed: "Tes Gagal",
    interview_passed: "Interview Lulus",
    interview_failed: "Interview Gagal",
    offered: "Ditawari Posisi",
    offer_rejected: "Ditolak Posisi",
    test: "Test",
    interview: "Interview",
    hired: "Diterima",
    rejected: "Ditolak",
};

const statusText = computed(() => statusMapping[props.status] || "Status Tidak Diketahui");
</script>

<template>
    <p class="text-base text-gray-500 font-semibold">
        <span>{{ statusText }}</span>
    </p>
</template>

<style scoped>

</style>
