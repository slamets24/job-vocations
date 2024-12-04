<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DeleteUserForm from "./Partials/DeleteUserForm.vue";
import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm.vue";
import { Head } from "@inertiajs/vue3";
import DashboardLayouts from "@/Layouts/DashboardLayouts.vue";

defineProps({
  mustVerifyEmail: {
    type: Boolean,
  },
  status: {
    type: String,
  },
});
</script>

<template>
  <Head title="Setting" />

  <DashboardLayouts>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Profile</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
        <div class="p-4 bg-white border-2 shadow sm:rounded-lg sm:p-8">
          <UpdateProfileInformationForm
            :must-verify-email="mustVerifyEmail"
            :status="status"
            class="max-w-xl"
          />
        </div>

        <div class="p-4 bg-white border-2 shadow sm:rounded-lg sm:p-8">
          <UpdatePasswordForm class="max-w-xl" />
        </div>

        <div class="p-4 bg-white shadow border-2 sm:rounded-lg sm:p-8">
          <DeleteUserForm class="max-w-xl" />
        </div>
      </div>
    </div>
  </DashboardLayouts>
</template>
