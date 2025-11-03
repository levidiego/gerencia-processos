@echo off
echo ========================================================
echo   INICIANDO KILL AUTOMATICO DE PROCESSOS
echo ========================================================
echo.
echo Este script mantém o kill automatico ativo.
echo O sistema verificará processos bloqueadores a cada minuto.
echo.
echo Tempo Z configurado: 15 minutos (900 segundos)
echo.
echo Para parar, pressione CTRL+C
echo.
echo ========================================================
echo.

cd /d D:\FONTES_IA\gerencia-processos
php artisan schedule:run >> storage\logs\scheduler.log 2>&1

:loop
timeout /t 60 /nobreak > nul
php artisan schedule:run >> storage\logs\scheduler.log 2>&1
goto loop
