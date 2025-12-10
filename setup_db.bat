@echo off
REM Database initialization script for Restaurant Management Web Application

echo Setting up database...
echo.

REM Navigate to project directory
cd /d "%~dp0"

REM Run PHP database initialization script
php -r "include 'db_init.php'; echo 'Database setup completed successfully!'; echo PHP_EOL;"

echo.
echo Database setup is complete!
echo You can now run the application with: php -S localhost:8000
pause
