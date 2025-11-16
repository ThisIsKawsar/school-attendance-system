<template>
    <div>
        <h2 class="mb-4">📝 Attendance Recording</h2>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">🏫 Class *</label>
                        <select class="form-control" v-model="filters.class" @change="loadStudents">
                            <option value="">Select Class</option>
                            <option v-for="cls in classes" :key="cls" :value="cls">Class {{ cls }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">📚 Section *</label>
                        <select class="form-control" v-model="filters.section" @change="loadStudents">
                            <option value="">Select Section</option>
                            <option v-for="sec in sections" :key="sec" :value="sec">Section {{ sec }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">📅 Date</label>
                        <input type="date" class="form-control" v-model="filters.date" :max="today">
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Table -->
        <div class="card" v-if="students.length > 0">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    👥 Mark Attendance for Class {{ filters.class }}-{{ filters.section }}
                    <small class="text-muted">({{ students.length }} students)</small>
                </h5>
                <div>
                    <button class="btn btn-sm btn-success me-2" @click="markAll('present')">
                        ✅ Mark All Present
                    </button>
                    <button class="btn btn-sm btn-danger me-2" @click="markAll('absent')">
                        ❌ Mark All Absent
                    </button>
                    <button class="btn btn-sm btn-warning" @click="markAll('late')">
                        ⏰ Mark All Late
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Class</th>
                                <th>Section</th>
                                <th width="150">Status</th>
                                <th width="200">Note</th>
                                <th width="120">Quick Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="student in students" :key="student.id">
                                <td><strong>{{ student.student_id }}</strong></td>
                                <td>{{ student.name }}</td>
                                <td>Class {{ student.class }}</td>
                                <td>Section {{ student.section }}</td>
                                <td>
                                    <select class="form-control form-control-sm" 
                                            v-model="attendanceData[student.id].status"
                                            @change="updateStatistics">
                                        <option value="present">✅ Present</option>
                                        <option value="absent">❌ Absent</option>
                                        <option value="late">⏰ Late</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" 
                                           class="form-control form-control-sm" 
                                           v-model="attendanceData[student.id].note"
                                           placeholder="Optional note...">
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info mb-1" 
                                            @click="setNote(student.id, 'Sick Leave')"
                                            title="Sick Leave">
                                        🤒
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning" 
                                            @click="setNote(student.id, 'Personal Reason')"
                                            title="Personal Reason">
                                        🏠
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Statistics and Submit -->
                <div class="d-flex justify-content-between align-items-center mt-4 p-3 bg-light rounded">
                    <div>
                        <h6 class="mb-2">📊 Real-time Statistics:</h6>
                        <span class="badge bg-success me-2">Present: {{ statistics.present }}</span>
                        <span class="badge bg-danger me-2">Absent: {{ statistics.absent }}</span>
                        <span class="badge bg-warning me-2">Late: {{ statistics.late }}</span>
                        <div class="mt-2">
                            <strong>Overall: {{ statistics.percentage }}% Present</strong>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-lg" 
                            @click="submitAttendance" 
                            :disabled="isSubmitting || !canSubmit">
                        {{ isSubmitting ? '💾 Saving...' : '💾 Save Attendance' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Empty States -->
        <div class="alert alert-info text-center" v-else-if="filters.class && filters.section && !loading">
            <h5>👋 No Students Found</h5>
            <p>No students found for Class {{ filters.class }}-Section {{ filters.section }}</p>
            <router-link to="/students" class="btn btn-primary">Add Students</router-link>
        </div>

        <div class="alert alert-warning text-center" v-else-if="!filters.class || !filters.section">
            <h5>🔍 Select Class & Section</h5>
            <p>Please select a class and section to load students for attendance.</p>
        </div>

        <div class="text-center py-5" v-else-if="loading">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading students...</span>
            </div>
            <p class="mt-2">Loading students...</p>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';

// Reactive data
const students = ref([]);
const loading = ref(false);
const isSubmitting = ref(false);
const attendanceData = ref({});

const filters = reactive({
    class: '',
    section: '',
    date: new Date().toISOString().split('T')[0]
});

const classes = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'];
const sections = ['A', 'B', 'C', 'D'];

// Computed properties
const today = computed(() => new Date().toISOString().split('T')[0]);

const statistics = computed(() => {
    const total = students.value.length;
    const present = Object.values(attendanceData.value).filter(a => a.status === 'present').length;
    const absent = Object.values(attendanceData.value).filter(a => a.status === 'absent').length;
    const late = Object.values(attendanceData.value).filter(a => a.status === 'late').length;
    const percentage = total > 0 ? Math.round((present / total) * 100) : 0;

    return { present, absent, late, percentage, total };
});

const canSubmit = computed(() => {
    return students.value.length > 0 && !isSubmitting.value;
});

// Methods
const loadStudents = async () => {
    if (!filters.class || !filters.section) {
        students.value = [];
        return;
    }

    try {
        loading.value = true;
        const params = new URLSearchParams({
            class: filters.class,
            section: filters.section
        });

        const response = await axios.get(`/api/students?${params}`);
        students.value = response.data.data;
        
        // Initialize attendance data
        initializeAttendanceData();
    } catch (error) {
        console.error('Error loading students:', error);
        alert('Error loading students');
    } finally {
        loading.value = false;
    }
};

const initializeAttendanceData = () => {
    attendanceData.value = {};
    students.value.forEach(student => {
        attendanceData.value[student.id] = {
            student_id: student.id,
            status: student.current_status || 'present',
            note: ''
        };
    });
};

const markAll = (status) => {
    Object.keys(attendanceData.value).forEach(studentId => {
        attendanceData.value[studentId].status = status;
    });
};

const setNote = (studentId, note) => {
    if (attendanceData.value[studentId]) {
        attendanceData.value[studentId].note = note;
    }
};

const updateStatistics = () => {
    // Computed property will automatically update
};

const submitAttendance = async () => {
    try {
        isSubmitting.value = true;
        
        const attendanceDataArray = Object.values(attendanceData.value);
        
        await axios.post('/api/attendance/bulk', {
            attendance_data: attendanceDataArray
        });

        alert('✅ Attendance saved successfully!');
        
        // Reload students to get updated status
        await loadStudents();
        
    } catch (error) {
        console.error('Error saving attendance:', error);
        alert('❌ Error saving attendance: ' + (error.response?.data?.message || 'Please try again.'));
    } finally {
        isSubmitting.value = false;
    }
};

// Watchers
watch(() => filters.class, loadStudents);
watch(() => filters.section, loadStudents);

// Lifecycle
onMounted(() => {
    // Initial setup
});
</script>