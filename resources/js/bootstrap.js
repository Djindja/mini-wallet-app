import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Add auth token from localStorage
const token = localStorage.getItem('authToken');
if (token) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}