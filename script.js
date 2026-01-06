// script.js - Scripts do site público

// Menu mobile
document.addEventListener('DOMContentLoaded', function() {
    const burger = document.querySelector('.burger');
    const nav = document.querySelector('.nav-menu');
    
    if (burger) {
        burger.addEventListener('click', () => {
            nav.classList.toggle('active');
        });
        
        // Fechar menu ao clicar em um link
        document.querySelectorAll('.nav-menu a').forEach(link => {
            link.addEventListener('click', () => {
                nav.classList.remove('active');
            });
        });
    }
});

// Carousel
let currentSlide = 0;
const slides = document.querySelectorAll('.carousel-item');
const dots = document.querySelectorAll('.dot');

function showSlide(n) {
    if (slides.length === 0) return;
    
    if (n >= slides.length) {
        currentSlide = 0;
    }
    if (n < 0) {
        currentSlide = slides.length - 1;
    }
    
    slides.forEach(slide => {
        slide.classList.remove('active');
    });
    
    dots.forEach(dot => {
        dot.classList.remove('active');
    });
    
    if (slides[currentSlide]) {
        slides[currentSlide].classList.add('active');
    }
    if (dots[currentSlide]) {
        dots[currentSlide].classList.add('active');
    }
}

function moveSlide(n) {
    currentSlide += n;
    showSlide(currentSlide);
}

function currentSlideSet(n) {
    currentSlide = n;
    showSlide(currentSlide);
}

// Auto play carousel
if (slides.length > 0) {
    setInterval(() => {
        currentSlide++;
        showSlide(currentSlide);
    }, 5000);
}

// Scroll suave para links âncora
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});