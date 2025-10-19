import defaultTheme from 'tailwindcss/defaultTheme.js';
import forms from '@tailwindcss/forms';
import animate from 'tailwindcss-animate';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.vue',
    './resources/js/**/*.js',
  ],

  safelist: [
    'bg-medic-100', 'bg-medic-200', 'bg-medic-400',
    'text-medic-100', 'text-medic-800',
    'border-medic-100', 'border-medic-400',
    'ring-medic-100', 'ring-medic-400',
    'hover:bg-medic-100', 'hover:bg-medic-400',
    'hover:border-medic-400', 'hover:text-white',
    'dark:bg-blue-500', 'dark:bg-blue-600', 'dark:bg-blue-700',
    'dark:border-blue-500', 'dark:border-blue-600', 'dark:border-blue-700',
    'dark:ring-blue-700', 'dark:text-blue-500',
  ],

  darkMode: 'class',

  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
      borderRadius: {
        lg: 'var(--radius)',
        md: 'calc(var(--radius) - 2px)',
        sm: 'calc(var(--radius) - 4px)',
      },
      colors: {
        background: 'hsl(var(--background))',
        foreground: 'hsl(var(--foreground))',

        // 🎨 Paleta pastel del proyecto
        primary: {
          200: '#d7e4ff',
          400: '#a7c3ff',
          600: '#6b96f6',
        },
        forest: {
          100: '#e3f5ee',
          200: '#c9ecdf',
          400: '#86d3b4',
        },
        mono: {
          50: '#fafbfc',
          100: '#f2f6fa',
          200: '#e5ebf3',
          300: '#d7dfeb',
          500: '#94a3b8',
          700: '#485365',
        },
        sidebar: '#e3f5ee',

        // 💙 Paleta medic existente
        medic: {
          50: '#F0F7FF',
          100: '#D6E8FB',
          200: '#A9D1F5',
          300: '#7CB9EE',
          400: '#4AA2E5',
          500: '#2F8AD4',
          600: '#236BAA',
          700: '#1A5386',
          800: '#133C62',
          900: '#0E2A44',
        },
        success: {
          300: '#6EE7B7',
          400: '#34D399',
        },
        error: {
          400: '#f03939ff',
        },
        warning: {
          300: '#FBBF24',
          400: '#F59E0B',
        },
        card: {
          DEFAULT: 'hsl(var(--card))',
          foreground: 'hsl(var(--card-foreground))',
        },
        popover: {
          DEFAULT: 'hsl(var(--popover))',
          foreground: 'hsl(var(--popover-foreground))',
        },
        secondary: {
          DEFAULT: 'hsl(var(--secondary))',
          foreground: 'hsl(var(--secondary-foreground))',
        },
        muted: {
          DEFAULT: 'hsl(var(--muted))',
          foreground: 'hsl(var(--muted-foreground))',
        },
        accent: {
          DEFAULT: 'hsl(var(--accent))',
          foreground: 'hsl(var(--accent-foreground))',
        },
        destructive: {
          DEFAULT: 'hsl(var(--destructive))',
          foreground: 'hsl(var(--destructive-foreground))',
        },
        border: 'hsl(var(--border))',
        input: 'hsl(var(--input))',
        ring: 'hsl(var(--ring))',
        chart: {
          1: 'hsl(var(--chart-1))',
          2: 'hsl(var(--chart-2))',
          3: 'hsl(var(--chart-3))',
          4: 'hsl(var(--chart-4))',
          5: 'hsl(var(--chart-5))',
        },
        sidebarTheme: {
          DEFAULT: 'hsl(var(--sidebar-background))',
          foreground: 'hsl(var(--sidebar-foreground))',
          primary: 'hsl(var(--sidebar-primary))',
          'primary-foreground': 'hsl(var(--sidebar-primary-foreground))',
          accent: 'hsl(var(--sidebar-accent))',
          'accent-foreground': 'hsl(var(--sidebar-accent-foreground))',
          border: 'hsl(var(--sidebar-border))',
          ring: 'hsl(var(--sidebar-ring))',
        },
      },
    },
  },

  plugins: [forms, animate],
};
