<template>
    <div id="app">
        <nav v-if="isAuthenticated" class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="/">🏫 School Attendance System</a>
                <div class="navbar-nav me-auto">
                    <router-link to="/" class="nav-link">Dashboard</router-link>
                    <router-link to="/students" class="nav-link">Students</router-link>
                    <router-link to="/attendance" class="nav-link">Attendance</router-link>
                </div>
                <div class="navbar-nav">
                    <span class="navbar-text me-3">Welcome, {{ user?.name }}</span>
                    <button class="btn btn-outline-light btn-sm" @click="logout">Logout</button>
                </div>
            </div>
        </nav>

        <router-view></router-view>
    </div>
</template>

<script>
import { auth } from './auth.js';

export default {
    name: 'App',
    data() {
        return {
            isAuthenticated: auth.isAuthenticated(),
            user: auth.getUser()
        }
    },
    methods: {
        async logout() {
            await auth.logout();
            this.isAuthenticated = false;
            this.user = null;
            this.$router.push('/login');
        }
    },
    mounted() {
        // Check authentication on app load
        if (!this.isAuthenticated && this.$route.path !== '/login') {
            this.$router.push('/login');
        }
    }
}
</script>

<style>
.router-link-active {
    font-weight: bold;
    background: rgba(255,255,255,0.1);
}
</style>