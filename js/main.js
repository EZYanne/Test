(function ($) {
    "use strict";
    
    // Dropdown on mouse hover
    $(document).ready(function () {
        function toggleNavbarMethod() {
            if ($(window).width() > 992) {
                $('.navbar .dropdown').on('mouseover', function () {
                    $('.dropdown-toggle', this).trigger('click');
                }).on('mouseout', function () {
                    $('.dropdown-toggle', this).trigger('click').blur();
                });
            } else {
                $('.navbar .dropdown').off('mouseover').off('mouseout');
            }
        }
        toggleNavbarMethod();
        $(window).resize(toggleNavbarMethod);
    });

    // Show the button when the user scrolls down 100px
    window.onscroll = function() {
        scrollFunction();
    };

    function scrollFunction() {
        const backToTopButton = document.querySelector('.back-to-top');
        if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
            backToTopButton.style.display = "block"; // Show button
        } else {
            backToTopButton.style.display = "none";  // Hide button
        }
    }

    // Smooth scroll to the top when the button is clicked
    document.querySelector('.back-to-top').addEventListener('click', function(e) {
        e.preventDefault();  // Prevent the default anchor behavior
        window.scrollTo({
            top: 0,
            behavior: 'smooth'  // Scroll behavior set to smooth
        });
    });

    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });

    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });

    // Testimonials carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1500,
        dots: true,
        loop: true,
        items: 1
    });

    // Dropdown toggle for Year Subjects
    $('.dropdown-toggle').on('click', function(e) {
        e.preventDefault();
        const dropdownMenu = $(this).next('.dropdown-menu');
        const isVisible = dropdownMenu.is(':visible');

        // Close all dropdowns
        $('.dropdown-menu').slideUp();

        // Toggle current dropdown
        if (!isVisible) {
            dropdownMenu.slideDown();
        }
    });
})(jQuery);
