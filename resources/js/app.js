import './bootstrap';
import Alpine from 'alpinejs';
import './chat';
import L from 'leaflet';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

// Perbaiki ikon marker Leaflet saat dibundel Vite
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({ iconUrl: markerIcon, iconRetinaUrl: markerIcon2x, shadowUrl: markerShadow });

const TILE_URL = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
const TILE_ATTR = '&copy; OpenStreetMap';
// Titik tengah default: Kota SoE, Timor Tengah Selatan
const DEFAULT_CENTER = [-9.8606, 124.2836];

/** Peta pemilih lokasi: tombol "Ambil Lokasi Saya" + pin bisa digeser. */
Alpine.data('mapPicker', (lat = null, lng = null) => ({
    lat: lat,
    lng: lng,
    pesan: '',
    map: null,
    marker: null,
    init() {
        const start = this.lat && this.lng ? [this.lat, this.lng] : DEFAULT_CENTER;
        this.map = L.map(this.$refs.map).setView(start, this.lat ? 16 : 11);
        L.tileLayer(TILE_URL, { attribution: TILE_ATTR, maxZoom: 19 }).addTo(this.map);
        if (this.lat && this.lng) this.pasangPin(this.lat, this.lng);
        this.map.on('click', (e) => this.pasangPin(e.latlng.lat, e.latlng.lng));
    },
    pasangPin(lat, lng) {
        this.lat = Number(lat).toFixed(7);
        this.lng = Number(lng).toFixed(7);
        if (!this.marker) {
            this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map);
            this.marker.on('dragend', () => {
                const p = this.marker.getLatLng();
                this.lat = p.lat.toFixed(7);
                this.lng = p.lng.toFixed(7);
            });
        } else {
            this.marker.setLatLng([lat, lng]);
        }
    },
    ambilLokasi() {
        if (!navigator.geolocation) {
            this.pesan = 'Perangkat tidak mendukung lokasi. Klik peta untuk memilih titik.';
            return;
        }
        this.pesan = 'Mengambil lokasi...';
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                this.pasangPin(pos.coords.latitude, pos.coords.longitude);
                this.map.setView([pos.coords.latitude, pos.coords.longitude], 17);
                this.pesan = 'Lokasi ditemukan. Geser pin bila kurang tepat.';
            },
            () => { this.pesan = 'Lokasi tidak bisa diambil. Izinkan akses lokasi atau klik peta.'; },
            { enableHighAccuracy: true, timeout: 15000 },
        );
    },
}));

/** Peta tampilan saja. */
Alpine.data('mapView', (lat, lng, zoom = 16) => ({
    init() {
        const map = L.map(this.$refs.map, { scrollWheelZoom: false }).setView([lat, lng], zoom);
        L.tileLayer(TILE_URL, { attribution: TILE_ATTR, maxZoom: 19 }).addTo(map);
        L.marker([lat, lng]).addTo(map);
    },
}));

/** Dropdown bertingkat kecamatan -> desa. */
Alpine.data('wilayahSelect', (kecamatanId = '', desaId = '', url = '') => ({
    kecamatanId: String(kecamatanId ?? ''),
    desaId: String(desaId ?? ''),
    desa: [],
    memuat: false,
    async init() {
        if (this.kecamatanId) await this.muatDesa(false);
        this.$watch('kecamatanId', () => this.muatDesa(true));
    },
    async muatDesa(reset) {
        if (reset) this.desaId = '';
        this.desa = [];
        if (!this.kecamatanId) return;
        this.memuat = true;
        try {
            const res = await window.axios.get(url.replace('__ID__', this.kecamatanId));
            this.desa = res.data;
        } finally {
            this.memuat = false;
        }
    },
}));

window.Alpine = Alpine;
Alpine.start();
