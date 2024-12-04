<script setup>
defineProps({
    isSubmitting: {
        type: Boolean,
        required: true,
        default: false
    },
    defaultText:{
        type: String,
        required: true
    },
    loadingText:{
        type: String,
        required: true
    }
});
</script>

<template>
    <button
        type="submit"
        class="text-white btn bg-second"
        :disabled="isSubmitting"
    >
        {{ isSubmitting ? loadingText : defaultText }}
    </button>
</template>

<style scoped>

</style>
