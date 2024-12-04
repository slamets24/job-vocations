<script setup>
import { Pencil } from "lucide-vue-next";
import Drawer from "@/Components/Drawer.vue";
import Alert from "@/Components/Alert.vue";
import { computed, ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import FormTextarea from "@/Components/FormTextarea.vue";
import ButtonSubmit from "@/Components/ButtonSubmit.vue";
import FormInput from "@/Components/FormInput.vue";

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

// Untuk mengelola data formulir dan menangani validasi atau error.
const form = useForm({
    salary: props.personal?.salary || "",
})

// Fungsi untuk mengirimkan data formulir ke server
// Untuk memperbarui deskripsi pribadi pengguna di database
const submit = () => {
    isSubmitting.value = true;

    form.put(route("personal.salary.update", props.personal.slug), {
        onSuccess: (response) => {
            isSubmitting.value = false;
            isSuccess.value = true;
            alertMessage.value = response.message || "Harapan Gaji berhasil diperbarui.";
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
</script>

<template>
    <Drawer v-if="user?.type === 'personal'" drawer-name="salary">

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

        <h1 class="text-2xl font-semibold">Harapan Gaji</h1>

        <!-- Template untuk formulir yang mengedit deskripsi pribadi pengguna -->
        <!-- Agar pengguna dapat mengubah deskripsi pribadi mereka -->
        <form @submit.prevent="submit" class="space-y-6 mt-5">
            <FormInput
                type="number"
                id="salary"
                v-model="form.salary"
                label="Gaji"
                placeholder="Contoh: 3000000"
                :error="form.errors.salary"
                :step="20000"
                required
                :disable="form.processing"
            />

            <div class="flex flex-row gap-2">
                <ButtonSubmit
                    class=""
                    :is-submitting="isSubmitting"
                    :default-text="'Perbarui Harapan Gaji'"
                    :loading-text="'Sedang Mengubah Harapan Gaji...'"
                />

                <button type="button" v-if="form.processing" :disabled="form.processing" class="cursor-pointer font-semibold btn btn-outline w-32 text-second">
                    Batal
                </button>
                <label v-else for="salary" aria-label="close sidebar"
                       class="drawer-overlay cursor-pointer font-semibold btn btn-outline w-32 text-second">
                    Batal
                </label>
            </div>
        </form>

    </Drawer>

    <div>
        <div>
            <h1 class="text-xl font-semibold">Harapan Gaji</h1>
        </div>
        <div class="relative border p-4 mt-4 rounded-md">
            <div>
                <p>
                    {{ personal.salary }}
                </p>
            </div>
            <div v-if="user?.type === 'personal'" class="absolute top-4 right-4 drawer-content">
                <label class="cursor-pointer" for="salary">
                    <Pencil :size="20"/>
                </label>
            </div>
        </div>
    </div>

</template>

<style scoped>

</style>
