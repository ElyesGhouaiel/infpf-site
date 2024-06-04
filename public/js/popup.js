document.addEventListener("DOMContentLoaded", function() {
    var modal = document.getElementById("popupForm");
    var btn = document.querySelector(".header__cta__doc");
    var span = document.querySelector(".close");

    btn.onclick = function() {
        modal.style.display = "block";
    }

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
});
