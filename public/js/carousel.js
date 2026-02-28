// Carousel functionality
let currentSlide = 0;
const totalSlides = 7;
const itemsPerView = 3;
let autoSlideInterval = null;

function updateCarousel() {
    const carousel = document.getElementById('featuresCarousel');
    const dots = document.querySelectorAll('.carousel-dot');
    const maxSlide = totalSlides - itemsPerView;

    if (carousel) {
        // Each slide is 1/7 of the wrapper, scroll by one slide width
        const scrollPercentage = currentSlide * (100 / totalSlides);
        carousel.style.transform = `translateX(-${scrollPercentage}%)`;
        console.log(`Slide ${currentSlide}, Transform: -${scrollPercentage}%`);
    }

    dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === currentSlide);
    });
}

function resetAutoSlide() {
    // Clear the existing interval
    if (autoSlideInterval) {
        clearInterval(autoSlideInterval);
    }

    // Start a new interval
    autoSlideInterval = setInterval(() => {
        nextSlide();
    }, 5000);
}

function nextSlide() {
    const maxSlide = totalSlides - itemsPerView;
    if (currentSlide < maxSlide) {
        currentSlide++;
    } else {
        currentSlide = 0;
    }
    updateCarousel();
}

function previousSlide() {
    const maxSlide = totalSlides - itemsPerView;
    if (currentSlide > 0) {
        currentSlide--;
    } else {
        currentSlide = maxSlide;
    }
    updateCarousel();
}

function goToSlide(index) {
    currentSlide = index;
    updateCarousel();
}

// Manual navigation functions with auto-slide reset
function nextSlideWithReset() {
    nextSlide();
    resetAutoSlide();
}

function previousSlideWithReset() {
    previousSlide();
    resetAutoSlide();
}

function goToSlideWithReset(index) {
    goToSlide(index);
    resetAutoSlide();
}

// Auto-advance carousel every 5 seconds - Initialize
autoSlideInterval = setInterval(() => {
    nextSlide();
}, 5000);

// Features Carousel Functionality
let currentSlideFeatures = 0;
const slides = document.querySelectorAll('.carousel-slide-features');
const totalSlidesFeatures = slides.length;
const itemsPerViewFeatures = 3;

function updateCarouselFeatures() {
    const carousel = document.getElementById('featuresCarouselMain');
    const dots = document.querySelectorAll('.carousel-dot-features');

    if (carousel) {
        const scrollAmount = currentSlideFeatures * (100 / itemsPerViewFeatures);
        carousel.style.transform = `translateX(-${scrollAmount}%)`;
    }

    dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === currentSlideFeatures);
    });
}

function nextSlideFeatures() {
    const maxSlides = Math.ceil(totalSlidesFeatures / itemsPerView) - 1;
    currentSlideFeatures = currentSlideFeatures >= maxSlides ? 0 : currentSlideFeatures + 1;
    updateCarouselFeatures();
}

function previousSlideFeatures() {
    const maxSlides = Math.ceil(totalSlidesFeatures / itemsPerView) - 1;
    currentSlideFeatures = currentSlideFeatures <= 0 ? maxSlides : currentSlideFeatures - 1;
    updateCarouselFeatures();
}

function goToSlideFeatures(index) {
    currentSlideFeatures = index;
    updateCarouselFeatures();
}

// Initialize carousel features
document.addEventListener('DOMContentLoaded', function() {
    updateCarouselFeatures();
});
