<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import BlockUi from "@/Components/BlockUi.vue";

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const isLoading = ref(false);

const submit = () => {
    isLoading.value = true;
    form.post(route('password.email'), {
        onFinish: () => {
            form.reset();
            isLoading.value = false;
        },
        onError: () => {
            isLoading.value = false;
        }
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password"/>

        <BlockUi message="Mengirimkan tautan reset kata sandi" :is-loading="form.processing" />

        <div class="relative">
            <div class="mb-4 text-sm text-gray-600">
                Lupa kata sandi Anda? Tidak masalah. Cukup beri tahu kami alamat email Anda, dan kami akan mengirimkan
                tautan untuk mereset kata sandi sehingga Anda dapat memilih yang baru.
            </div>

            <div
                v-if="status"
                class="mb-4 text-sm font-medium text-green-600"
            >
                {{ status }}
            </div>

            <form
                @submit.prevent="submit"
                :class="{ 'opacity-50 pointer-events-none': isLoading }"
            >
                <div>
                    <InputLabel for="email" value="Email"/>

                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                        required
                        autofocus
                        :disabled="form.processing"
                        autocomplete="username"
                    />

                    <InputError class="mt-2" :message="form.errors.email"/>
                </div>

                <div class="mt-4 flex items-center justify-end">
                    <PrimaryButton
                        class="btn-md"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Email Password Reset Link
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>

<style scoped>
</style>
