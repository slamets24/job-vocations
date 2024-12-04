<template>
    <details class="collapse collapse-arrow bg-base-200">
        <summary class="collapse-title text-xl font-semibold">{{ title }}</summary>
        <div class="collapse-content my-2">
            <p>{{ content }}</p>
        </div>
    </details>
</template>
<script setup>
    defineProps({
        content: String,
        title: String
    })
</script>
<style lang="">

</style>
