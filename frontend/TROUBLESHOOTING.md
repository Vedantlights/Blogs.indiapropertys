# Troubleshooting Guide

## 🔴 Error: `ERR_CONNECTION_REFUSED`

### Problem
```
Failed to load resource: net::ERR_CONNECTION_REFUSED
localhost/backend/api/blogs:1
```

### Causes & Solutions

#### 1. **Backend Not Running**
**Solution:** Ensure your PHP backend server is running and accessible.

**Check:**
- Visit `https://blogs.indiapropertys.com/backend/api/blogs` directly in browser
- Should return JSON, not 404 or connection error

#### 2. **Wrong API URL**
**Solution:** The frontend is trying to connect to `localhost` from production.

**Fixed:** Updated `frontend/src/config/api.js` to auto-detect production domain.

**Manual Override:**
```javascript
// In frontend/src/config/api.js
export const API_BASE_URL = 'https://blogs.indiapropertys.com/backend/api';
```

#### 3. **Backend Path Incorrect**
**Solution:** Verify backend is at `/backend/` path on your server.

**Test:**
```bash
curl https://blogs.indiapropertys.com/backend/api/blogs
```

#### 4. **CORS Issues**
**Solution:** Backend CORS updated to allow your domain.

**Verify:** Check `backend/config/cors.php` includes:
```php
'https://blogs.indiapropertys.com',
```

## 🔍 Debugging Steps

### Step 1: Test Backend Directly

Open browser and visit:
```
https://blogs.indiapropertys.com/backend/api/blogs
```

**Expected:** JSON response with blogs
**If Error:** Backend is not accessible

### Step 2: Check Browser Console

Open DevTools (F12) → Console tab

**Look for:**
- CORS errors
- Network errors
- API URL being used

### Step 3: Check Network Tab

Open DevTools (F12) → Network tab

**Check:**
- Request URL
- Response status
- Response headers (CORS headers)

### Step 4: Verify API Configuration

Check `frontend/src/config/api.js`:
```javascript
console.log('API Base URL:', API_BASE_URL);
```

Should show: `https://blogs.indiapropertys.com/backend/api`

## 🛠️ Quick Fixes

### Fix 1: Update API URL Manually

Edit `frontend/src/config/api.js`:
```javascript
export const API_BASE_URL = 'https://blogs.indiapropertys.com/backend/api';
```

Then rebuild:
```bash
cd frontend
npm run build
```

### Fix 2: Check Backend Deployment

Ensure:
- ✅ Backend files are uploaded to server
- ✅ `.htaccess` is in place
- ✅ PHP is enabled
- ✅ Database credentials are correct

### Fix 3: Check Server Configuration

**Apache:**
- `mod_rewrite` enabled
- `.htaccess` allowed

**Nginx:**
- Proper rewrite rules configured

## 📞 Common Issues

### Issue: API works in browser but not from frontend

**Cause:** CORS or same-origin policy

**Solution:** 
1. Check CORS headers in response
2. Verify `backend/config/cors.php` is loaded
3. Check browser console for CORS errors

### Issue: 404 Not Found

**Cause:** Backend routing not working

**Solution:**
1. Check `.htaccess` file exists
2. Verify `mod_rewrite` is enabled
3. Check `backend/index.php` routing

### Issue: 500 Internal Server Error

**Cause:** PHP error or database connection issue

**Solution:**
1. Check PHP error logs
2. Verify database credentials in `.env`
3. Check database connection

## ✅ Verification Checklist

- [ ] Backend accessible at: `https://blogs.indiapropertys.com/backend/api`
- [ ] API returns JSON (not HTML error page)
- [ ] CORS headers present in response
- [ ] Frontend API URL points to production
- [ ] Database connection working
- [ ] `.htaccess` files in place
- [ ] PHP errors logged (check logs)

## 🆘 Still Having Issues?

1. **Check Server Logs:**
   - PHP error logs
   - Apache/Nginx error logs
   - Browser console errors

2. **Test API Endpoints:**
   ```bash
   # Test blogs
   curl https://blogs.indiapropertys.com/backend/api/blogs
   
   # Test categories
   curl https://blogs.indiapropertys.com/backend/api/categories
   ```

3. **Verify File Structure:**
   ```
   backend/
   ├── api/
   ├── config/
   ├── controllers/
   ├── index.php
   └── .htaccess
   ```
