import L from 'leaflet';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

const defaultIcon = L.icon({
    iconUrl: markerIcon,
    iconRetinaUrl: markerIcon2x,
    shadowUrl: markerShadow,
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41],
});

L.Marker.prototype.options.icon = defaultIcon;

document.addEventListener('alpine:init', () => {
    Alpine.data('leafletMap', (latitude, longitude) => ({
        latitude,
        longitude,
        address: '',
        marker: null,

        init() {
            const map = L.map(this.$el).setView([this.latitude, this.longitude], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            }).addTo(map);

            setTimeout(() => map.invalidateSize(), 200);

            this.marker = L.marker([this.latitude, this.longitude], { icon: defaultIcon }).addTo(map);
            this.marker.bindPopup(this.popupContent());

            this.reverseGeocode();
        },

        reverseGeocode() {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${this.latitude}&lon=${this.longitude}`)
                .then((response) => response.json())
                .then((data) => {
                    this.address = data.display_name || '';
                    this.marker.setPopupContent(this.popupContent());
                })
                .catch(() => {
                    this.address = '';
                });
        },

        popupContent() {
            const address = this.address ? `<br>${this.address}` : '';

            return `Lokasi Verifikasi${address}<br><a href="https://www.google.com/maps/search/?api=1&query=${this.latitude},${this.longitude}" target="_blank" rel="noopener">Buka di Google Maps</a>`;
        },
    }));
});
