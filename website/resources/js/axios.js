import axios from 'axios';
const axiosInstance = axios.create({
  baseURL: import.meta.env.MODE === 'development' ? 'http://localhost:8000' : 'https://lizc39.sg-host.com',
  withCredentials: true, // Include HttpOnly cookies
});

export default axiosInstance;
