import axios from 'axios';

class Auth {
    constructor() {
        this.token = localStorage.getItem('auth_token');
        this.user = JSON.parse(localStorage.getItem('auth_user') || 'null');
        
        // Set default authorization header if token exists
        if (this.token) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
        }
    }

    async login(credentials) {
        try {
            const response = await axios.post('/api/login', credentials);
            this.setAuth(response.data);
            return response.data;
        } catch (error) {
            throw error.response?.data || { message: 'Login failed' };
        }
    }

    async register(userData) {
        try {
            const response = await axios.post('/api/register', userData);
            this.setAuth(response.data);
            return response.data;
        } catch (error) {
            throw error.response?.data || { message: 'Registration failed' };
        }
    }

    async logout() {
        try {
            if (this.token) {
                await axios.post('/api/logout', {}, {
                    headers: { Authorization: `Bearer ${this.token}` }
                });
            }
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            this.clearAuth();
        }
    }

    setAuth(data) {
        this.token = data.token;
        this.user = data.user;
        
        localStorage.setItem('auth_token', data.token);
        localStorage.setItem('auth_user', JSON.stringify(data.user));
        
        // Set default authorization header
        axios.defaults.headers.common['Authorization'] = `Bearer ${data.token}`;
    }

    clearAuth() {
        this.token = null;
        this.user = null;
        
        localStorage.removeItem('auth_token');
        localStorage.removeItem('auth_user');
        
        delete axios.defaults.headers.common['Authorization'];
    }

    isAuthenticated() {
        return !!this.token;
    }

    getUser() {
        return this.user;
    }

    getToken() {
        return this.token;
    }
}

// Create and export auth instance
export const auth = new Auth();