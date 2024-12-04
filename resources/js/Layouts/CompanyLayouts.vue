<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link, usePage } from "@inertiajs/vue3"; // Import usePage dari Inertia

// Mengambil informasi halaman saat ini
const { url } = usePage(); // Mengambil informasi tentang URL saat ini

// Menentukan judul berdasarkan URL
const isLoginRoute = url.includes("login"); // Ganti 'login' dengan path yang sesuai jika perlu
const isRegisterRoute = url.includes("register"); // Ganti 'register' dengan path yang sesuai jika perlu
</script>

<template>
  <div class="flex items-center">
    <div class="bg-[#0AAB7C] w-1/3 min-h-dvh hidden md:block">
      <div class="flex items-center justify-center h-dvh md:px-4">
        <h1 class="text-2xl font-bold text-white">
          Mari Bergabung Sebagai Perusahaan
        </h1>
      </div>
    </div>
    <div class="flex items-center justify-center w-full">
      <div class="w-full max-w-md px-4 mt-20 md:mt-0 md:px-0">
        <div class="mb-4 space-y-3">
          <h1 class="text-xl font-bold">
            <!-- Menampilkan judul berdasarkan route -->
            {{
              isLoginRoute
                ? "Sign In"
                : isRegisterRoute
                ? "Register"
                : "Welcome"
            }}
          </h1>
          <p class="text-sm text-gray-500">
            Selamat Datang di Indonesia #1 Job Portal
          </p>
        </div>
        <div>
          <slot />
        </div>
      </div>
    </div>
  </div>
</template>
