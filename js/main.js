document.addEventListener('DOMContentLoaded', () => {
    // Hide page loader after page load
    const loader = document.querySelector('.page-loader');
    if (loader) {
        setTimeout(() => {
            loader.classList.add('hidden');
        }, 500); // 500ms delay for smooth transition demo
    }

    // Mobile menu toggle
    const mobileToggle = document.querySelector('.mobile-toggle');
    const navLinks = document.querySelector('.nav-links');

    if (mobileToggle) {
        mobileToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });
    }

    // Initialize Add to Cart forms via AJAX if desired, 
    // or keep standard form submissions. We'll use standard post but 
    // maybe enhance with some animations later.
    
    // Add subtle fade-in animation to products on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.fade-in-element').forEach(el => {
        observer.observe(el);
    });
});
