import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import daisyui from "daisyui"

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    daisyui: {
        darkTheme: "light", // force light theme
        themes: ["light"] // hanya gunakan tema light
    },
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'main': '#4D6EFF',
                'second': '#0AAB7C',
                'three': '#7B92B2',
                'four': '#363955',
                'five': '#f5f6e6',
                'six': '#e8e8e8'
              },
        },
    },

    plugins: [forms, daisyui],
};
