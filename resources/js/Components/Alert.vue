<script setup>
import { computed } from "vue";

const props = defineProps({
    message: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        required: true,
        validator: (value) => ["info", "success", "warning", "error"].includes(value),
    },
    closable: {
        type: Boolean,
        default: false, // default tidak bisa di close
    },
});

const icon = computed(() => {
    switch (props.type) {
        case "info":
            return `
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>`;
        case "success":
            return `
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>`;
        case "warning":
            return `
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>`;
        case "error":
            return `
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>`;
        default:
            return "";
    }
});
</script>

<template>
    <div role="alert" class="alert">
        <div v-html="icon"></div>
        <span>{{ message }}</span>

        <!-- Tombol close hanya ditampilkan jika props `closable` adalah true -->
        <div v-if="closable" class="flex justify-center">
            <i @click="$emit('close')" class='bx bx-x bx-sm cursor-pointer'></i>
        </div>
    </div>
</template>

<style scoped>

</style>
