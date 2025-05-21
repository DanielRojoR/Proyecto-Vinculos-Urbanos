
document.addEventListener('DOMContentLoaded', function() {
    const parallaxSection = document.getElementById('parallax-section');
    const parallaxSpeed = 0.5; // Adjust this value (0 = no movement, 1 = full speed)

    function updateParallax() {
        // Get the position of the parallax section relative to the viewport
        const parallaxRect = parallaxSection.getBoundingClientRect();

        // Check if the parallax section is in the viewport
        if (parallaxRect.top < window.innerHeight && parallaxRect.bottom > 0) {
            // Calculate the background position based on scroll position
            // This moves the background at a fraction of the normal scroll speed
            const yPos = +window.scrollY * parallaxSpeed;
            parallaxSection.style.backgroundPositionY = yPos + 'px';
        }
    }

    // Update on scroll
    window.addEventListener('scroll', updateParallax);

    // Initial update
    updateParallax();
});