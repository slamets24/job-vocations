<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import Alert from "@/Components/Alert.vue";
import { ref } from "vue";
import FormInput from "@/Components/FormInput.vue";
import ButtonSubmit from "@/Components/ButtonSubmit.vue";
import BlockUi from "@/Components/BlockUi.vue";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    errors: {
        type: Object,
    }
});

const page = usePage();
const isSubmitting = ref(false)

const success = ref(page.props.flash.success || null);

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    isSubmitting.value = true;
    form.post(route("login"), {
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
    <Head title="Log in"/>
    <GuestLayout>
        <BlockUi message="Sedang Login" :is-loading="form.processing" />

        <div class="mb-4 space-y-4 text-gray-600">
            <h1 class="text-xl font-bold">Login</h1>

            <!-- Jika user memaksa masuk ke suatu halaman tanpa login, maka tampilkan pesan -->
            <Alert
                v-if="errors.unauthorized"
                type="warning"
                class="alert-warning"
                :closable="true"
                @close="errors.unauthorized = null"
                :message="errors.unauthorized"/>

            <p class="text-sm">Selamat Datang Kembali</p>
        </div>

        <Alert
            v-if="success"
            :message="success"
            class="my-5 alert-success"
            type="success"
            :closable="true"
            @close="success = null"/>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <FormInput
                id="email"
                type="email"
                v-model="form.email"
                label="Email"
                :error="form.errors.email"
                required
                autofocus
                :disabled="form.processing"
                autocomplete="username"
            />

            <div class="mt-4">
                <FormInput
                    id="password"
                    type="password"
                    v-model="form.password"
                    label="Password"
                    :error="form.errors.password"
                    :disabled="form.processing"
                    required
                />
            </div>

            <div class="mt-4 flex justify-between">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember"/>
                    <span class="text-sm text-gray-600 ms-2">Remember me</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-gray-600 underline rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Lupa password?
                </Link>
            </div>

            <div class="mt-4">
                <ButtonSubmit
                    class="w-full"
                    :is-submitting="isSubmitting"
                    :default-text="'Log in'"
                    :loading-text="'Sedang Login...'"
                />
            </div>
        </form>
        <div
            class="flex items-center justify-center w-full mt-4 md:mt-6 md:justify-start"
        >
            <p class="flex items-center text-xs text-[#1a1a1a]">
                Belum Punya Akun?
                <Link
                    class="ml-1 text-xs font-bold text-[#0AAB7C] text-tertiary-violet-50"
                    :href="route('register')"
                >Daftar
                </Link
                >
            </p>
        </div>
    </GuestLayout>
</template>

<style scoped>
</style>
