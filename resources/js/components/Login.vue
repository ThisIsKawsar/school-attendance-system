<template>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Login to School Attendance System</h4>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="login">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input 
                                    type="email" 
                                    class="form-control" 
                                    id="email" 
                                    v-model="form.email" 
                                    required
                                    placeholder="Enter your email"
                                >
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="password" 
                                    v-model="form.password" 
                                    required
                                    placeholder="Enter your password"
                                >
                            </div>
                            <button 
                                type="submit" 
                                class="btn btn-primary w-100" 
                                :disabled="loading"
                            >
                                {{ loading ? 'Logging in...' : 'Login' }}
                            </button>
                        </form>
                        
                        <div v-if="error" class="alert alert-danger mt-3">
                            {{ error }}
                        </div>
                        
                        <div class="mt-3 text-center">
                            <small><strong>Demo Credentials:</strong> admin@school.com / password</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { auth } from '../auth.js';

export default {
    name: 'Login',
    data() {
        return {
            form: {
                email: 'admin@school.com',
                password: 'password'
            },
            loading: false,
            error: null
        }
    },
    methods: {
        async login() {
            try {
                this.loading = true;
                this.error = null;
                
                await auth.login(this.form);
                this.$router.push('/');
                
            } catch (error) {
                this.error = error.message || 'Login failed. Please check your credentials.';
            } finally {
                this.loading = false;
            }
        }
    },
    mounted() {
        // If user is already authenticated, redirect to dashboard
        if (auth.isAuthenticated()) {
            this.$router.push('/');
        }
    }
}
</script>

<style scoped>
.card {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: none;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.alert {
    border: none;
    border-radius: 0.375rem;
}
</style>