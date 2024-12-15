import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './vendor/zaimea/zaimea/**/*.blade.php',
        './vendor/zaimealabs/charts/**/*.blade.php',
    ],
    safelist: [
        'ltr:origin-top-left', 'rtl:origin-top-right', 'ltr:origin-top-right', 'rtl:origin-top-left',
        'origin-top', 'start-0', 'end-0',
        'w-48', 'w-60', 'w-full', 'min-w-36', 'min-w-48', 'min-w-60'
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            minHeight: (theme) => ({
                ...theme('spacing'),
              }),
        },
    },

    plugins: [forms, typography],
};
