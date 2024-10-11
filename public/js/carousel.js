// public/js/carousel.js

// Índice del slide actual, inicializado en 0
let slideIndex = 0;
// Obtiene todos los elementos con la clase "carousel-slide" (diapositivas del carrusel)
const slides = document.getElementsByClassName("carousel-slide");
// Obtiene todos los elementos con la clase "dot" (puntos indicadores del carrusel)
const dots = document.getElementsByClassName("dot");
// Variable para almacenar el temporizador del carrusel
let timer;

// Función para mostrar los slides
function showSlides() {
    // Si no hay slides, la función no hace nada
    if (slides.length === 0) return;

    // Oculta todas las diapositivas
    for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }

    // Incrementa el índice del slide actual
    slideIndex++;
    // Si el índice excede la cantidad de slides, se reinicia a 1
    if (slideIndex > slides.length) { 
        slideIndex = 1;
    }

    // Muestra el slide correspondiente
    slides[slideIndex - 1].style.display = "block";

    // Actualiza el estado de los puntos (dots) para mostrar cuál es el slide activo
    for (let i = 0; i < dots.length; i++) {
        // Remueve la clase "active" de todos los puntos
        dots[i].className = dots[i].className.replace(" active", "");
    }
    // Añade la clase "active" al punto correspondiente al slide actual
    dots[slideIndex - 1].className += " active";

    // Reinicia el temporizador para pasar al siguiente slide automáticamente
    resetTimer();
}

// Función para mostrar el slide anterior
function prevSlide() {
    // Reduce el índice de slide en 2 (para retroceder)
    slideIndex -= 2;
    // Si el índice es menor que 0, lo ajusta al último slide
    if (slideIndex < 0) { 
        slideIndex = slides.length - 1; 
    }
    // Llama a la función para mostrar el slide actualizado
    showSlides();
}

// Función para mostrar el siguiente slide
function nextSlide() {
    // Simplemente muestra el siguiente slide
    showSlides();
}

// Función para mostrar un slide específico
function currentSlide(n) {
    // Ajusta el índice al valor específico menos 1 (porque el índice empieza en 0)
    slideIndex = n - 1;
    // Llama a la función para mostrar el slide correspondiente
    showSlides();
}

// Función para reiniciar el temporizador del carrusel
function resetTimer() {
    // Limpia el temporizador actual
    clearTimeout(timer);
    // Establece un nuevo temporizador para llamar a `showSlides` después de 3 segundos (3000 ms)
    timer = setTimeout(showSlides, 3000);
}

// Código que se ejecuta una vez que el documento se ha cargado completamente
document.addEventListener("DOMContentLoaded", function() {
    // Si hay al menos un slide, muestra el primero
    if (slides.length > 0) {
        slides[0].style.display = "block";
        // Si hay puntos, marca el primero como activo
        if (dots.length > 0) {
            dots[0].className += " active"; // Marca el primer punto como activo
        }
    }
    // Inicia el temporizador del carrusel
    resetTimer();
});
