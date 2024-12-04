<script setup>
import RequiredField from "@/Components/RequiredField.vue";
import InputError from "@/Components/InputError.vue";

defineProps({
    type: {
      type: String,
      required: false,
      default: 'text'
    },
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
        type: [String, Number],
        default: ""
    },
    error: {
        type: String,
        default: ""
    },
    required: {
        type: Boolean,
        default: false
    }
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div class="space-y-2">
        <label :for="id" class="block text-sm font-semibold text-gray-700">
            {{ label }}
            <RequiredField v-if="required"/> <span v-show="helpText" class="font-medium">({{ helpText }})</span>
        </label>
        <input
            :type="type"
            :id="id"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            v-bind="$attrs"
            :required="required"
            class="bg-[#F1F1F5]"
            :class="{ 'w-full': !['checkbox', 'radio'].includes(type), 'input': !['checkbox', 'radio'].includes(type), 'checkbox': type === 'checkbox' }"
        />
        <InputError :message="error"/>
    </div>
</template>

<style scoped>

</style>
