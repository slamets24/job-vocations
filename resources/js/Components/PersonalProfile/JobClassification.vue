<script setup>
import { Pencil } from "lucide-vue-next";
import Drawer from "@/Components/Drawer.vue";
import Alert from "@/Components/Alert.vue";
import { computed, onMounted, ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import FormTextarea from "@/Components/FormTextarea.vue";
import ButtonSubmit from "@/Components/ButtonSubmit.vue";
import { useJobClassifications } from "@/Composables/index.js";
import FormSelect from "@/Components/FormSelect.vue";

const props = defineProps({
    personal: {
        type: Object,
        required: true
    }
});

const page = usePage();
const user = computed(() => page.props.auth.user)

const isSubmitting = ref(false);
const alertMessage = ref(null);
const isError = ref(false)
const isSuccess = ref(false)

const { jobClassifications, loadJobClassifications } = useJobClassifications();


// Untuk mengelola data formulir dan menangani validasi atau error.
const form = useForm({
    job_classification: props.personal?.job_classification_id || null,
})

// Fungsi untuk mengirimkan data formulir ke server
// Untuk memperbarui deskripsi pribadi pengguna di database
const submit = () => {
    isSubmitting.value = true;

    form.put(route("personal.classification.update", props.personal.slug), {
        onSuccess: (response) => {
            isSubmitting.value = false;
            isSuccess.value = true;
            alertMessage.value = response.message || "Bidang Pekerjaan berhasil diperbarui.";
        },
        onError: (errors) => {
            isSubmitting.value = false;
            isError.value = true;

            if (errors.notFound) {
                alertMessage.value = errors.notFound;
            } else if (errors.dbError) {
                alertMessage.value = errors.dbError;
            } else if (errors.generalError) {
                alertMessage.value = errors.generalError;
            } else {
                isError.value = false
            }
        },
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
};

onMounted(() => {
    loadJobClassifications();
});
</script>

<template>
    <Drawer v-if="user?.type === 'personal'" drawer-name="job_classification">

        <!-- Komponen Alert untuk menampilkan pesan sukses atau error.-->
        <!-- Untuk memberi umpan balik kepada pengguna tentang status operasi -->

        <!-- Alert Success -->
        <Alert
            v-if="isSuccess"
            :message="alertMessage"
            type="success"
            class="alert-success"
            :closable="true"
            @close="isSuccess = false"
        />

        <!-- Alert Error -->
        <Alert
            v-if="isError"
            :message="alertMessage"
            type="error"
            class="alert-error"
            :closable="true"
            @close="isError = false"
        />

        <h1 class="text-2xl font-semibold">Bidang Pekerjaan</h1>

        <!-- Template untuk formulir yang mengedit deskripsi pribadi pengguna -->
        <!-- Agar pengguna dapat mengubah deskripsi pribadi mereka -->
        <form @submit.prevent="submit" class="space-y-6 mt-5">
            <FormSelect
                id="job_classification"
                v-model="form.job_classification"
                label="Klasifikasi Bidang Pekerjaan"
                :options="jobClassifications"
                :error="form.errors.job_classification"
                required
                :disable="form.processing"
            />

            <div class="flex flex-row gap-2">
                <ButtonSubmit
                    class=""
                    :is-submitting="isSubmitting"
                    :default-text="'Ubah Bidang Pekerjaan'"
                    :loading-text="'Sedang Mengubah Bidang Pekerjaan...'"
                />

                <button type="button" v-if="form.processing" :disabled="form.processing" class="cursor-pointer font-semibold btn btn-outline w-32 text-second">
                    Batal
                </button>
                <label v-else for="job_classification" aria-label="close sidebar"
                       class="drawer-overlay cursor-pointer font-semibold btn btn-outline w-32 text-second">
                    Batal
                </label>
            </div>
        </form>

    </Drawer>

    <div>
        <div>
            <h1 class="text-xl font-semibold">Bidang Pekerjaan</h1>
        </div>
        <div class="relative border p-4 mt-4 rounded-md">
            <div>
                <p>
                    {{ personal.job_classification_name }}
                </p>
            </div>
            <div v-if="user?.type === 'personal'" class="absolute top-4 right-4 drawer-content">
                <label class="cursor-pointer" for="job_classification">
                    <Pencil :size="20"/>
                </label>
            </div>
        </div>
    </div>

</template>

<style scoped>

</style>
