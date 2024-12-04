<template>
    <div class=" bg-gray-50 py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl md:text-4xl font-bold text-center text-gray-900 mb-12">
                Dapatkan gambaran yang jelas sebelum melamar
            </h1>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-8">
                <!-- Culture and Values -->
                <div class="flex flex-col items-center text-center p-6">
                    <div class="w-16 h-16 mb-6 flex items-center justify-center">
                        <div class="relative">
                            <div class="w-12 h-12 bg-second/20 rounded-full flex items-center justify-center">
                                <HandHeart class="w-6 h-6 text-second"/>
                            </div>
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                        Budaya dan nilai
                    </h3>
                    <p class="text-gray-600">
                        Cari tahu tentang budaya perusahaan
                    </p>
                </div>

                <!-- Ratings and Reviews -->
                <div class="flex flex-col items-center text-center p-6">
                    <div class="w-16 h-16 mb-6 flex items-center justify-center">
                        <div class="w-12 h-12 bg-second/20 rounded-full flex items-center justify-center">
                            <MapPinned class="w-6 h-6 text-second"/>
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                        Lokasi
                    </h3>
                    <p class="text-gray-600">
                        Cari tahu lokasi perusahaan
                    </p>
                </div>

                <!-- Benefits -->
                <div class="flex flex-col items-center text-center p-6">
                    <div class="w-16 h-16 mb-6 flex items-center justify-center">
                        <div class="w-12 h-12 bg-second/20 rounded-full flex items-center justify-center">
                            <Gift class="w-6 h-6 text-second"/>
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                        Tunjangan dan keuntungan
                    </h3>
                    <p class="text-gray-600">
                        Temukan keuntungan yang penting bagi Anda
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { HandHeart, MapPinned, Gift } from 'lucide-vue-next'
</script>

<style scoped>
.grid {
    transition: all 0.3s ease-in-out;
}

.grid > div {
    transition: transform 0.3s ease-in-out;
}

.grid > div:hover {
    transform: translateY(-5px);
}
</style>
