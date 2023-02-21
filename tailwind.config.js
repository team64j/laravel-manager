/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',

  content: [
    './resources/views/*/*.blade.php',
    './resources/views/*.blade.php',
    './resources/js/**',
    './src/Layouts/*.php'
  ],

  // safelist: [
  //   {
  //     pattern: /p-\d|p\w-\d|m-\d/
  //   }
  // ],

  theme: {
    extend: {
      fontFamily: {
        sans: '-apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji"'
      },
      colors: {
        transparent: 'transparent',
        current: 'currentColor',
        white: '#ffffff',
        cms: {
          50: '#b9c1c9',
          100: '#959ca4',
          200: '#797f86',
          300: '#6d7279',
          400: '#53575d',
          500: '#414449',
          600: '#3f4550',
          700: '#282c34',
          800: '#202329',
          900: '#1a1c21',
        },
      },
    }
  },

  plugins: [
    require('@tailwindcss/forms')
  ]
}
