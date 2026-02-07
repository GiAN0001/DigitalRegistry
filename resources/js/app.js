import "./bootstrap"; // Keep existing imports
import Alpine from "alpinejs";
import Chart from "chart.js/auto"; // Use 'chart.js/auto' for auto-registration

window.Alpine = Alpine;
window.Chart = Chart;
Alpine.start();

window.toggleModal = function (modalId) {
    // added by gian
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.toggle("hidden");

        if (!modal.classList.contains("hidden")) {
            document.body.style.overflow = "hidden";
        } else {
            document.body.style.overflow = "auto";
        }
    }
};
