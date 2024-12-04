<script setup>
defineProps({
    message: {
        type: String,
        required: true,
        default: '',
    },
});
</script>

<template>
    <p v-show="message" class="text-sm text-red-600">
        {{ message }}
    </p>
</template>
