<template>
    <div class="mx-auto px-4 xl:px-0 ">
        <div class="relative">
            <div class="overflow-hidden">
                <div
                    class="flex transition-transform duration-300 ease-in-out"
                    :style="{
                        transform: `translateX(-${currentSlide * 100}%)`,
                    }"
                >
                    <div
                        v-for="company in companies"
                        :key="company.id"
                        class="w-full flex-shrink-0 px-2 sm:w-1/2 md:w-1/3 lg:w-1/5">
                        <div class="p-2">
                            <!-- Added padding around each card -->
                            <CardCompany
                                :company
                                class="w-full h-full"
                            />
                        </div>
                    </div>

                </div>
            </div>
            <button
                @click="prevSlide"
                class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-white p-2 rounded-full shadow-md text-second hover:text-second focus:outline-none focus:ring-2 focus:ring-second focus:ring-opacity-50"
                aria-label="Previous slide"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7"
                    />
                </svg>
            </button>
            <button
                @click="nextSlide"
                class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-white p-2 rounded-full shadow-md text-second hover:text-second focus:outline-none focus:ring-2 focus:ring-second focus:ring-opacity-50"
                aria-label="Next slide"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5l7 7-7 7"
                    />
                </svg>
            </button>
        </div>
        <div class="flex justify-center mt-4">
            <button
                v-for="(_, index) in Math.ceil(companies.length / itemsPerSlide)"
                :key="index"
                @click="goToSlide(index)"
                class="mx-1 w-3 h-3 rounded-full focus:outline-none focus:ring-2 focus:ring-second focus:ring-opacity-50"
                :class="currentSlide === index ? 'bg-second' : 'bg-gray-300'"
                :aria-label="`Go to slide ${index + 1}`"
            ></button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import CardCompany from "./Card/CardCompany.vue";

const props = defineProps({
    companies: {
        type: Object,
        required: true
    }
})

const currentSlide = ref(0);
const itemsPerSlide = ref(1);

const updateItemsPerSlide = () => {
    if (window.innerWidth >= 1024) {
        itemsPerSlide.value = 5;
    } else if (window.innerWidth >= 768) {
        itemsPerSlide.value = 3;
    } else if (window.innerWidth >= 640) {
        itemsPerSlide.value = 2;
    } else {
        itemsPerSlide.value = 1;
    }
};

const totalSlides = computed(() =>
    Math.ceil(props.companies.value.length / itemsPerSlide.value)
);

const nextSlide = () => {
    currentSlide.value = (currentSlide.value + 1) % totalSlides.value;
};

const prevSlide = () => {
    currentSlide.value =
        (currentSlide.value - 1 + totalSlides.value) % totalSlides.value;
};

const goToSlide = (index) => {
    currentSlide.value = index;
};

onMounted(() => {
    updateItemsPerSlide();
    window.addEventListener("resize", updateItemsPerSlide);
});

onUnmounted(() => {
    window.removeEventListener("resize", updateItemsPerSlide);
});
</script>
