// resources/js/app.js
import './bootstrap';
import Alpine from 'alpinejs';
import './text-to-speech';


window.Alpine = Alpine;
Alpine.start();

// Global functions for queue management
window.queueHelpers = {
    formatTime(dateString) {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });
    },

    getStatusColor(status) {
        const colors = {
            'waiting': 'blue',
            'called': 'yellow',
            'served': 'green',
            'cancelled': 'red'
        };
        return colors[status] || 'gray';
    }
}
