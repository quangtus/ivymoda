//------------------------------SLIDER
// IVY Moda Banner Slider - Clean & Working Version
console.log('🎠 Slider.js loaded - Version 5.0');

(function() {
    'use strict';

    function initSlider() {
        const imgItem = document.querySelectorAll(".aspect-ratio-169 img");
        const imgItemContainer = document.querySelector(".aspect-ratio-169");
        const dotItem = document.querySelectorAll(".dot");

        console.log('🎠 Slider Init:', {
            container: !!imgItemContainer,
            images: imgItem.length,
            dots: dotItem.length
        });

        // Check if elements exist
        if (!imgItemContainer || imgItem.length === 0 || dotItem.length === 0) {
            console.warn('⚠️ Slider elements not found');
            return;
        }

        let currentIndex = 0;
        const imgLength = imgItem.length;
        let autoSlideInterval = null;

        // Setup flexbox layout - FIX: Each image must be 100% of VIEWPORT, not container
        imgItemContainer.style.display = 'flex';
        imgItemContainer.style.width = (imgLength * 100) + '%';
        imgItemContainer.style.transition = 'transform 0.5s ease';

        imgItem.forEach(function(image, idx) {
            // Each image takes 100% of viewport (which is 100%/imgLength of the container)
            image.style.flex = '0 0 ' + (100 / imgLength) + '%';
            image.style.width = (100 / imgLength) + '%';
            image.style.maxWidth = '100%';
            image.style.objectFit = 'cover';
            console.log('🖼️ Image ' + (idx + 1) + ' configured: width=' + (100 / imgLength) + '% of container');
        });

        console.log('🎠 Slider configured: ' + imgLength + ' slides, container width: ' + (imgLength * 100) + '%');

        function goToSlide(index) {
            currentIndex = index;
            // Translate by (100% / total slides) for each slide
            // E.g., with 3 slides: slide 0 = 0%, slide 1 = -33.333%, slide 2 = -66.666%
            const translatePercent = -(index * (100 / imgLength));
            imgItemContainer.style.transform = 'translateX(' + translatePercent + '%)';

            // Update dots
            dotItem.forEach(function(dot, idx) {
                if (idx === index) {
                    dot.classList.add("active");
                } else {
                    dot.classList.remove("active");
                }
            });

            console.log('🎠 Slide ' + (index + 1) + '/' + imgLength + ' | translateX: ' + translatePercent + '%');
        }

        function nextSlide() {
            currentIndex = (currentIndex + 1) % imgLength;
            goToSlide(currentIndex);
        }

        function startAutoSlide() {
            if (autoSlideInterval) {
                clearInterval(autoSlideInterval);
            }
            autoSlideInterval = setInterval(nextSlide, 5000);
        }

        // Dot click handlers
        dotItem.forEach(function(dot, idx) {
            dot.style.cursor = 'pointer';
            dot.style.pointerEvents = 'auto';

            dot.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('🖱️ Dot ' + (idx + 1) + ' clicked');

                clearInterval(autoSlideInterval);
                goToSlide(idx);
                setTimeout(startAutoSlide, 5000);
            });
        });

        // Hover pause
        imgItemContainer.addEventListener('mouseenter', function() {
            clearInterval(autoSlideInterval);
        });

        imgItemContainer.addEventListener('mouseleave', startAutoSlide);

        // Start
        startAutoSlide();
        console.log('✅ Slider initialized successfully');
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSlider);
    } else {
        initSlider();
    }
})();