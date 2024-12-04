<script setup>

import { Pencil } from "lucide-vue-next";
import FormInputFile from "@/Components/FormInputFile.vue";
import Alert from "@/Components/Alert.vue";
import ButtonSubmit from "@/Components/ButtonSubmit.vue";
import Drawer from "@/Components/Drawer.vue";
import { computed, ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";


const page = usePage();
const user = computed(() => page.props.auth.user)

const props = defineProps({
    personal: {
        type: Object,
        required: true
    }
})

const isSubmitting = ref(false);
const alertMessage = ref(null);
const isError = ref(false)
const isSuccess = ref(false)

// Referensi untuk FormInputFile
const fileInputRef = ref(null);

const resetForm = () => {
    form.reset(); // Reset data di form
    if (fileInputRef.value) {
        fileInputRef.value.clearFile(); // Panggil metode clearFile di FormInputFile
    }
};

// Untuk mengelola data formulir dan menangani validasi atau error.
const form = useForm({
    _method: 'PUT',
    imageProfile: null,
}, {
    forceFormData: true
});

// Fungsi untuk mengirimkan data formulir ke server
// Untuk memperbarui photo profile pengguna di database
const submit = () => {
    isSubmitting.value = true;

    form.post(route("personal.photo.update", props.personal.slug), {
        _method: 'PUT',

        onSuccess: (response) => {
            resetForm();
            isSubmitting.value = false;
            isSuccess.value = true;
            alertMessage.value = response.message || "Photo berhasil diperbarui.";
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

    <!--Muncul ketika yang login adalah peronal-->
    <Drawer v-if="user?.type === 'personal'" drawer-name="image">

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

        <h1 class="text-2xl font-semibold">Foto Profil</h1>

        <!-- Template untuk formulir yang mengedit photo profile pengguna -->
        <!-- Agar pengguna dapat mengubah photo profile mereka -->
        <form @submit.prevent="submit" class="space-y-6 mt-5">
            <FormInputFile
                id="imageProfile"
                label="Upload Profil"
                accept="image/*"
                help-text="Pastikan file yang diupload adalah gambar"
                v-model="form.imageProfile"
                :error="form.errors.imageProfile"
                ref="fileInputRef"
                :disable="form.processing"
                required
            />


            <div class="flex flex-row gap-2">
                <ButtonSubmit
                    class=""
                    :is-submitting="isSubmitting"
                    :default-text="'Ubah Foto'"
                    :loading-text="'Sedang Mengubah Foto...'"
                />

                <button type="button" v-if="form.processing" :disabled="form.processing"
                        class="cursor-pointer font-semibold btn btn-outline w-32 text-second">
                    Batal
                </button>
                <label v-else for="image" aria-label="close sidebar"
                       class="drawer-overlay cursor-pointer font-semibold btn btn-outline w-32 text-second">
                    Batal
                </label>
            </div>
        </form>

    </Drawer>


    <div class="flex">
        <label for="my-drawer-7" class="relative group cursor-pointer">
            <div class="relative w-32 h-32">
                <div
                    class="absolute inset-0 rounded-full bg-gradient-to-r bg-second p-[2px]"
                >
                    <div
                        class="w-full h-full rounded-full overflow-hidden bg-black"
                    >
                        <img
                            :src="personal.image_profile"
                            alt="Photo Profile"
                            class="w-full h-full object-cover"
                        />
                    </div>
                </div>
                <div v-if="user?.type === 'personal'"
                    class="absolute -top-1 -right-1 w-8 h-8 bg-second rounded-full shadow-lg transition-opacity duration-200 flex items-center justify-center text-white drawer-content">
                    <label class="cursor-pointer" for="image">
                        <Pencil class="w-4 h-4"/>
                    </label>
                </div>
            </div>
        </label>
    </div>
</template>

<style scoped>

</style>
