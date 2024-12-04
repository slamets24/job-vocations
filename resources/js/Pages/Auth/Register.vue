<script setup>
import { computed, ref } from "vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import FormInput from "@/Components/FormInput.vue";
import ButtonSubmit from "@/Components/ButtonSubmit.vue";
import BlockUi from "@/Components/BlockUi.vue";

const props = defineProps({
    errors: {
        type: Object,
    },
})

const isSubmitting = ref(false)

const form = useForm({
    email: "",
    password: "",
    password_confirmation: "",
    type: "personal",
    is_aggreed: false
});

const defaultText = computed(() => {
    return `Daftar Akun ${form.type === 'personal' ? 'Personal' : 'Perusahaan'}`;
})

const loadingText = computed(() => {
    return `Sedang Daftar Akun ${form.type === 'personal' ? 'Personal' : 'Perusahaan'}...`;
})

// Fungsi untuk mengubah tipe pengguna
const toggleUserType = (type) => {
    form.type = type;
};

const submit = () => {
    isSubmitting.value = true;
    form.post(route("register"), {
        onSuccess: () => {
            isSubmitting.value = false;
            form.reset();
        },
        onError: (errors) => {
            isSubmitting.value = false;
        },
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
};
</script>

<template>
    <Head title="Daftar"/>
    <GuestLayout>
        <BlockUi message="Sedang Daftar" :is-loading="form.processing"/>

        <div class="flex flex-col items-center justify-center px-4">
            <h1 class="mb-6 text-2xl font-bold">Daftar Akun</h1>
            <div class="flex mb-4 space-x-4">
                <button
                    :class="{
            'bg-second text-white': form.type === 'personal',
            'bg-gray-200': form.type !== 'personal',
          }"
                    @click="toggleUserType('personal')"
                    class="px-4 py-2 rounded"
                >
                    Personal
                </button>
                <button
                    :class="{
            'bg-second text-white': form.type === 'company',
            'bg-gray-200': form.type !== 'company',
          }"
                    @click="toggleUserType('company')"
                    class="px-4 py-2 rounded"
                >
                    Perusahaan
                </button>
            </div>

            <form @submit.prevent="submit" class="w-full max-w-md">
                <div class="mt-4 space-y-5">
                    <FormInput
                        id="email"
                        type="email"
                        v-model="form.email"
                        label="Email"
                        :error="form.errors.email"
                        required
                        :disabled="form.processing"
                    />

                    <FormInput
                        id="password"
                        type="password"
                        v-model="form.password"
                        label="Password"
                        :error="form.errors.password"
                        required
                        :disabled="form.processing"
                    />

                    <FormInput
                        id="password_confirmation"
                        type="password"
                        v-model="form.password_confirmation"
                        label="Konfirmasi Password"
                        :error="form.errors.password_confirmation"
                        required
                        :disabled="form.processing"
                    />

                    <FormInput
                        id="is_aggreed"
                        v-model:checked="form.is_aggreed"
                        @change="form.is_aggreed = $event.target.checked"
                        type="checkbox"
                        label="Dengan ini saya menyetujui (Persyaratan & Ketentuan) dan (Keamanan & Privasi)?"
                        :error="form.errors.is_aggreed"
                        :disabled="form.processing"
                    />

                    <div class="flex gap-2">
                        <Link :href="route('terms')" class="text-second underline text-sm">Persyaratan & ketentuan</Link>
                        <Link :href="route('security-privacy')" class="text-second underline text-sm">Keamanan & Privasi</Link>
                    </div>

                    <Link
                        :href="route('login')"
                        class="text-sm text-gray-600 underline hover:text-gray-900"
                    >
                        Sudah punya akun?
                    </Link>

                    <ButtonSubmit
                        class="w-full"
                        :is-submitting="isSubmitting"
                        :default-text="defaultText"
                        :loading-text="loadingText"
                    />
                </div>
            </form>
        </div>
    </GuestLayout>
</template>

<style scoped>
</style>
