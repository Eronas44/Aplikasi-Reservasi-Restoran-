/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './**/*.{html,php,js}',
  ],
  theme: {
    extend: {
      colors: {
        bg: '#f8f1e8',
        panel: '#fff9f3',
        ink: '#201913',
        muted: '#66574b',
        line: '#eadfd4',
        accent: '#8a5d49',
        'accent-strong': '#5e392e',
      },
      fontFamily: {
        display: ['Fraunces', 'serif'],
        sans: ['Inter', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
