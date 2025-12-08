
import './bootstrap'; // Keep existing imports
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto'; // Use 'chart.js/auto' for auto-registration

window.Alpine = Alpine;
window.Chart = Chart;
Alpine.start();

 // Make it globally accessible for your component