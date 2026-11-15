import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Figtree', ...defaultTheme.fontFamily.sans],
                display: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                agro: {
                    canvas: '#FAF9F6',
                    surface: '#FFFFFF',
                    'surface-low': '#F4F3F1',
                    'surface-container': '#EFEEEB',
                    'on-surface': '#1A1C1A',
                    'on-surface-variant': '#41493E',
                    outline: '#717A6D',
                    'outline-variant': '#E0DED7',
                    primary: '#1B5E20',
                    'primary-dark': '#00450D',
                    'primary-active': '#0D3311',
                    'primary-light': '#E8F5E9',
                    secondary: '#5D4037',
                    'secondary-light': '#FFDBD0',
                    tertiary: '#1565C0',
                    'tertiary-dark': '#0D47A1',
                    'tertiary-light': '#E3F2FD',
                    success: '#2E7D32',
                    warning: '#E65100',
                    'warning-light': '#FFF3E0',
                    danger: '#C62828',
                    'danger-light': '#FFEBEE',
                },
            },
        },
    },

    plugins: [forms, typography],
};
