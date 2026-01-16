/**
 * API Configuration
 * 
 * Update the API_BASE_URL based on your environment
 */

// Auto-detect production vs development
const getApiBaseUrl = () => {
  // Check if we're in production (hosted domain)
  if (typeof window !== 'undefined') {
    const hostname = window.location.hostname;
    
    // Production domain
    if (hostname === 'blogs.indiapropertys.com' || hostname.includes('indiapropertys.com')) {
      return 'https://blogs.indiapropertys.com/backend/api';
    }
    
    // Development (localhost)
    if (hostname === 'localhost' || hostname === '127.0.0.1') {
      return 'http://localhost/backend/api';
    }
  }
  
  // Fallback: use environment variable or default
  return process.env.REACT_APP_API_URL || 'http://localhost/backend/api';
};

export const API_BASE_URL = getApiBaseUrl();

export default {
  baseURL: API_BASE_URL,
  timeout: 10000, // 10 seconds
  headers: {
    'Content-Type': 'application/json',
  },
};
