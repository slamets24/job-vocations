import { ref, watch } from 'vue';

/**
 * useDebounce Hook
 * @param {Ref} value - Reactive reference value to debounce
 * @param {Number} delay - Debounce delay in milliseconds
 * @returns {Ref} - Debounced value
 */
export default function useDebounce(value, delay = 300) {
    const debouncedValue = ref(value.value);

    let timeout;

    watch(value, (newValue) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            debouncedValue.value = newValue;
        }, delay);
    });

    return debouncedValue;
}
