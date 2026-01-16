# Production Setup Guide

## 🔧 API Configuration

The API configuration automatically detects the environment:

- **Production**: `https://blogs.indiapropertys.com/backend/api`
- **Development**: `http://localhost/backend/api`

## 🚨 Common Errors & Solutions

### Error: `ERR_CONNECTION_REFUSED`

**Cause:** Frontend can't connect to backend API

**Solutions:**

1. **Check Backend is Running**
   - Ensure PHP backend is accessible at: `https://blogs.indiapropertys.com/backend/api`
   - Test directly: Visit `https://blogs.indiapropertys.com/backend/api/blogs` in browser

2. **Check API URL Configuration**
   - Verify `frontend/src/config/api.js` has correct production URL
   - Current auto-detection should work, but you can override with:
   ```javascript
   export const API_BASE_URL = 'https://blogs.indiapropertys.com/backend/api';
   ```

3. **Check CORS Settings**
   - Update `backend/config/cors.php` to allow your domain:
   ```php
   $allowedOrigins = [
       'https://blogs.indiapropertys.com',
       'https://www.blogs.indiapropertys.com',
   ];
   ```

4. **Check Backend Server**
   - Ensure Apache/Nginx is running
   - Check `.htaccess` is working
   - Verify PHP is enabled

### Error: `Failed to fetch`

**Cause:** Network/CORS issue

**Solutions:**
1. Check browser console for CORS errors
2. Verify backend CORS headers are set correctly
3. Check if backend is accessible from frontend domain

## 📋 Production Checklist

- [ ] Backend deployed and accessible
- [ ] Database credentials configured in `.env`
- [ ] CORS configured for production domain
- [ ] API base URL points to production
- [ ] Frontend built and deployed
- [ ] All API endpoints tested
- [ ] Image uploads directory has write permissions
- [ ] `.htaccess` files are in place

## 🔍 Testing

### Test Backend API

```bash
# Test blogs endpoint
curl https://blogs.indiapropertys.com/backend/api/blogs

# Test categories
curl https://blogs.indiapropertys.com/backend/api/categories
```

### Test from Browser Console

```javascript
// Test API connection
fetch('https://blogs.indiapropertys.com/backend/api/blogs')
  .then(res => res.json())
  .then(data => console.log('Success:', data))
  .catch(err => console.error('Error:', err));
```

## 🌐 Domain Configuration

If your backend is on a different domain/subdomain:

1. Update `frontend/src/config/api.js`:
   ```javascript
   export const API_BASE_URL = 'https://api.indiapropertys.com/api';
   ```

2. Update CORS in `backend/config/cors.php`:
   ```php
   $allowedOrigins = [
       'https://blogs.indiapropertys.com',
   ];
   ```

## 📝 Environment Variables

For React apps, you can use environment variables:

1. Create `.env.production` in `frontend/`:
   ```
   REACT_APP_API_URL=https://blogs.indiapropertys.com/backend/api
   ```

2. Create `.env.development` in `frontend/`:
   ```
   REACT_APP_API_URL=http://localhost/backend/api
   ```

3. Rebuild frontend:
   ```bash
   npm run build
   ```
