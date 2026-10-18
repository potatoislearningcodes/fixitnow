import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

// Leaflet's default marker icon references image paths that don't survive
// Vite's bundling unless pointed at explicitly — this fixes the broken
// marker icon that otherwise shows as a blank grey square.
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

/**
 * Renders a small OpenStreetMap view with one marker. Called from Blade
 * views (see resources/views/bookings/show.blade.php) after the DOM is
 * ready, since app.js loads as a deferred module before DOMContentLoaded.
 */
window.initFixItNowMap = function (elementId, lat, lng, label) {
    const el = document.getElementById(elementId);
    if (! el) return;

    const map = L.map(elementId).setView([lat, lng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
    }).addTo(map);

    L.marker([lat, lng]).addTo(map).bindPopup(label).openPopup();
};
