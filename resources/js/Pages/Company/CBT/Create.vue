<template>

    <Head title="Buat Soal" />
    <DashboardLayouts>
        <BlockUi message="Sedang Membuat Soal" :is-loading="form.processing" />

        <Alert v-if="errors.dbError || errors.generalError || form.errors.message || customAlert.show"
            :message="errors.dbError || errors.generalError || form.errors.message || customAlert.message"
            class="mt-5 alert-error w-full" type="error" />

        <div class="breadcrumbs text-sm mt-1">
            <ul>
                <li>
                    <Link :href="route('company.dashboard')">Dashboard</Link>
                </li>
                <li>
                    <Link :href="route('jobs.index')">Lowongan</Link>
                </li>
                <li>Buat Soal</li>
            </ul>
        </div>

        <h2 class="text-2xl font-bold mb-4 mt-4">Buat Soal untuk: {{ job.title }}</h2>

        <form @submit.prevent="submitTest" class="space-y-6">
            <!-- Detail Test -->
            <div class="space-y-4">
                <FormInput id="name" v-model="form.name" label="Nama Soal" placeholder="Masukkan nama soal"
                    :error="form.errors.name" required autofocus :disable="form.processing" />

                <FormTextarea id="description" v-model="form.description" label="Deskripsi"
                    helpText="Silahkan isi deskripsi soal, lebih detail maka lebih bagus"
                    :error="form.errors.description" required rows="10" :disable="form.processing" />

                <FormInput type="number" id="duration" v-model.number="form.duration" label="Durasi"
                    help-text="Durasi tes dalam menit" placeholder="Contoh: 60" :error="form.errors.duration" required
                    :disable="form.processing" min="0" />

                <FormInput type="number" id="passing_score" v-model.number="form.passing_score" label="Passing Score"
                    help-text="Bobot passing score" placeholder="Contoh: 10" :error="form.errors.passing_score" required
                    :disable="form.processing" min="0" />
            </div>

            <!-- Pertanyaan Section -->
            <div class="space-y-6">
                <h3 class="text-xl font-semibold">Pertanyaan</h3>

                <TransitionGroup name="list">
                    <div v-for="(question, qIndex) in form.questions" :key="qIndex"
                        class="p-4 border rounded-md shadow bg-second space-y-4">
                        <div>
                            <label :for="`question-${qIndex + 1}`" class="block text-sm font-medium text-white">
                                Pertanyaan ke- {{ qIndex + 1 }}
                            </label>
                            <input :id="`question-${qIndex + 1}`" v-model="question.question" type="text"
                                class="w-full mt-2 bg-[#F1F1F5] input"
                                :class="{ 'border-red-500': form.errors[`questions.${qIndex}.question`] }"
                                placeholder="Pertanyaan" required />

                            <div v-if="form.errors[`questions.${qIndex}.question`]" class="text-sm text-red-500 mt-1">
                                {{ form.errors[`questions.${qIndex}.question`] }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-white">Pilihan Opsi</label>
                            <TransitionGroup name="list">
                                <div v-for="(option, oIndex) in question.options" :key="oIndex"
                                    class="flex items-center gap-2">
                                    <input v-model="option.option_text" type="text" class="w-full bg-[#F1F1F5] input"
                                        :class="{ 'border-red-500': form.errors[`questions.${qIndex}.options.${oIndex}.option_text`] }"
                                        placeholder="Opsi" required />
                                    <div class="flex items-center gap-2">
                                        <input type="radio" :name="'correctAnswer' + qIndex" :value="oIndex"
                                            v-model="question.correctOptionIndex"
                                            @change="handleCorrectAnswer(qIndex, oIndex)" />
                                        <input type="number" v-model.number="option.points"
                                            class="w-16 bg-[#F1F1F5] input"
                                            :class="{ 'border-red-500': form.errors[`questions.${qIndex}.options.${oIndex}.points`] }"
                                            placeholder="Poin" min="0" required />
                                    </div>

                                    <!-- Hapus Opsi -->
                                    <Trash2 class="text-red-500 cursor-pointer hover:text-red-700"
                                        @click="removeOption(qIndex, oIndex)" />
                                </div>
                            </TransitionGroup>
                            <div class="flex justify-between">
                                <button @click="addOption(qIndex)" class="text-sm text-white hover:underline"
                                    type="button" :disabled="question.options.length >= 5">
                                    Tambah Opsi
                                </button>
                            </div>
                        </div>
                    </div>
                </TransitionGroup>
            </div>

            <!-- Tambah & Hapus Pertanyaan -->
            <div class="flex justify-between items-center mt-4">
                <button @click="addQuestion" type="button"
                    class="text-second font-bold btn btn-sm btn-outline hover:bg-second hover:border-second hover:text-white">
                    Tambah Pertanyaan
                </button>
                <button v-if="form.questions.length > 1" @click="removeQuestion(form.questions.length - 1)"
                    type="button"
                    class="text-red-500 font-bold btn btn-sm btn-outline hover:bg-red-500 hover:border-red-500 hover:text-white">
                    Hapus Pertanyaan
                </button>
            </div>

            <!-- Kirim Soal -->

            <ButtonSubmit class="w-full" :is-submitting="isSubmitting" :default-text="'Buat Soal'"
                :loading-text="'Sedang Membuat Soal...'" />
        </form>
    </DashboardLayouts>
</template>


<script setup>
// Import modul dan komponen yang dibutuhkan
import { ref } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import DashboardLayouts from "@/Layouts/DashboardLayouts.vue"; // Layout dashboard
import FormInput from "@/Components/FormInput.vue"; // Komponen input form
import FormTextarea from "@/Components/FormTextarea.vue"; // Komponen textarea
import { Trash2 } from 'lucide-vue-next'; // Ikon Trash dari Lucide
import Alert from "@/Components/Alert.vue"; // Komponen alert
import BlockUi from "@/Components/BlockUi.vue"; // Komponen block UI
import ButtonSubmit from "@/Components/ButtonSubmit.vue"; // Tombol untuk submit form

// Mendefinisikan props untuk menerima data dari server
const props = defineProps({
    job: {
        type: Object, // Data pekerjaan dalam bentuk objek
        required: true // Props `job` harus ada
    },
    errors: {
        type: Object, // Data kesalahan validasi
    }
});

// State untuk custom alert
const customAlert = ref({
    show: false, // Menandai apakah alert ditampilkan
    message: "", // Pesan yang akan ditampilkan pada alert
});

// State untuk menandai status pengiriman form
const isSubmitting = ref(false);

// Struktur awal untuk pertanyaan
const initialQuestionState = {
    question: "", // Teks pertanyaan
    options: [ // Pilihan jawaban
        { option_text: "", is_correct: false, points: '' }, // Opsi pertama
        { option_text: "", is_correct: false, points: '' }  // Opsi kedua
    ],
    correctOptionIndex: null // Indeks untuk opsi yang benar
};

// Membuat form menggunakan useForm dari Inertia
const form = useForm({
    job_posting_id: props.job.id, // ID posting pekerjaan
    name: "", // Nama tes
    description: "", // Deskripsi tes
    duration: "", // Durasi tes
    passing_score: "", // Nilai kelulusan
    questions: [{ ...initialQuestionState }] // Daftar pertanyaan dengan satu pertanyaan awal
});

// Fungsi untuk menambahkan pertanyaan baru ke form
const addQuestion = () => {
    const newQuestion = JSON.parse(JSON.stringify(initialQuestionState)); // Membuat salinan objek pertanyaan awal
    form.questions.push(newQuestion); // Menambahkan pertanyaan ke daftar
};

// Fungsi untuk menghapus pertanyaan
const removeQuestion = (index) => {
    if (form.questions.length > 1) { // Memastikan minimal ada satu pertanyaan
        form.questions.splice(index, 1); // Menghapus pertanyaan berdasarkan indeks
    } else {
        showAlert("Minimal harus ada satu pertanyaan."); // Menampilkan pesan jika mencoba menghapus pertanyaan terakhir
    }
};


// Fungsi untuk menambahkan opsi jawaban ke sebuah pertanyaan
const addOption = (qIndex) => {
    const question = form.questions[qIndex];
    if (question.options.length < 5) { // Membatasi maksimum 5 opsi per pertanyaan
        question.options.push({
            option_text: "", // Teks opsi kosong
            is_correct: false, // Menandai opsi belum benar
            points: "" // Poin opsi kosong
        });
    } else {

        showAlert("Maksimal 5 opsi per pertanyaan."); // Menampilkan pesan jika mencapai batas opsi
    }
};

// Fungsi untuk menghapus opsi jawaban dari sebuah pertanyaan
const removeOption = (qIndex, oIndex) => {
    const question = form.questions[qIndex];
    if (question.options.length > 2) { // Memastikan minimal ada dua opsi per pertanyaan
        question.options.splice(oIndex, 1); // Menghapus opsi berdasarkan indeks
    } else {
        showAlert("Minimal harus ada dua opsi."); // Menampilkan pesan jika mencoba menghapus opsi terakhir
    }
};

// Fungsi untuk menetapkan jawaban yang benar pada sebuah pertanyaan
const handleCorrectAnswer = (qIndex, oIndex) => {
    form.questions[qIndex].options.forEach((option, index) => {
        option.is_correct = index === oIndex; // Menandai opsi yang benar berdasarkan indeks
    });
};

// Fungsi untuk menampilkan alert dengan pesan tertentu
const showAlert = (message) => {
    customAlert.value = {
        show: true, // Menampilkan alert
        message, // Menyimpan pesan ke state alert
    };

    // Scroll halaman ke atas dengan animasi
    window.scrollTo({
        top: 0,
        behavior: "smooth", // Memberikan animasi scroll yang halus
    });

    // Menyembunyikan alert setelah 5 detik
    setTimeout(() => {
        customAlert.value.show = false;
    }, 5000);
};

// Fungsi untuk mengirimkan form ke server
const submitTest = () => {
    isSubmitting.value = true; // Menandai form sedang dikirim
    form.post(route("cbt.store"), { // Mengirimkan form ke server dengan route `cbt.store`
        onSuccess: () => {
            isSubmitting.value = false; // Menghentikan status pengiriman jika berhasil
            form.reset(); // Mereset form
            form.questions = [{ ...initialQuestionState }]; // Mengatur ulang daftar pertanyaan
        },
        onError: (errors) => {
            isSubmitting.value = false; // Menghentikan status pengiriman jika terjadi kesalahan
        },
        onFinish: () => {
            isSubmitting.value = false; // Selalu menghentikan status pengiriman setelah selesai
        },
    });
};
</script>


<style scoped>
.list-move,
.list-enter-active,
.list-leave-active {
    transition: all 0.5s ease;
}

.list-enter-from,
.list-leave-to {
    opacity: 0;
    transform: translateX(30px);
}
</style>
