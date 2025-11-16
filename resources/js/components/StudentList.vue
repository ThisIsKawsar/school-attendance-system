<template>
    <div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>👥 Student Management</h2>
            <button class="btn btn-primary" @click="showAddModal = true">
                ➕ Add Student
            </button>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">🔍 Search</label>
                        <input type="text" class="form-control" placeholder="Name or Student ID..." 
                               v-model="filters.search" @input="debounceLoadStudents">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">🏫 Class</label>
                        <select class="form-control" v-model="filters.class" @change="loadStudents">
                            <option value="">All Classes</option>
                            <option v-for="cls in classes" :key="cls" :value="cls">Class {{ cls }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">📚 Section</label>
                        <select class="form-control" v-model="filters.section" @change="loadStudents">
                            <option value="">All Sections</option>
                            <option v-for="sec in sections" :key="sec" :value="sec">Section {{ sec }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Table -->
        <div class="card">
            <div class="card-body">
                <div v-if="loading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <template v-else>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Photo</th>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Class</th>
                                    <th>Section</th>
                                    <th>Attendance %</th>
                                    <th>Today's Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="student in students.data" :key="student.id">
                                    <td>
                                        <img :src="student.photo || '/default-avatar.png'" 
                                             class="rounded-circle" width="40" height="40">
                                    </td>
                                    <td><strong>{{ student.student_id }}</strong></td>
                                    <td>{{ student.name }}</td>
                                    <td>Class {{ student.class }}</td>
                                    <td>Section {{ student.section }}</td>
                                    <td>
                                        <span :class="getPercentageClass(student.attendance_percentage)">
                                            {{ student.attendance_percentage }}%
                                        </span>
                                    </td>
                                    <td>
                                        <span :class="getStatusClass(student.current_status)">
                                            {{ student.current_status }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning me-1" @click="editStudent(student)">
                                            ✏️ Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger" @click="deleteStudent(student.id)">
                                            🗑️ Delete
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <nav v-if="students.meta?.last_page > 1" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item" :class="{ disabled: !students.links?.prev }">
                                <button class="page-link" @click="loadStudents(students.meta.current_page - 1)">
                                    Previous
                                </button>
                            </li>
                            <li class="page-item" v-for="page in students.meta.last_page" :key="page"
                                :class="{ active: page === students.meta.current_page }">
                                <button class="page-link" @click="loadStudents(page)">{{ page }}</button>
                            </li>
                            <li class="page-item" :class="{ disabled: !students.links?.next }">
                                <button class="page-link" @click="loadStudents(students.meta.current_page + 1)">
                                    Next
                                </button>
                            </li>
                        </ul>
                    </nav>
                </template>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div v-if="showAddModal" class="modal fade show d-block" style="background: rgba(0,0,0,0.5)">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ editingStudent ? '✏️ Edit' : '➕ Add' }} Student</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="saveStudent">
                            <div class="mb-3">
                                <label class="form-label">Name *</label>
                                <input type="text" class="form-control" v-model="studentForm.name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Student ID *</label>
                                <input type="text" class="form-control" v-model="studentForm.student_id" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Class *</label>
                                <select class="form-control" v-model="studentForm.class" required>
                                    <option value="">Select Class</option>
                                    <option v-for="cls in classes" :key="cls" :value="cls">Class {{ cls }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Section *</label>
                                <select class="form-control" v-model="studentForm.section" required>
                                    <option value="">Select Section</option>
                                    <option v-for="sec in sections" :key="sec" :value="sec">Section {{ sec }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Photo URL</label>
                                <input type="text" class="form-control" v-model="studentForm.photo" 
                                       placeholder="https://example.com/photo.jpg">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
                        <button type="button" class="btn btn-primary" @click="saveStudent" :disabled="saving">
                            {{ saving ? 'Saving...' : (editingStudent ? 'Update' : 'Save') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';

const students = ref({});
const loading = ref(false);
const saving = ref(false);
const showAddModal = ref(false);
const editingStudent = ref(null);

const filters = reactive({
    search: '',
    class: '',
    section: ''
});

const studentForm = reactive({
    name: '',
    student_id: '',
    class: '',
    section: '',
    photo: ''
});

const classes = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'];
const sections = ['A', 'B', 'C', 'D'];

// Methods
const loadStudents = async (page = 1) => {
    try {
        loading.value = true;
        console.log('Loading students...');
        
        const params = new URLSearchParams({
            page: page,
            ...filters
        });

        const response = await axios.get(`/api/students?${params}`);
        console.log('Students API Response:', response.data);
        
        // Check if response is HTML (error)
        if (typeof response.data === 'string' && response.data.includes('<!DOCTYPE html>')) {
            throw new Error('API returned HTML instead of JSON. Check if /api/students route exists.');
        }
        
        // Handle response format
        if (response.data.data) {
            students.value = {
                data: response.data.data,
                meta: response.data.meta,
                links: response.data.links
            };
        } else {
            students.value = {
                data: response.data,
                meta: {},
                links: {}
            };
        }
        
    } catch (error) {
        console.error('Error loading students:', error);
        alert('Error loading students: ' + error.message);
        students.value = { data: [], meta: {}, links: {} };
    } finally {
        loading.value = false;
    }
};

const debounceLoadStudents = (() => {
    let timeout;
    return () => {
        clearTimeout(timeout);
        timeout = setTimeout(() => loadStudents(), 500);
    };
})();

const getPercentageClass = (percentage) => {
    if (percentage >= 80) return 'text-success fw-bold';
    if (percentage >= 60) return 'text-warning';
    return 'text-danger';
};

const getStatusClass = (status) => {
    const baseClass = 'badge';
    switch (status) {
        case 'present': return baseClass + ' bg-success';
        case 'absent': return baseClass + ' bg-danger';
        case 'late': return baseClass + ' bg-warning';
        default: return baseClass + ' bg-secondary';
    }
};

const editStudent = (student) => {
    editingStudent.value = student;
    Object.assign(studentForm, student);
    showAddModal.value = true;
};

const saveStudent = async () => {
    try {
        saving.value = true;
        
        if (editingStudent.value) {
            await axios.put(`/api/students/${editingStudent.value.id}`, studentForm);
        } else {
            await axios.post('/api/students', studentForm);
        }
        
        closeModal();
        await loadStudents();
        alert('Student saved successfully!');
    } catch (error) {
        console.error('Error saving student:', error);
        alert('Error saving student: ' + (error.response?.data?.message || 'Unknown error'));
    } finally {
        saving.value = false;
    }
};

const deleteStudent = async (studentId) => {
    if (confirm('Are you sure you want to delete this student?')) {
        try {
            await axios.delete(`/api/students/${studentId}`);
            await loadStudents();
            alert('Student deleted successfully!');
        } catch (error) {
            console.error('Error deleting student:', error);
            alert('Error deleting student');
        }
    }
};

const closeModal = () => {
    showAddModal.value = false;
    editingStudent.value = null;
    Object.assign(studentForm, {
        name: '',
        student_id: '',
        class: '',
        section: '',
        photo: ''
    });
};

// Lifecycle
onMounted(() => {
    loadStudents();
});
</script>