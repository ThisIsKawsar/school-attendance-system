import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';
import Dashboard from './components/Dashboard.vue';
import StudentList from './components/StudentList.vue';
import AttendanceRecording from './components/AttendanceRecording.vue';
import Login from './components/Login.vue';
import { auth } from './auth.js';

const routes = [
    { path: '/login', component: Login, meta: { public: true } },
    { path: '/', component: Dashboard, meta: { requiresAuth: true } },
    { path: '/students', component: StudentList, meta: { requiresAuth: true } },
    { path: '/attendance', component: AttendanceRecording, meta: { requiresAuth: true } }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// Auth guard
router.beforeEach((to, from, next) => {
    const isAuthenticated = auth.isAuthenticated();
    
    if (to.meta.requiresAuth && !isAuthenticated) {
        next('/login');
    } else if (to.meta.public && isAuthenticated) {
        next('/');
    } else {
        next();
    }
});

const app = createApp(App);
app.use(router);
app.mount('#app');