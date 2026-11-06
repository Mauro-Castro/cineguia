@echo off
cd /d "%~dp0"
where php >nul 2>&1
if errorlevel 1 (
    echo PHP nao foi encontrado neste computador.
    echo Instale o XAMPP e copie esta pasta para C:\xampp\htdocs\filme
    echo Depois inicie o Apache e abra http://localhost/filme/index.php
    pause
    exit /b 1
)
echo CineGuia em http://localhost:8000/index.php
start "" "http://localhost:8000/index.php"
php -S localhost:8000
