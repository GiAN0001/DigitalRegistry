import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            // --- ORIGINAL CONFIG ---
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },

            // --- ADDED ROOT DESIGN - JAZ ---


            colors: {
                primary: {
                    DEFAULT: '#1d4ed8', // Blue 700
                    hover: '#1e40af',   // Blue 800 (Optional hover)
                },
                secondary: {
                    DEFAULT: '#334155', // Slate 700
                },
            },

            // Border Radius (8px & 16px)
            borderRadius: {
                '8': '8px',
                '16': '16px',
            },

            // Spacing (Gap 16px)
            spacing: {
                '16px': '16px',
            },

            // Typography (Ag SM/medium)
            fontSize: {
                'sm-medium': ['14px', {
                    lineHeight: '20px',
                    fontWeight: '500',
                }],
            },

            // Box Shadow (SM)
            boxShadow: {
                'sm': '0 1px 2px 0 rgb(0 0 0 / 0.05)',
            },
        },
    },

    plugins: [forms],
};
