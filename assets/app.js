
document.addEventListener("DOMContentLoaded", function () {

    const logoutLinks = document.querySelectorAll(".logout");

    logoutLinks.forEach(function (link) {
        link.addEventListener("click", function (event) {
            const confirmLogout = confirm(
                "Are you sure you want to logout?"
            );

            if (!confirmLogout) {
                event.preventDefault();
            }
        });
    });

    // Mobile sidebar toggle
    const menuBtn = document.getElementById("menuBtn");
    const sidebar = document.getElementById("sidebar");

    if (menuBtn && sidebar) {
        menuBtn.addEventListener("click", function () {
            sidebar.classList.toggle("show");
        });
    }

    // Automatically hide alerts
    const alerts = document.querySelectorAll(".alert");

    alerts.forEach(function (alert) {
        const closeBtn = alert.querySelector(".alert-close");

        if (closeBtn) {
            closeBtn.addEventListener("click", function () {
                alert.remove();
            });
        }
    });

});
