/**
 * Module de rafraîchissement automatique du dashboard NinjaOne
 * Actualise les graphiques toutes les 30 secondes
 */

// Stocker les instances Chart globalement
window.chartInstances = {
    ticketsChart: null,
    patchesChart: null,
    alertsChart: null,
    healthChart: null
};

// Fonction pour enregistrer une instance Chart
window.registerChartInstance = function (name, chartInstance) {
    //console.log('Enregistrement du graphique:', name);
    window.chartInstances[name] = chartInstance;
};

class DashboardRefresh {
    constructor(refreshInterval = 30000) {
        this.refreshInterval = refreshInterval;
        this.init();
    }

    /**
     * Initialiser le système de rafraîchissement
     */
    init() {
        // Démarrer le rafraîchissement automatique
        this.startAutoRefresh();
    }

    /**
     * Démarrer le rafraîchissement automatique
     */
    startAutoRefresh() {
        setInterval(() => {
            this.refreshDashboardData();
        }, this.refreshInterval);
    }

    /**
     * Actualiser les données du dashboard via AJAX
     */
    refreshDashboardData() {
        fetch('/api/dashboard/ninjaOne/refresh', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
            .then(response => {
                if (!response.ok) {
                    console.error('Erreur HTTP:', response.status, response.statusText);
                    throw new Error(`Erreur HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Données reçues:', data);
                this.updateCharts(data);
            })
            .catch(error => {
                console.error('Erreur lors du rafraîchissement:', error);
            });
    }

    /**
     * Mettre à jour les graphiques avec les nouvelles données
     */
    updateCharts(data) {
        // Actualiser les graphiques Chart.js
        this.updateTicketsChart(data);
        this.updatePatchesChart(data);
        this.updateAlertsChart(data);
        this.updateHealthChart(data);

        // Actualiser les tableaux et listes
        this.updateTicketsTable(data);
        this.updateHealthTable(data);
        this.updateAlertsList(data);

        // Actualiser les nombre totaux affichés
        this.updateTotals(data);
    }

    /**
     * Mettre à jour le graphique des tickets
     */
    updateTicketsChart(data) {
        const ticketsChart = window.chartInstances.ticketsChart;
        console.log('Mise à jour tickets - Chart instance exists:', !!ticketsChart);

        if (ticketsChart && data.tickets) {
            try {
                const statusTickets = data.tickets.statusTickets;
                const ticketCounts = data.tickets.ticketCounts;
                const statusColors = this.getStatusColors();
                const colors = statusTickets.map(status => statusColors[status] || '#999999');

                console.log('Tickets - Nouveaux labels:', statusTickets);
                console.log('Tickets - Nouveaux données:', ticketCounts);

                ticketsChart.data.labels = statusTickets;
                ticketsChart.data.datasets[0].data = ticketCounts;
                ticketsChart.data.datasets[0].backgroundColor = colors;
                ticketsChart.update();
                console.log('✓ Graphique tickets mis à jour');
            } catch (e) {
                console.error('✗ Erreur lors de la mise à jour du graphique tickets:', e);
            }
        } else {
            console.warn('⚠ Graphique tickets non disponible');
        }
    }

    /**
     * Mettre à jour le graphique des patches
     */
    updatePatchesChart(data) {
        const patchesChart = window.chartInstances.patchesChart;
        console.log('Mise à jour patches - Chart instance exists:', !!patchesChart);

        if (patchesChart && data.allPatchesJson) {
            try {
                const allPatchesLabels = JSON.parse(data.allPatchesJson);
                const allPatchesCounts = JSON.parse(data.allPatchesCountsJson);

                patchesChart.data.labels = allPatchesLabels;
                patchesChart.data.datasets[0].data = allPatchesCounts;
                patchesChart.update();
            } catch (e) {
                console.error('✗ Erreur lors du parsing des données patches:', e);
            }
        } else {
            console.warn('⚠ Graphique patches non disponible');
        }
    }

    /**
     * Mettre à jour le graphique des alertes
     */
    updateAlertsChart(data) {
        const alertsChart = window.chartInstances.alertsChart;
        console.log('Mise à jour alertes - Chart instance exists:', !!alertsChart);

        if (alertsChart && data.allAlertsJson) {
            try {
                const allAlertsData = JSON.parse(data.allAlertsJson);
                const allLabelsAlertsData = JSON.parse(data.allLabelsAlertsJson);

                alertsChart.data.labels = allLabelsAlertsData;
                alertsChart.data.datasets[0].data = allAlertsData;
                alertsChart.update();
            } catch (e) {
                console.error('✗ Erreur lors du parsing des données alertes:', e);
            }
        } else {
            console.warn('⚠ Graphique alertes non disponible');
        }
    }

    /**
     * Mettre à jour le graphique de santé des appareils
     */
    updateHealthChart(data) {
        const healthChart = window.chartInstances.healthChart;

        if (healthChart && data.deviceHealths) {
            try {
                const statusHealth = JSON.parse(data.deviceHealths.statusHealthJson);
                const healthCounts = JSON.parse(data.deviceHealths.healthCountsJson);

                healthChart.data.labels = statusHealth;
                healthChart.data.datasets[0].data = healthCounts;
                healthChart.update();
            } catch (e) {
                console.error('✗ Erreur lors du parsing des données santé:', e);
            }
        } else {
            console.warn('⚠ Graphique santé non disponible');
        }
    }

    /**
     * Mettre à jour le tableau des tickets
     */
    updateTicketsTable(data) {
        try {
            const tbody = document.getElementById('ticketsTableBody');
            if (!tbody || !data.tickets) return;

            const statusTickets = data.tickets.statusTickets;
            const ticketCounts = data.tickets.ticketCounts;

            // Couleurs des statuts
            const statusColors = this.getStatusColors();

            // Générer le HTML du tableau
            let html = '';
            for (let i = 0; i < statusTickets.length; i++) {
                const status = statusTickets[i];
                const count = ticketCounts[i] || 0;
                const color = statusColors[status] || '#999999';

                html += `
                    <tr class="text-center">
                        <td>
                            <div class="w-6 h-6 rounded-full mx-auto" style="background-color: ${color}"></div>
                        </td>
                        <td class="p-2 text-left">${status}</td>
                        <td class="p-2 text-right">${count}</td>
                    </tr>
                `;
            }

            tbody.innerHTML = html;
        } catch (e) {
            console.error('✗ Erreur lors de la mise à jour du tableau tickets:', e);
        }
    }

    /**
     * Mettre à jour le tableau de santé des appareils
     */
    updateHealthTable(data) {
        try {
            const tbody = document.getElementById('healthTableBody');
            if (!tbody || !data.deviceHealths) return;

            const statusHealth = JSON.parse(data.deviceHealths.statusHealthJson);
            const healthCounts = JSON.parse(data.deviceHealths.healthCountsJson);

            // Couleurs des statuts de santé
            const statusColors = {
                'HEALTHY': '#4BC0A0',
                'UNKNOWN': '#B1BDBE',
                'NEEDS_ATTENTION': '#FFCE56',
                'UNHEALTHY': '#FF6384'
            };

            // Générer le HTML du tableau
            let html = '';
            for (let i = 0; i < statusHealth.length; i++) {
                const status = statusHealth[i];
                const count = healthCounts[i] || 0;
                const color = statusColors[status] || '#999999';

                html += `
                    <tr class="text-center">
                        <td class="p-2">
                            <div class="w-6 h-6 rounded-full mx-auto" style="background-color: ${color}"></div>
                        </td>
                        <td class="p-2 text-left">${status}</td>
                        <td class="p-2 text-right">${count}</td>
                    </tr>
                `;
            }

            tbody.innerHTML = html;
        } catch (e) {
            console.error('✗ Erreur lors de la mise à jour du tableau santé:', e);
        }
    }

    /**
     * Mettre à jour la liste des alertes
     */
    updateAlertsList(data) {
        try {
            const alertsList = document.getElementById('alertsLabelsList');
            if (!alertsList || !data.allLabelsAlertsJson) return;

            const labels = JSON.parse(data.allLabelsAlertsJson);
            const colors = ['#6BAED6', '#F4A582', '#66C2A5'];

            // Générer le HTML de la liste
            let html = '';
            for (let i = 0; i < labels.length; i++) {
                const label = labels[i];
                const color = colors[i % colors.length];

                html += `
                    <div class="flex items-center justify-start gap-2">
                        <span class="h-4 aspect-square rounded-full" style="background-color: ${color};"></span>
                        <p>${label}</p>
                    </div>
                `;
            }

            alertsList.innerHTML = html;
        } catch (e) {
            console.error('✗ Erreur lors de la mise à jour de la liste alertes:', e);
        }
    }

    /**
     * Mettre à jour les nombres totaux affichés
     */
    updateTotals(data) {
        try {
            // Tickets
            const ticketCountElement = document.querySelector('[data-total="tickets"]');
            if (ticketCountElement && data.tickets && data.tickets.openTicketCounts !== undefined) {
                const newValue = String(data.tickets.openTicketCounts);
                if (ticketCountElement.textContent !== newValue) {
                    ticketCountElement.textContent = newValue;
                }
            }

            // Patches
            const patchCountElement = document.querySelector('[data-total="patches"]');
            if (patchCountElement && data.allPatches !== undefined) {
                const newValue = String(data.allPatches);
                if (patchCountElement.textContent !== newValue) {
                    patchCountElement.textContent = newValue;
                }
            }

            // Alertes
            const alertCountElement = document.querySelector('[data-total="alerts"]');
            if (alertCountElement && data.allAlerts !== undefined) {
                const newValue = String(data.allAlerts);
                if (alertCountElement.textContent !== newValue) {
                    alertCountElement.textContent = newValue;
                }
            }
        } catch (e) {
            console.error('✗ Erreur lors de la mise à jour des totaux:', e);
        }
    }

    /**
     * Configurer un bouton de rafraîchissement manuel
     */
    setupManualRefreshButton() {
        const refreshButton = document.getElementById('refreshButton');
        if (refreshButton) {
            refreshButton.addEventListener('click', () => {
                this.refreshDashboardData();
                refreshButton.classList.add('animate-spin');
                setTimeout(() => {
                    refreshButton.classList.remove('animate-spin');
                }, 1000);
            });
        }
    }

    /**
     * Retourner les couleurs des statuts de tickets
     */
    getStatusColors() {
        return {
            'Paused': '#949597',
            'En attente': '#337ab7',
            'Ouvert': '#fac905',
            'Nouveau': '#d53948'
        };
    }
}

// Initialiser au chargement du DOM
document.addEventListener('DOMContentLoaded', () => {
    new DashboardRefresh(30000); // 30 secondes
});
