<template>
    <div>
        <h2 class="mb-4">📊 Live Attendance Dashboard</h2>
        
        <!-- Error Alert -->
        <div class="alert alert-danger" v-if="apiError">
            <strong>API Error:</strong> {{ apiError }}
            <br><small>Check browser console for details</small>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3" v-for="stat in statsCards" :key="stat.title">
                <div class="card text-white h-100" :class="stat.bgColor">
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ stat.title }}</h5>
                        <h2>{{ stat.value }}</h2>
                        <small>{{ stat.description }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 mb-4">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">📈 Monthly Attendance Overview</h5>
                    </div>
                    <div class="card-body">
                        <div v-if="!hasChartData" class="text-center text-muted py-4">
                            No attendance data available for chart
                        </div>
                        <canvas id="attendanceChart" width="400" height="200" v-else></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">🚀 Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <router-link to="/attendance" class="btn btn-primary btn-block w-100 mb-2">
                            📝 Take Today's Attendance
                        </router-link>
                        <router-link to="/students" class="btn btn-outline-secondary btn-block w-100">
                            👥 Manage Students
                        </router-link>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">📋 Today's Summary</h5>
                    </div>
                    <div class="card-body">
                        <div v-if="hasTodayData">
                            <div v-for="item in todaySummary" :key="item.status" 
                                 class="d-flex justify-content-between align-items-center mb-2 p-2 rounded"
                                 :class="item.bgClass">
                                <span>{{ item.label }}:</span>
                                <strong>{{ item.count }}</strong>
                            </div>
                            <div class="mt-3 text-center">
                                <strong>Total: {{ statistics.todayTotal || 0 }}</strong>
                            </div>
                        </div>
                        <div v-else class="text-center text-muted">
                            No attendance data for today
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { auth } from '../auth.js';  // Add this import

export default {
    data() {
        return {
            statistics: {
                today: { present: 0, absent: 0, late: 0 },
                month: { present: 0, absent: 0, late: 0 },
                total_students: 0
            },
            apiError: null,
            chart: null
        }
    },
    computed: {
        statsCards() {
            return [
                {
                    title: 'Present Today',
                    value: this.statistics.today.present,
                    description: 'Students present',
                    bgColor: 'bg-success'
                },
                {
                    title: 'Absent Today',
                    value: this.statistics.today.absent,
                    description: 'Students absent',
                    bgColor: 'bg-danger'
                },
                {
                    title: 'Late Today',
                    value: this.statistics.today.late,
                    description: 'Students late',
                    bgColor: 'bg-warning'
                },
                {
                    title: 'Total Students',
                    value: this.statistics.total_students,
                    description: 'In system',
                    bgColor: 'bg-info'
                }
            ];
        },
        todaySummary() {
            return [
                { 
                    status: 'present', 
                    label: 'Present', 
                    count: this.statistics.today.present,
                    bgClass: 'bg-success bg-opacity-10 text-success'
                },
                { 
                    status: 'absent', 
                    label: 'Absent', 
                    count: this.statistics.today.absent,
                    bgClass: 'bg-danger bg-opacity-10 text-danger'
                },
                { 
                    status: 'late', 
                    label: 'Late', 
                    count: this.statistics.today.late,
                    bgClass: 'bg-warning bg-opacity-10 text-warning'
                }
            ];
        },
        hasChartData() {
            return this.statistics.month && 
                   (this.statistics.month.present > 0 || 
                    this.statistics.month.absent > 0 || 
                    this.statistics.month.late > 0);
        },
        hasTodayData() {
            return this.statistics.today && 
                   (this.statistics.today.present > 0 || 
                    this.statistics.today.absent > 0 || 
                    this.statistics.today.late > 0);
        }
    },
    methods: {
        async loadStatistics() {
            try {
                this.apiError = null;
                console.log('Loading statistics from API...');
                
                const response = await axios.get('/api/dashboard/statistics');
                console.log('API Response:', response.data);
                
                // Handle the response data directly
                this.statistics = response.data;
                
                // Calculate today's total
                this.statistics.todayTotal = 
                    (this.statistics.today?.present || 0) +
                    (this.statistics.today?.absent || 0) +
                    (this.statistics.today?.late || 0);
                    
                this.renderChart();
                
            } catch (error) {
                console.error('Error loading statistics:', error);
                this.apiError = error.message;
                
                // Set default values
                this.statistics = {
                    today: { present: 0, absent: 0, late: 0 },
                    month: { present: 0, absent: 0, late: 0 },
                    total_students: 0
                };
            }
        },
        renderChart() {
            const ctx = document.getElementById('attendanceChart');
            if (!ctx) return;
            
            // Destroy existing chart
            if (this.chart) {
                this.chart.destroy();
            }

            if (this.hasChartData) {
                this.chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Present', 'Absent', 'Late'],
                        datasets: [{
                            label: 'This Month',
                            data: [
                                this.statistics.month.present,
                                this.statistics.month.absent,
                                this.statistics.month.late
                            ],
                            backgroundColor: ['#28a745', '#dc3545', '#ffc107'],
                            borderColor: ['#218838', '#c82333', '#e0a800'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });
            }
        }
    },
    async mounted() {
        // Check if user is authenticated
        if (!auth.isAuthenticated()) {
            this.$router.push('/login');
            return;
        }
        await this.loadStatistics();
    },
    beforeUnmount() {
        if (this.chart) {
            this.chart.destroy();
        }
    }
}
</script>