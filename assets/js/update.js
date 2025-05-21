import 'core-js/modules/es.date.to-string';
import 'core-js/modules/es.regexp.exec';
import 'core-js/modules/es.string.replace';
import 'core-js/modules/web.timers';

function updateClock() {
    const clockElement = document.getElementById('clock');
    if (!clockElement) return;

    const now = new Date();
    const options = {
        timeZone: 'Europe/Paris',
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };

    clockElement.textContent = now.toLocaleString('fr-FR', options).replace(',', '');
}

// Mettre à jour l'horloge toutes les 60 secondes
setInterval(updateClock, 60000);

// Mettre à jour l'horloge immédiatement au chargement
document.addEventListener('DOMContentLoaded', updateClock);