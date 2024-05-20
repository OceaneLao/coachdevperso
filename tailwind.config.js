/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    colors: {
      // Configure your color palette here
      pink : "#ce6a85",
      blue : "#84a59d",
      brown : "#64594E",
      white : "#fff",
      red : "#f00",
      beige : "#fbf5f4",
      green : "#0A6847",
    },
    fontFamily: {
      neuton: ['Neuton'],
      inter: ['Inter'],
    }
  },
  plugins:[],
}