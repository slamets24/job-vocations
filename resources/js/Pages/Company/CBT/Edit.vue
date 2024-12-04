<template>

    <Head title="Ubah Soal" />
    <DashboardLayouts>
        <BlockUi message="Sedang Mengubah Soal" :is-loading="form.processing" />

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
                <li>Ubah Soal</li>
            </ul>
        </div>

        <div class="flex items-center justify-between mb-4 mt-4">
            <h2 class="text-2xl font-bold">Ubah Soal untuk: {{ job.title }}</h2>
            <button @click="openModal" class="btn btn-sm bg-red-500 text-white hover:bg-red-600">
                Hapus Soal
            </button>
        </div>

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
                                    <Trash2 class="text-white cursor-pointer hover:text-red-700"
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

    <Modal :show="showDeleteModal" maxWidth="md" @close="closeModal">
        <div class="p-6">
            <h2 class="text-lg font-bold mb-4">Konfirmasi Penghapusan</h2>
            <p class="text-sm text-gray-700 mb-6">
                Apakah Anda yakin ingin menghapus soal ini? Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="flex justify-end space-x-3">
                <button @click="closeModal" class="btn btn-outline bg-gray-200 hover:bg-gray-300 text-gray-700">
                    Batal
                </button>
                <button @click="confirmDelete" class="btn bg-red-500 text-white hover:bg-red-600">
                    Hapus
                </button>
            </div>
        </div>
    </Modal>
</template>

<script setup>

// Import library dan komponen yang dibutuhkan
import { ref } from "vue"; // State dan reaktivitas dari Vue
import { Head, useForm, router, Link } from "@inertiajs/vue3"; // Inertia.js untuk routing dan form
import Modal from '@/Components/Modal.vue'; // Komponen modal
import DashboardLayouts from "@/Layouts/DashboardLayouts.vue"; // Layout dashboard
import BlockUi from "@/Components/BlockUi.vue"; // Komponen block UI
import { Trash2 } from 'lucide-vue-next'; // Ikon Trash
import ButtonSubmit from "@/Components/ButtonSubmit.vue"; // Tombol submit
import FormInput from "@/Components/FormInput.vue"; // Input form
import FormTextarea from "@/Components/FormTextarea.vue"; // Textarea
import Alert from "@/Components/Alert.vue"; // Komponen alert

// Props yang diterima dari server
const props = defineProps({
    job: {
        type: Array, // Daftar pekerjaan
        required: true
    },
    test: {
        type: Object, // Data tes
        required: true,
    },
    questions: {
        type: Array, // Daftar pertanyaan
        required: true,
    },
    errors: {
        type: Object, // Kesalahan validasi
    }
});

// State untuk memproses submit atau delete
const processing = ref(false);
// State untuk menampilkan modal delete
const showDeleteModal = ref(false);

const customAlert = ref({
    show: false, // Apakah alert ditampilkan
    message: "", // Pesan alert
});

// Fungsi untuk menampilkan alert
const showAlert = (message) => {
    customAlert.value = {
        show: true,
        message,
    };

    // Scroll ke atas dengan animasi
    window.scrollTo({
        top: 0,
        behavior: "smooth",
    });

    // Menyembunyikan alert setelah 5 detik
    setTimeout(() => {
        customAlert.value.show = false;
    }, 5000);
};

// Struktur awal untuk pertanyaan
const initialQuestionState = {
    question: "", // Teks pertanyaan
    options: [ // Daftar opsi
        { option_text: "", is_correct: false, points: 0 },
        { option_text: "", is_correct: false, points: 0 }
    ],
    correctOptionIndex: null, // Indeks opsi yang benar
};

// State form, diisi dengan data dari server
const form = useForm({
    name: props.test.name || "", // Nama tes
    description: props.test.description || "", // Deskripsi tes
    duration: props.test.duration || "", // Durasi tes
    passing_score: props.test.passing_score || "", // Skor kelulusan
    questions: props.questions.length > 0 ? JSON.parse(JSON.stringify(props.questions)) : [{ ...initialQuestionState }], // Daftar pertanyaan
});

// Fungsi untuk mengatur jawaban benar
const handleCorrectAnswer = (qIndex, oIndex) => {
    form.questions[qIndex].correctOptionIndex = oIndex; // Tetapkan indeks opsi benar
    form.questions[qIndex].options.forEach((opt, index) => {
        opt.is_correct = index === oIndex; // Tandai `is_correct` hanya untuk opsi yang benar
    });
};

// Fungsi untuk menambahkan pertanyaan baru
const addQuestion = () => {
    const newQuestion = JSON.parse(JSON.stringify(initialQuestionState));
    form.questions.push(newQuestion);
};

// Fungsi untuk menghapus pertanyaan
const removeQuestion = (index) => {
    if (form.questions.length > 1) { // Memastikan minimal ada satu pertanyaan
        form.questions.splice(index, 1);
    } else {
        showAlert("Minimal harus ada satu pertanyaan.");
    }
};

// Fungsi untuk menambahkan opsi baru ke pertanyaan
const addOption = (qIndex) => {
    const question = form.questions[qIndex];
    if (question.options.length < 5) { // Maksimal 5 opsi per pertanyaan
        question.options.push({
            option_text: "",
            is_correct: false,
            points: ""
        });
    }
};

// Fungsi untuk menghapus opsi dari pertanyaan
const removeOption = (qIndex, oIndex) => {
    const question = form.questions[qIndex];
    if (question.options.length > 2) { // Minimal 2 opsi per pertanyaan
        question.options.splice(oIndex, 1);
    } else {
        showAlert("Minimal harus ada dua opsi.");
    }
};

// Fungsi untuk mengirimkan form
const submitTest = async () => {
    console.log("Data to be submitted:", form); // Debug data yang dikirim
    try {
        await form.put(route('cbt.update', props.test.id), { // Kirim form ke server
            onSuccess: () => {
                console.log("Form submitted successfully");
                form.reset(); // Reset form setelah sukses
                form.questions = [{ ...initialQuestionState }]; // Reset pertanyaan
                processing.value = false;
            },
            onError: (errors) => {
                console.error("Validation errors:", errors); // Log error validasi
                processing.value = false;
            },
        });
    } catch (error) {
        console.error("Error submitting form:", error); // Log error pengiriman
        processing.value = false;
    }
};

// Fungsi untuk membuka modal konfirmasi delete
const openModal = () => {
    showDeleteModal.value = true;
};

// Fungsi untuk menutup modal
const closeModal = () => {
    showDeleteModal.value = false;
};

// Fungsi untuk menghapus data tes
const confirmDelete = async () => {
    closeModal(); // Tutup modal sebelum memproses delete
    try {
        await router.delete(route("cbt.destroy", props.test.id), { // Kirim permintaan delete
            onSuccess: () => {
                router.visit(route("cbt.index")); // Redirect ke halaman index
            },
            onError: (errors) => {
                console.error("Error deleting test:", errors); // Log error delete
            },
        });
    } catch (error) {
        console.error("Error deleting test:", error); // Log error
    }
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

.list-leave-active {
    position: absolute;
}
</style>
