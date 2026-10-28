@echo off
title Calculando a Sorte - Inicializador
color 0B
echo ========================================================
echo   Calculando a Sorte - Plataforma de Jogos Matematicos
echo ========================================================
echo.

where php >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [AVISO] O comando 'php' nao foi encontrado no PATH do Windows.
    echo.
    echo Se voce utiliza o XAMPP ou Laragon:
    echo 1. Certifique-se de que o Apache e o MySQL estao iniciados.
    echo 2. Mova a pasta 'Calculando a sorte' para 'C:\xampp\htdocs\'.
    echo 3. Acesse no seu navegador: http://localhost/Calculando%%20a%%20sorte/
    echo.
    echo Pressione qualquer tecla para abrir as instrucoes detalhadas no README.md...
    pause >nul
    start README.md
    exit /b
)

echo Iniciando servidor embutido do PHP...
echo Acesse no seu navegador: http://localhost:8000/
echo.
echo Para encerrar o servidor, feche esta janela ou pressione CTRL + C.
echo.
start http://localhost:8000/
php -S localhost:8000
