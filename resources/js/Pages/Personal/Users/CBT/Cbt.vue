<template>
    <div>
        <div class="min-h-screen flex flex-col">
            <main class="flex-1 flex">
                <!-- Sidebar -->
                <transition name="slide" mode="out-in">
                    <aside v-if="isSidebarOpen"
                        class="fixed inset-0 z-40 md:hidden w-3/4 bg-white border-r border-gray-200 h-full overflow-y-auto">
                        <nav class="p-4">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-lg font-semibold">Pertanyaan</h2>
                                <button @click="isSidebarOpen = false" class="p-2 focus:outline-none">
                                    <span class="text-gray-500">Tutup</span>
                                </button>
                            </div>
                            <div class="grid grid-cols-4 gap-2">
                                <button v-for="(question, index) in questions" :key="index" @click="goToQuestion(index)"
                                    class="w-full text-center px-2 py-1 border-2 rounded-md text-sm" :class="{
                                        'bg-second text-white': currentQuestion === index,
                                        'hover:bg-gray-100': currentQuestion !== index,
                                        'font-semibold bg-green-100': selectedAnswers[index] !== -1,
                                    }">
                                    {{ index + 1 }}
                                </button>
                            </div>
                        </nav>
                    </aside>
                </transition>

                <!-- Main content area -->
                <div class="flex-1 md:mt-0 overflow-y-auto relative">
                    <div class="mx-auto">
                        <!-- Test Interface -->
                        <div v-if="!testCompleted" class="p-4 mt-6 px-4">
                            <!-- Mobile sidebar toggle -->
                            <button v-if="!isSidebarOpen" @click="isSidebarOpen = !isSidebarOpen"
                                class="md:hidden fixed top-4 left-4 z-50 p-2 bg-white rounded-full shadow-lg focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24"
                                    class="fill-[#46A87C]">
                                    <path d="M10.296 7.71L14.621 12l-4.325 4.29 1.408 1.42L17.461 12l-5.757-5.71z">
                                    </path>
                                    <path d="M6.704 6.29L5.296 7.71 9.621 12l-4.325 4.29 1.408 1.42L12.461 12z"></path>
                                </svg>
                            </button>

                            <!-- Desktop Sidebar -->
                            <div class="flex gap-4">
                                <div
                                    class="hidden md:block md:w-1/3 lg:w-1/4 bg-white border-r border-gray-200 h-full overflow-y-auto">
                                    <nav class="p-4">
                                        <div class="flex justify-between items-center mb-4">
                                            <h2 class="text-lg font-semibold">Pertanyaan</h2>
                                        </div>
                                        <div class="grid grid-cols-4 gap-2 md:grid-cols-3 lg:grid-cols-4">
                                            <button v-for="(question, index) in questions" :key="index"
                                                @click="goToQuestion(index)"
                                                class="w-full text-center px-2 py-1 border-2 rounded-md text-sm" :class="{
                                                    'bg-second text-white': currentQuestion === index,
                                                    'hover:bg-gray-100': currentQuestion !== index,
                                                    'font-semibold bg-green-100': selectedAnswers[index] !== -1,
                                                }">
                                                {{ index + 1 }}
                                            </button>
                                        </div>
                                    </nav>
                                </div>
                                <div class="mt-10 w-full px-3">
                                    <div
                                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            Pertanyaan {{ currentQuestion + 1 }} dari {{ questions.length }}
                                        </h2>
                                        <span class="text-sm font-medium text-gray-500 mt-2 sm:mt-0">
                                            Waktu Sisa: {{ formatTime(timeRemaining) }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                                        <div class="bg-second h-2.5 rounded-full"
                                            :style="{ width: `${((currentQuestion + 1) / questions.length) * 100}%` }">
                                        </div>
                                    </div>
                                    <p v-if="questions[currentQuestion]" class="text-base text-gray-700 mb-4 break-all">
                                        {{ questions[currentQuestion]?.question }}
                                    </p>
                                    <p v-else class="text-red-500">Pertanyaan tidak tersedia.</p>

                                    <div class="space-y-2">
                                        <label v-for="(option, index) in questions[currentQuestion]?.options"
                                            :key="index" class="flex items-center space-x-2">
                                            <input type="radio" :id="`option-${index}`" :value="option.id"
                                                v-model="selectedAnswers[currentQuestion]"
                                                class="focus:ring-second h-4 w-4 text-second border-gray-300" />
                                            <span class="text-gray-700">{{ option.option_text }}</span>
                                        </label>
                                    </div>

                                    <div
                                        class="mt-5 flex flex-col sm:flex-row justify-between space-y-2 sm:space-y-0 sm:space-x-2">
                                        <button @click="previousQuestion" :disabled="currentQuestion === 0"
                                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-second disabled:opacity-50">
                                            Sebelumnya
                                        </button>
                                        <button v-if="currentQuestion < questions.length - 1" @click="nextQuestion"
                                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-second hover:opacity-[0.5] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-second">
                                            Selanjutnya
                                        </button>
                                        <button v-else @click="finishTest"
                                            :disabled="selectedAnswers.some(answer => answer === -1)"
                                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                            Selesai Test
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Results Screen -->
                        <div v-else class="absolute top-0 left-0 flex items-center justify-center w-full h-full p-4">
                            <div>
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Hasil Test</h2>
                                <p class="text-3xl font-bold text-second mb-4">Score: {{ score }} / {{ questions.length
                                    }}</p>
                                <div class="mt-5">
                                    <button @click="restartTest"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-second hover:opacity-[0.5] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-second">
                                        Kembali Ke Halaman Utama
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>


<script setup>
import { ref, onMounted, watch, onUnmounted } from "vue";
import { router } from '@inertiajs/vue3';

// Data yang diterima dari server melalui props
const props = defineProps({
    test: Object,
    questions: Array,
    timeLimit: Number, // Durasi dalam menit
});

// Inisialisasi variabel
const currentQuestion = ref(0);
const isSidebarOpen = ref(false);
const selectedAnswers = ref(new Array(props.questions.length).fill(-1));
const timeRemaining = ref(props.timeLimit * 60);
const testCompleted = ref(false);
const score = ref(0);
const alertMessage = ref("");
const alertType = ref("");

const showAlert = (message, type = "info") => {
    alertMessage.value = message;
    alertType.value = type;
};

// Timer Logika
const startTimer = () => {
    const timer = setInterval(() => {
        if (timeRemaining.value > 0) {
            timeRemaining.value--;
        } else {
            clearInterval(timer);
            // Hentikan interval
            if (!testCompleted.value) {
                showAlert("Waktu habis. Tes akan selesai secara otomatis.", "warning");
                finishTest(); // Selesaikan tes otomatis
                clearInterval(timer);
            }
        }
    }, 1000);
};

// Fungsi untuk mengubah waktu menjadi format menit:detik
const formatTime = (seconds) => {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toString().padStart(2, "0")}`;
};

// Fungsi untuk berpindah ke pertanyaan selanjutnya
const nextQuestion = () => {
    if (currentQuestion.value < props.questions.length - 1) {
        currentQuestion.value++;
    }
};

// Fungsi untuk berpindah ke pertanyaan sebelumnya
const previousQuestion = () => {
    if (currentQuestion.value > 0) {
        currentQuestion.value--;
    }
};

// Fungsi untuk berpindah ke pertanyaan tertentu
const goToQuestion = (index) => {
    currentQuestion.value = index;
    isSidebarOpen.value = false;
};

// Fungsi untuk menghitung skor
const calculateScore = () => {
    score.value = selectedAnswers.value.reduce((total, answer, index) => {
        const selectedOption = props.questions[index].options.find((opt) => opt.id === answer);
        return total + (selectedOption ? selectedOption.points : 0);
    }, 0);
};

// Fungsi untuk selesaikan tes
const finishTest = async () => {
    const validAnswers = selectedAnswers.value.map((answer, index) => {
        if (answer !== -1) { // Hanya simpan jawaban yang telah dijawab
            return {
                user_id: props.test.user_id,
                test_id: props.test.id,
                question_id: props.questions[index].id,
                option_id: answer,
            };
        }
    }).filter(Boolean); // Hapus nilai null atau undefined

    testCompleted.value = true;
    calculateScore();

    try {
        router.post(route('cbt.submit.answers'), {
            answers: validAnswers,
            test_id: props.test.id,
            score: score.value,
        }, {
            onSuccess: (page) => {
                showAlert("Jawaban berhasil disimpan secara otomatis.", "success");
                localStorage.removeItem(`savedAnswers_${props.test.id}`);
                localStorage.removeItem(`timeRemaining_${props.test.id}`);
            },
            onError: () => {
                showAlert("Terjadi kesalahan saat menyimpan jawaban otomatis.", "error");
            },
        });
    } catch (error) {
        console.error("Error menyimpan jawaban:", error);
        showAlert("Terjadi kesalahan saat menyimpan jawaban otomatis.", "error");
    }
};

// Fungsi untuk memulai ulang tes
const restartTest = () => {
    currentQuestion.value = 0;
    selectedAnswers.value = new Array(props.questions.length).fill(-1);
    timeRemaining.value = props.timeLimit * 60;
    testCompleted.value = false;
    score.value = 0;
    router.visit('/');
};

/*
Fungsi ini dibuat untuk memastikan data jawaban (selectedAnswers) dan waktu yang tersisa (timeRemaining)
tetap /tersimpan, meskipun pengguna merefresh halaman atau meninggalkan halaman tersebut.
Data ini memungkinkan pengguna untuk melanjutkan proses tanpa kehilangan informasi sebelumnya.
*/
watch(
    [selectedAnswers, timeRemaining],
    ([newAnswers, newTimeRemaining]) => {
        localStorage.setItem(`savedAnswers_${props.test.id}`, JSON.stringify(newAnswers));
        localStorage.setItem(`timeRemaining_${props.test.id}`, newTimeRemaining.toString());
    },
    { deep: true }
);

// Lifecycle Hooks
onMounted(() => {
    // 1. Mengembalikan jawaban dan waktu yang tersimpan
    const savedAnswers = localStorage.getItem(`savedAnswers_${props.test.id}`);
    const savedTimeRemaining = localStorage.getItem(`timeRemaining_${props.test.id}`);

    if (savedAnswers) {
        selectedAnswers.value = JSON.parse(savedAnswers);
    }
    if (savedTimeRemaining) {
        timeRemaining.value = parseInt(savedTimeRemaining, 10);
    } else {
        timeRemaining.value = props.timeLimit * 60;
    }

    // Mulai timer setelah memuat data tersimpan
    startTimer();

    // 2. Tambahkan event listener untuk sebelum meninggalkan halaman
    const handleBeforeUnload = (event) => {
        if (!testCompleted.value) {
            event.preventDefault();
            event.returnValue = "Tes belum selesai. Apakah Anda yakin ingin meninggalkan halaman?";
            return "Tes belum selesai. Apakah Anda yakin ingin meninggalkan halaman?";
        }
    };

    window.addEventListener("beforeunload", handleBeforeUnload);

    // Membersihkan event listener pada unmounted
    onUnmounted(() => {
        window.removeEventListener("beforeunload", handleBeforeUnload);
    });
});

</script>


<style>
/* Sidebar slide transition */
.slide-enter-active,
.slide-leave-active {
    transition: transform 0.3s ease;
}

.slide-enter {
    transform: translateX(-100%);
}

.slide-leave-to {
    transform: translateX(-100%);
}
</style>
