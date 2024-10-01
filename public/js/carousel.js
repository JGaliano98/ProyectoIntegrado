// public/js/carousel.js

let slideIndex = 0;
const slides = document.getElementsByClassName("carousel-slide");
const dots = document.getElementsByClassName("dot");
let timer;

function showSlides() {
    if (slides.length === 0) return;

    for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    slideIndex++;
    if (slideIndex > slides.length) { 
        slideIndex = 1;
    }
    slides[slideIndex - 1].style.display = "block";

    // Actualiza los puntos
    for (let i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" active", "");
    }
    dots[slideIndex - 1].className += " active";

    resetTimer();
}

function prevSlide() {
    slideIndex -= 2;
    if (slideIndex < 0) { slideIndex = slides.length - 1; }
    showSlides();
}

function nextSlide() {
    showSlides();
}

function currentSlide(n) {
    slideIndex = n - 1;
    showSlides();
}

function resetTimer() {
    clearTimeout(timer);
    timer = setTimeout(showSlides, 3000);
}

document.addEventListener("DOMContentLoaded", function() {
    if (slides.length > 0) {
        slides[0].style.display = "block";
        if (dots.length > 0) {
            dots[0].className += " active"; // Marca el primer punto como activo
        }
    }
    resetTimer();
});
