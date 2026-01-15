/**
 * ACT Test Prep - Charts and Data Visualization
 */

const Charts = {
    instances: new Map(),
    defaultColors: {
        primary: '#3b82f6',
        secondary: '#8b5cf6',
        success: '#10b981',
        warning: '#f59e0b',
        danger: '#ef4444',
        gray: '#6b7280'
    },

    subjectColors: {
        english: '#3b82f6',
        math: '#10b981',
        reading: '#8b5cf6',
        science: '#f59e0b',
        writing: '#ec4899'
    },

    /**
     * Create or update chart
     */
    create(canvasId, type, data, options = {}) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) {
            console.error(`Canvas ${canvasId} not found`);
            return null;
        }

        // Destroy existing chart
        if (this.instances.has(canvasId)) {
            this.instances.get(canvasId).destroy();
        }

        const ctx = canvas.getContext('2d');
        
        // Default options
        const defaultOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: options.showLegend !== false,
                    position: 'bottom'
                }
            }
        };

        const chart = new Chart(ctx, {
            type,
            data,
            options: { ...defaultOptions, ...options }
        });

        this.instances.set(canvasId, chart);
        return chart;
    },

    /**
     * Create line chart
     */
    line(canvasId, labels, datasets, options = {}) {
        const data = {
            labels,
            datasets: datasets.map((ds, i) => ({
                label: ds.label || `Dataset ${i + 1}`,
                data: ds.data,
                borderColor: ds.color || this.defaultColors.primary,
                backgroundColor: ds.fill ? this.hexToRgba(ds.color || this.defaultColors.primary, 0.1) : 'transparent',
                fill: ds.fill || false,
                tension: ds.smooth ? 0.4 : 0,
                pointRadius: ds.showPoints ? 4 : 0,
                pointHoverRadius: 6
            }))
        };

        return this.create(canvasId, 'line', data, {
            scales: {
                y: {
                    beginAtZero: options.beginAtZero !== false
                }
            },
            ...options
        });
    },

    /**
     * Create bar chart
     */
    bar(canvasId, labels, datasets, options = {}) {
        const data = {
            labels,
            datasets: datasets.map((ds, i) => ({
                label: ds.label || `Dataset ${i + 1}`,
                data: ds.data,
                backgroundColor: ds.colors || ds.color || this.defaultColors.primary,
                borderRadius: 4
            }))
        };

        return this.create(canvasId, 'bar', data, {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            ...options
        });
    },

    /**
     * Create horizontal bar chart
     */
    horizontalBar(canvasId, labels, datasets, options = {}) {
        return this.bar(canvasId, labels, datasets, {
            indexAxis: 'y',
            ...options
        });
    },

    /**
     * Create pie chart
     */
    pie(canvasId, labels, data, colors = null, options = {}) {
        const chartData = {
            labels,
            datasets: [{
                data,
                backgroundColor: colors || this.generateColors(labels.length)
            }]
        };

        return this.create(canvasId, 'pie', chartData, options);
    },

    /**
     * Create doughnut chart
     */
    doughnut(canvasId, labels, data, colors = null, options = {}) {
        const chartData = {
            labels,
            datasets: [{
                data,
                backgroundColor: colors || this.generateColors(labels.length),
                cutout: options.cutout || '60%'
            }]
        };

        return this.create(canvasId, 'doughnut', chartData, options);
    },

    /**
     * Create radar chart
     */
    radar(canvasId, labels, datasets, options = {}) {
        const data = {
            labels,
            datasets: datasets.map((ds, i) => ({
                label: ds.label || `Dataset ${i + 1}`,
                data: ds.data,
                borderColor: ds.color || this.defaultColors.primary,
                backgroundColor: this.hexToRgba(ds.color || this.defaultColors.primary, 0.2),
                pointBackgroundColor: ds.color || this.defaultColors.primary
            }))
        };

        return this.create(canvasId, 'radar', data, {
            scales: {
                r: {
                    beginAtZero: true,
                    max: options.max || 36
                }
            },
            ...options
        });
    },

    /**
     * Create score trend chart
     */
    scoreTrend(canvasId, scores, options = {}) {
        const labels = scores.map((_, i) => `Test ${i + 1}`);
        
        return this.line(canvasId, labels, [{
            label: 'Score',
            data: scores,
            color: this.defaultColors.primary,
            fill: true,
            smooth: true,
            showPoints: true
        }], {
            scales: {
                y: {
                    min: 0,
                    max: 36,
                    ticks: {
                        stepSize: 6
                    }
                }
            },
            ...options
        });
    },

    /**
     * Create subject comparison chart
     */
    subjectComparison(canvasId, subjectScores, options = {}) {
        const subjects = Object.keys(subjectScores);
        const scores = Object.values(subjectScores);
        const colors = subjects.map(s => this.subjectColors[s] || this.defaultColors.gray);

        return this.bar(canvasId, subjects.map(s => Utils.capitalize(s)), [{
            label: 'Score',
            data: scores,
            colors
        }], {
            scales: {
                y: {
                    min: 0,
                    max: 36,
                    ticks: {
                        stepSize: 6
                    }
                }
            },
            ...options
        });
    },

    /**
     * Create progress ring
     */
    progressRing(canvasId, progress, options = {}) {
        const remaining = 100 - progress;
        
        return this.doughnut(canvasId, ['Progress', 'Remaining'], 
            [progress, remaining],
            [options.color || this.defaultColors.primary, '#e5e7eb'],
            {
                cutout: '75%',
                showLegend: false,
                ...options
            }
        );
    },

    /**
     * Create activity heatmap (basic version)
     */
    activityHeatmap(containerId, data, options = {}) {
        const container = document.getElementById(containerId);
        if (!container) return;

        const weeks = 12;
        const days = 7;
        
        let html = '<div class="grid grid-cols-7 gap-1">';
        
        for (let w = 0; w < weeks; w++) {
            for (let d = 0; d < days; d++) {
                const index = w * 7 + d;
                const value = data[index] || 0;
                const intensity = Math.min(4, Math.floor(value / 2));
                const colors = ['bg-gray-100', 'bg-green-200', 'bg-green-300', 'bg-green-400', 'bg-green-500'];
                
                html += `<div class="w-4 h-4 rounded-sm ${colors[intensity]}" title="${value} activities"></div>`;
            }
        }
        
        html += '</div>';
        container.innerHTML = html;
    },

    /**
     * Update chart data
     */
    update(canvasId, newData) {
        const chart = this.instances.get(canvasId);
        if (chart) {
            chart.data = newData;
            chart.update();
        }
    },

    /**
     * Destroy chart
     */
    destroy(canvasId) {
        const chart = this.instances.get(canvasId);
        if (chart) {
            chart.destroy();
            this.instances.delete(canvasId);
        }
    },

    /**
     * Destroy all charts
     */
    destroyAll() {
        this.instances.forEach(chart => chart.destroy());
        this.instances.clear();
    },

    // ==================== Helper Methods ====================

    /**
     * Generate array of colors
     */
    generateColors(count) {
        const baseColors = [
            '#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#ef4444',
            '#06b6d4', '#84cc16', '#f43f5e', '#6366f1', '#14b8a6'
        ];
        
        const colors = [];
        for (let i = 0; i < count; i++) {
            colors.push(baseColors[i % baseColors.length]);
        }
        return colors;
    },

    /**
     * Convert hex to rgba
     */
    hexToRgba(hex, alpha) {
        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }
};

// Make available globally
window.Charts = Charts;
