document.addEventListener("DOMContentLoaded", function() {
    var modal = document.getElementById("popupForm");
    var btn = document.querySelector(".header__cta__doc");
    var closeButtons = document.querySelectorAll("#popupForm .close");

    if (btn && modal) {
    btn.onclick = function() {
        modal.style.display = "block";
        }
    }

    closeButtons.forEach(function(span) {
    span.onclick = function() {
        modal.style.display = "none";
    }
    });

    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });

    // Fermer avec la touche Echap
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape" && modal.style.display === "block") {
            modal.style.display = "none";
        }
    });
});
