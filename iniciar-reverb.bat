@echo off
title Servidor WebSocket - Laravel Reverb (Inventario)
echo ========================================================
echo  Iniciando Servidor WebSocket (Laravel Reverb)
echo  Puerto: 8080 - Host: 0.0.0.0
echo ========================================================
php artisan reverb:start --host=0.0.0.0 --port=8080 --debug
pause
