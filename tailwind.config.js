/** @type {import('tailwindcss').Config} */

export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
      "background": "var(--background)",
      "base": "var(--base)",
      "text_dark": "var(--text_dark)",
      "text_light": "var(--text_light)",
      "button": "var(--button)",
      "button2": "var(--button2)",
      "button3": "var(--button3)",
      "button4": "var(--button4)",
      "line": "var(--line)",
      "selection": "var(--selection)"
      },
      zIndex: {
        "120": "120"
      }
    },
  },
  plugins: [],
}