/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./assets/**/*.js",
        "./templates/**/*.html.twig"
    ],
    theme: {
        extend: {
            colors: {
                "primary-red":"#971313",
                "secondary-red":"#AA4141",
                "primary-black":"#2C2C2C",
                "secondary-black":"#4F4F59",
                "primary-white":"#F7F7F7",
                "secondary-white":"#E4E2E3",
                "tertiary-white":"#D7D5D6",

            },
            fontFamily: {
            },
        },
    },
    plugins: [],
}