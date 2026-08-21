@echo off
chcp 65001 >nul
setlocal EnableDelayedExpansion

set "TIDB_HOST=gateway01.ap-southeast-1.prod.aws.tidbcloud.com"
set "TIDB_PORT=4000"
set "TIDB_USER=4WXwYbGLHmDTbak.root"
set "SQLFILE=%~dp0database\tidb_all.sql"
set "MYSQLEXE=C:\xampp\mysql\bin\mysql.exe"

echo.
echo ==========================================================
echo   HieuMini - Nap 7 co so du lieu len TiDB Cloud
echo ==========================================================
echo.
echo   May chu : %TIDB_HOST%
echo   Cong    : %TIDB_PORT%
echo   Nguoi dung: %TIDB_USER%
echo.

REM ---------- Kiem tra tep SQL ----------
if not exist "%SQLFILE%" (
    echo [LOI] Khong tim thay tep: %SQLFILE%
    echo.
    pause
    exit /b 1
)
for %%A in ("%SQLFILE%") do echo   Tep SQL : %%~nxA (%%~zA byte)
echo.

REM ---------- Tim mysql.exe ----------
if not exist "%MYSQLEXE%" (
    where mysql.exe >nul 2>&1
    if errorlevel 1 (
        echo [LOI] Khong tim thay mysql.exe
        echo.
        echo Mac dinh script tim tai: C:\xampp\mysql\bin\mysql.exe
        echo Neu ban cai XAMPP o o dia khac, hay mo tep .bat nay bang Notepad
        echo va sua dong "set MYSQLEXE=" cho dung duong dan tren may ban.
        echo.
        pause
        exit /b 1
    ) else (
        set "MYSQLEXE=mysql.exe"
    )
)
echo   Cong cu : %MYSQLEXE%
echo.

REM ---------- Nhap mat khau ----------
echo Dan mat khau TiDB (lay tu nut "Generate Password" tren TiDB Console).
echo Meo: bam chuot phai trong cua so nay de dan.
echo.
set /p "TIDB_PASS=Mat khau TiDB: "

if "%TIDB_PASS%"=="" (
    echo.
    echo [LOI] Ban chua nhap mat khau.
    echo.
    pause
    exit /b 1
)

echo.
echo Dang nap du lieu... Qua trinh nay mat khoang 1-2 phut, vui long cho.
echo.

REM XAMPP dung MariaDB client: dung tham so --ssl (khong phai --ssl-mode)
"%MYSQLEXE%" -h %TIDB_HOST% -P %TIDB_PORT% -u %TIDB_USER% -p"%TIDB_PASS%" ^
    --ssl --default-character-set=utf8mb4 < "%SQLFILE%"

if errorlevel 1 goto :fail

echo.
echo ==========================================================
echo   NAP DU LIEU THANH CONG
echo ==========================================================
echo.
echo Dang kiem tra lai so bang trong tung co so du lieu...
echo.

"%MYSQLEXE%" -h %TIDB_HOST% -P %TIDB_PORT% -u %TIDB_USER% -p"%TIDB_PASS%" ^
    --ssl --default-character-set=utf8mb4 --table -e ^
    "SELECT table_schema AS 'Co so du lieu', COUNT(*) AS 'So bang' FROM information_schema.tables WHERE table_schema IN ('hieumini_portfolio','hieumini_db','hieumini_bookstore_db','hieumini_furniture_db','datcyber_appliances_db','hieumini_gym_db','hieumini_market_db') GROUP BY table_schema ORDER BY table_schema;"

echo.
echo Ket qua dung phai la 7 dong, tong cong 55 bang:
echo   hieumini_portfolio      4
echo   hieumini_db             7
echo   hieumini_bookstore_db   8
echo   hieumini_furniture_db   8
echo   datcyber_appliances_db  8
echo   hieumini_gym_db         8
echo   hieumini_market_db     12
echo.
echo Buoc tiep theo: mo DEPLOY.md, lam theo Buoc 3 de day ma nguon len GitHub.
echo.
pause
exit /b 0

:fail
echo.
echo ==========================================================
echo   NAP DU LIEU THAT BAI
echo ==========================================================
echo.
echo Cac nguyen nhan thuong gap:
echo.
echo   1. Sai mat khau
echo      Vao TiDB Console bam "Generate Password" tao mat khau moi.
echo.
echo   2. IP bi chan
echo      TiDB Console - Networking - bat "Allow access from anywhere".
echo.
echo   3. Tuong lua chan cong 4000
echo      Thu tat tam thoi tuong lua hoac dung mang khac.
echo      Hoac dung Cach A trong DEPLOY.md (dan SQL vao SQL Editor tren web).
echo.
echo   4. Loi "unknown option --ssl" hoac loi TLS:
echo      Client MariaDB cu co the khong bat duoc TLS toi TiDB. Khi do
echo      hay dung TiDB SQL Editor tren web: mo tep database\tidb_all.sql
echo      bang Notepad, sao chep toan bo, dan vao SQL Editor va bam Run.
echo.
pause
exit /b 1
