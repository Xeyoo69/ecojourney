/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        forest: {
          50: '#f2f7f3',
          100: '#e1eee4',
          200: '#c4ddc9',
          300: '#9fc5a8',
          400: '#70a47e',
          500: '#4f8760',
          600: '#3d6e4c',
          700: '#31583d',
          800: '#294733',
          900: '#223b2b',
        },
        sand: {
          50: '#fcfaf5',
          100: '#f5efe2',
          200: '#eadfc9',
          300: '#ddc9a8',
        },
      },
      ringColor: theme => theme('colors'),
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
        display: ['Plus Jakarta Sans', 'sans-serif'],
      },
    },
  },
  plugins: [],
}