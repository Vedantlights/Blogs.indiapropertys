# Database Configuration

## Current Database Settings

The database credentials are configured in `backend/.env`:

```
DB_HOST=localhost
DB_NAME=u449667423_Blogs
DB_USER=u449667423_sneha
DB_PASS=Blogs@2026
DB_CHARSET=utf8mb4
```

## How It Works

The `backend/config/database.php` file automatically loads these credentials from the `.env` file.

## Testing Connection

To test if the database connection works, you can:

1. **Via PHP:**
   ```php
   require_once 'config/database.php';
   try {
       $pdo = getDBConnection();
       echo "Database connected successfully!";
   } catch (Exception $e) {
       echo "Error: " . $e->getMessage();
   }
   ```

2. **Via API:**
   - Try accessing any API endpoint
   - Check server error logs if connection fails

## Important Notes

- The `.env` file is in `.gitignore` (not tracked by git)
- Never commit database credentials to version control
- For production, use environment variables or secure credential storage

## Troubleshooting

### Connection Failed

1. **Check database exists:**
   ```sql
   SHOW DATABASES LIKE 'u449667423_Blogs';
   ```

2. **Check user permissions:**
   ```sql
   SHOW GRANTS FOR 'u449667423_sneha'@'localhost';
   ```

3. **Check MySQL service is running**

4. **Verify credentials:**
   - Database name: `u449667423_Blogs`
   - Username: `u449667423_sneha`
   - Password: `Blogs@2026`

### Remote Database

If your database is on a remote server, update `DB_HOST` in `.env`:
```
DB_HOST=your-remote-host.com
```

## Security

- ✅ `.env` file is excluded from git
- ✅ Credentials are not hardcoded
- ✅ Database connection uses PDO prepared statements
- ⚠️ Ensure `.env` file has proper file permissions (not world-readable)
