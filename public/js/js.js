
document.addEventListener("DOMContentLoaded", function () {
    const searchBtn = document.querySelector(".searchBtn");
    const closeBtn = document.querySelector(".closeBtn"); // Corrected class name
    const overlay = document.querySelector(".overlay");
    const searchPanel = document.querySelector(".search-panel");

    // Open Search Panel
    searchBtn.addEventListener("click", function () {
        searchPanel.classList.add("open");
        overlay.style.display = "block"; // Show the overlay
        overlay.style.opacity = "1"; // Fade in effect
    });

    // Close Search Panel on Close Button
    closeBtn.addEventListener("click", function () {
        searchPanel.classList.remove("open");
        overlay.style.opacity = "0"; // Fade out effect
        setTimeout(() => (overlay.style.display = "none"), 300); // Hide overlay after fade out
    });

    // Close Search Panel on Overlay Click
    overlay.addEventListener("click", function () {
        searchPanel.classList.remove("open");
        overlay.style.opacity = "0"; // Fade out effect
        setTimeout(() => (overlay.style.display = "none"), 300); // Hide overlay after fade out
    });
});


document.addEventListener('DOMContentLoaded', function () {
    // Initialize Swiper
    const swiper = new Swiper('.swiper-container', {
        loop: true, // Enables looping
    });

    // Pagination click handler
    const paginationLinks = document.querySelectorAll('.pagination .page-link');
    paginationLinks.forEach(link => {
        link.addEventListener('click', function (event) {
            event.preventDefault();

            const page = this.getAttribute('data-page');
            if (page) {
                swiper.slideToLoop(parseInt(page) - 1); // Slide to specific slide
            }

            if (this.classList.contains('prev')) {
                swiper.slidePrev();
            }

            if (this.classList.contains('next')) {
                swiper.slideNext();
            }

            // Update active class
            paginationLinks.forEach(link => link.classList.remove('active'));
            if (page) this.classList.add('active');
        });
    });

    // Set initial active pagination link
    document.querySelector('.pagination .page-link[data-page="1"]').classList.add('active');
});

