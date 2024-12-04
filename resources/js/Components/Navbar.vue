<script setup>
import { Link, router, usePage } from "@inertiajs/vue3";
import { computed, onMounted, ref } from "vue";

const data = defineProps({
    picture:{
        type: String
    }
})

const { props } = usePage();
const user = props.auth.user; // Ambil data pengguna
const appName = computed(() => props.appName);
const userType = user?.type; // Ambil tipe pengguna
const profileImage = computed(() => props.auth.profile_image); // untuk memanggil photo profile

function isCurrentRoute(routes) {
  return routes.includes(route().current());
}
</script>

<template>
  <nav class="sticky top-0 z-50">
    <div class="mx-auto lg:px-6 xl:px-0 navbar bg-base-100 max-w-7xl">

      <!-- Navigation Mobile -->
      <div class="dropdown">
        <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
               stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h8m-8 6h16"/>
          </svg>
        </div>

        <!-- Menu untuk Company -->
        <ul v-if="userType === 'company'" tabindex="0"
            class="menu menu-sm dropdown-content bg-base-100 rounded-box z-[1] mt-3 w-52 p-2 shadow">
          <li>
            <Link :class="{ 'bg-second text-white': route().current('company.dashboard') }"
                  :href="route('company.dashboard')">Dashboard
            </Link>
          </li>
          <li>
            <Link :href="route('jobs.index')" :class="{ 'bg-second text-white': isCurrentRoute(['jobs.index', 'jobs.edit', 'show.applicants', 'jobs.show', 'applications.detailApplicant', 'cbt.create', 'cbt.edit']) }">Lowongan
              Diposting
            </Link>
          </li>
        </ul>

        <!-- Menu untuk pengguna yang tidak login atau pengguna sebagai personal -->
        <ul v-if="userType !== 'company'" tabindex="0"
            class="menu menu-sm dropdown-content bg-base-100 rounded-box z-[1] mt-3 w-52 p-2 shadow">
          <li>
            <Link :href="route('home')" :class="{ 'bg-second text-white': route().current('home') }">
              Cari Lowongan
            </Link>
          </li>

          <!--tampilkan ketika sudah login saja dan sebagai personal-->
          <li v-if="userType === 'personal'">
            <Link :href="route('applications.proposed')"
                  :class="{ 'bg-second text-white': route().current('applications.proposed') }">Lamaran Saya
            </Link>
          </li>

          <li>
            <Link :href="route('companies.index')"
                  :class="{ 'bg-second text-white': isCurrentRoute(['companies.index', 'companies.show']) }"
            >Jelajahi Perusahaan
            </Link>
          </li>

          <!--Tampilkan jika pengguna belum login-->
          <template v-if="!userType">
            <li class="mt-3">
              <Link :href="route('register')"
                    class="btn btn-outline btn-sm outline-2 border-second hover:border-second text-second">
                Daftar
              </Link>
            </li>

            <li class="mt-2">
              <Link :href="route('login')" class="btn btn-sm bg-second text-white">
                Masuk
              </Link>
            </li>
          </template>
        </ul>
      </div>

      <!-- Navigation Desktop -->
      <div class="flex-1">
        <Link href="/" class="text-xl normal-case btn btn-ghost">
          {{ appName }}
        </Link>

        <!-- Menu untuk Company -->
        <ul v-if="userType === 'company'" class="items-center hidden px-6 menu menu-horizontal lg:flex">
          <li>
            <Link :class="{ 'bg-second text-white': route().current('company.dashboard') }"
                  :href="route('company.dashboard')">
              Dashboard
            </Link>
          </li>

          <li>
            <Link :href="route('jobs.index')" :class="{ 'bg-second text-white': isCurrentRoute(['jobs.index', 'jobs.edit', 'show.applicants', 'jobs.show', 'applications.detailApplicant', 'cbt.create', 'cbt.edit']) }">
              Lowongan Diposting
            </Link>
          </li>

        </ul>

        <!-- Menu untuk pengguna yang tidak login atau pengguna sebagai personal -->
        <ul v-if="userType !== 'company'" class="items-center hidden px-6 menu menu-horizontal lg:flex">
          <li>
            <Link :href="route('home')" :class="{ 'bg-second text-white': route().current('home') }">
              Cari Lowongan
            </Link>
          </li>

          <li v-if="userType === 'personal'">
            <Link :href="route('applications.proposed')" :class="{ 'bg-second text-white': route().current('applications.proposed') }">
              Lamaran Saya
            </Link>
          </li>

          <li>
            <Link :href="route('companies.index')"
                  :class="{ 'bg-second text-white': isCurrentRoute(['companies.index', 'companies.show', 'companies.search']) }">
              Jelajahi Perusahaan
            </Link>
          </li>
        </ul>
      </div>

      <div class="flex gap-2">

        <!-- Tampilkan tombol button jika sudah login sebagai company -->
        <Link
            v-if="userType === 'company'"
            :href="route('jobs.create')"
            class="hidden md:flex items-center text-second font-bold btn btn-sm btn-outline hover:bg-second hover:border-second hover:text-white group">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
               class="group-hover:fill-white fill-second">
            <path d="M19 11h-6V5h-2v6H5v2h6v6h2v-6h6z"></path>
          </svg>
          Posting Lowongan
        </Link>

        <!-- Navigation yang ada photo profile nya jika sudah login sebagai personal atau company -->
        <div v-if="userType" class="dropdown dropdown-end">

          <!-- Avatar -->
          <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
            <div class="w-10 rounded-full">
              <img alt="Gambar Company"
                   :src="profileImage"/>
            </div>
          </div>

          <ul tabindex="0"
              class="menu menu-sm dropdown-content bg-base-100 rounded-box z-[1] mt-3 w-52 p-2 shadow space-y-2">

            <!-- Untuk tipe pengguna Company: Menampilkan link Posting Lowongan -->
            <li class="md:hidden" v-if="userType === 'company'">
              <Link
                  :href="route('jobs.create')"
                  class="items-center text-second font-bold btn btn-sm btn-outline hover:bg-second hover:text-white group">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                     class="group-hover:fill-white fill-second">
                  <path d="M19 11h-6V5h-2v6H5v2h6v6h2v-6h6z"></path>
                </svg>
                Posting Lowongan
              </Link>
            </li>

            <!-- Profil dan Setting untuk semua pengguna -->
            <li>
              <Link
                  :href="userType == 'company' ? route('company.profile.index') : route('personal.index')">
                Profile
              </Link>

            </li>

            <li>
              <Link href="/settings " class="">Setting</Link>
            </li>

            <!-- Logout -->
            <li>
              <Link
                  :href="route('logout')"
                  method="post"
                  as="button"
                  @click.prevent
              >
                Logout
              </Link>
            </li>
          </ul>
        </div>

        <!-- Tampilkan tombol masuk dan daftar jika belum login -->
        <div v-else class="flex gap-3 hidden lg:flex xl:px-3">
          <Link :href="route('login')"
                class="btn btn-outline border-second hover:border-second text-second hover:bg-second hover:text-white group">
            Masuk
          </Link>
          <Link :href="route('register')" class="btn bg-second text-white group">
            Daftar
          </Link>
        </div>
      </div>
    </div>
  </nav>
</template>

<style scoped>
/* Tambahkan gaya untuk elemen aktif */
.active {
  font-weight: bold;
  color: #3490dc; /* Warna biru sesuai tema Tailwind */
}
</style>
