<script setup>
defineProps({
    isLoading: {
        type: Boolean,
        required: true,
    },
    message: {
        type: String,
        required: true,
    }
})
</script>

<template>
    <div
        v-if="isLoading"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="flex flex-col items-center">
            <div
                class="w-16 h-16 border-4 border-t-4 border-gray-200 border-t-[#0AAB7C] rounded-full animate-spin"></div>
            <p class="mt-4 text-white">{{message}}...</p>
        </div>
    </div>
</template>

<style scoped>
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
