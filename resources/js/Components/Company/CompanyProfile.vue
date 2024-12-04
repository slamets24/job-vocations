<template>
    <div class="border-2 lg:w-1/2 w-full p-6 rounded-xl shadow">
        <div class="avatar">
            <div class="w-24 rounded">
                <img
                    :src="job.image" alt="Company Image"/>
            </div>
        </div>
        <div class="space-y-6">
            <div class="mt-2">
                <h2 class="text-xl">{{ job.company_name }}</h2>
            </div>
            <div class="w-full max-h-48 overflow-hidden text-ellipsis ">
                <p class="text-gray-500">
                    Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the
                    industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type
                    and scrambled it to make a type specimen book. It has survived not only five centuries, but also the
                    leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s
                    with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop
                    publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                </p>
            </div>
            <div class="">
                <Link
                    :href="route('companies.show', job.company_slug)"
                    class="btn btn-outline hover:bg-white/20 hover:text-black">
                    Selengkapnya Tentang Perusahaan
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path
                                d="m11.293 17.293 1.414 1.414L19.414 12l-6.707-6.707-1.414 1.414L15.586 11H6v2h9.586z">
                            </path>
                        </svg>
                    </span>
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from "vue";

const props = defineProps({
    job: {
        type: Object,
        required: true
    }
})

const descriptionFormatted = computed(() => {
    return props.job.description.replace(/\n/g, '<br>');
});
</script>
<style lang=""></style>
