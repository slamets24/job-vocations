<script setup>
import RequiredField from "@/Components/RequiredField.vue";
import InputError from "@/Components/InputError.vue";

defineProps({
    id: {
        type: String,
        required: true
    },
    label: {
        type: String,
        required: true
    },
    modelValue: {
        type: [String, Number, null],
        default: null
    },
    options: {
        type: Array,
        required: true
    },
    error: {
        type: String,
        default: ""
    },
    required: {
        type: Boolean,
        default: false
    },
    placeholder: {
        type: String,
        default: ""
    }
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div class="space-y-2">
        <label :for="id" class="block text-sm font-semibold text-gray-700">
            {{ label }}
            <RequiredField v-if="required" />
        </label>
        <select
            :id="id"
            :value="modelValue"
            @change="$emit('update:modelValue', $event.target.value)"
            v-bind="$attrs"
            :required="required"
            class="w-full bg-[#F1F1F5] input"
        >
            <option value="" disabled>{{ placeholder || `Pilih ${label}` }}</option>
            <option
                v-for="option in options"
                :key="option.value ?? option.code ?? option.id"
                :value="option.value ?? option.code ?? option.id"
            >
                {{ option.label ?? option.name }}
            </option>
        </select>
        <InputError :message="error" />
    </div>
</template>

<style scoped>

</style>
