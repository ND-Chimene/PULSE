/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./assets/**/*.js",
        "./templates/**/*.html.twig"
    ],
    theme: {
        extend: {
            colors: {
                "primary-red": "#971313",
                "secondary-red": "#AA4141",
                "primary-black": "#2C2C2C",
                "secondary-black": "#4F4F59",
                "primary-white": "#F7F7F7",
                "secondary-white": "#E4E2E3",
                "tertiary-white": "#D7D5D6",
            },
            backgroundImage: {
                'red-to-black': 'linear-gradient(to bottom, #AA4141 80%, #2C2C2C 100%)',
                'black-to-red': 'linear-gradient(to bottom, #2C2C2C 5%, #AA4141 100%)',
            },
        },
    },
    plugins: [],
}