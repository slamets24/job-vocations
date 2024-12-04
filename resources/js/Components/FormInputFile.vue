<script setup>
import RequiredField from "@/Components/RequiredField.vue";
import InputError from "@/Components/InputError.vue";
import { ref, watch } from 'vue';

const props = defineProps({
    id: {
        type: String,
        required: true
    },
    label: {
        type: String,
        required: true
    },
    helpText: {
        type: String,
        default: ""
    },
    modelValue: {
        type: [String, File, null],
        default: null
    },
    error: {
        type: String,
        default: ""
    },
    required: {
        type: Boolean,
        default: false
    },
    accept: {
        type: String,
        default: ""
    },
});

const emit = defineEmits(['update:modelValue', 'error']);

// Refs
const fileInput = ref(null);
const selectedFileName = ref('');
const localError = ref('');

// Methods
// const handleFileUpload = (event) => {
//     const file = event.target.files[0];
//
//     if (!file) {
//         clearFile();
//         return;
//     }
//
//     // Update file name display
//     selectedFileName.value = file.name;
//
//     // Emit the file object
//     emit('update:modelValue', file);
// };

const handleFileUpload = (event) => {
    const file = event.target.files[0];

    if (!file) {
        clearFile();
        return;
    }

    selectedFileName.value = file.name;

    emit('update:modelValue', file);
    emit('input', file);
};

const clearFile = () => {
    if (fileInput.value) {
        fileInput.value.value = '';
    }
    selectedFileName.value = '';
    localError.value = '';
    emit('update:modelValue', null);
};

defineExpose({
    clearFile
});

// Watch for external error changes
watch(() => props.error, (newError) => {
    localError.value = newError;
});
</script>

<template>
    <div class="form-control w-full max-w-sm space-y-2">
        <label :for="id" class="block text-sm font-semibold text-gray-700">
            {{ label }}
            <RequiredField v-if="required"/> <span v-show="helpText" class="font-medium">({{ helpText }})</span>
        </label>

        <div class="relative">
            <input
                type="file"
                ref="fileInput"
                :id="id"
                :accept="accept"
                @change="handleFileUpload"
                v-bind="$attrs"
                :required="required"
                class="file-input file-input-bordered w-full max-w-xs"
            />

            <button
                v-if="selectedFileName"
                @click="clearFile"
                type="button"
                class="absolute right-2 top-1/2 -translate-y-1/2">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- File name display -->
        <div v-if="selectedFileName" class="text-sm mt-1 text-gray-600">
            {{ selectedFileName }}
        </div>

        <!-- Error display -->
        <InputError :message="localError || error"/>
    </div>
</template>
