<script setup>
import { X } from "lucide-vue-next";

const props = defineProps({
    drawerName: {
        type: String,
        required:true
    }
})
</script>

<template>
    <div class="drawer drawer-end z-[9999]">
        <input :id="drawerName" type="checkbox" class="drawer-toggle"/>
        <div class="drawer-side">
            <label :for="drawerName" aria-label="close sidebar" class="drawer-overlay"></label>
            <ul class="relative w-full md:w-4/5 xl:w-3/4 min-h-full p-4 xl:py-8 bg-white menu text-base-content space-y-2 xl:space-y-4">
                <label :for="drawerName" aria-label="close sidebar"
                       class="drawer-overlay absolute top-4 right-4 cursor-pointer">
                    <X/>
                </label>

                <slot/>
            </ul>
        </div>
    </div>
</template>

<style scoped>

</style>
