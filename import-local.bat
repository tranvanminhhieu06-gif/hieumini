@echo off
chcp 65001 >nul
setlocal EnableDelayedExpansion

set "SQLFILE=%~dp0database\tidb_all.sql"
set "MYSQLEXE=C:\xampp\mysql\bin\mysql.exe"

echo.
echo ==========================================================
echo   HieuMini - Nap TAT CA co so du lieu vao MySQL noi bo
echo   (XAMPP tren may nay - localhost:3306)
echo ==========================================================
echo.
echo   Se tao 7 co so du lieu:
echo     hieumini_portfolio      (cong trung bay HieuMini)
echo     hieumini_db             (HieuWeb01 - thoi trang)
echo     hieumini_bookstore_db   (HieuWeb02 - cong nghe)
echo     hieumini_furniture_db   (HieuWeb03 - hoc tap)
echo     datcyber_appliances_db  (HieuWeb04 - gia dung)
echo     hieumini_gym_db         (HieuWeb05 - the hinh)
echo     hieumini_market_db      (HieuWeb06 - cho ma nguon)
echo.

REM ---------- Kiem tra tep SQL ----------
if not exist "%SQLFILE%" (
    echo [LOI] Khong tim thay: %SQLFILE%
    echo.
    pause
    exit /b 1
)
for %%A in ("%SQLFILE%") do echo   Tep SQL : %%~nxA (%%~zA byte)

REM ---------- Tim mysql.exe ----------
if not exist "%MYSQLEXE%" (
    where mysql.exe >nul 2>&1
    if errorlevel 1 (
        echo.
        echo [LOI] Khong tim thay mysql.exe tai C:\xampp\mysql\bin\
        echo Neu cai XAMPP o o dia khac, mo tep .bat nay bang Notepad va sua
        echo dong "set MYSQLEXE=" cho dung duong dan.
        echo.
        pause
        exit /b 1
    ) else (
        set "MYSQLEXE=mysql.exe"
    )
)
echo   Cong cu : %MYSQLEXE%
echo.

REM ---------- Hoi mat khau root (XAMPP mac dinh de trong) ----------
echo Nhap mat khau MySQL root cua XAMPP.
echo (Mac dinh XAMPP de TRONG - chi can bam Enter)
echo.
set "DBPASS="
set /p "DBPASS=Mat khau MySQL root: "

echo.
echo Dang nap du lieu... vui long cho khoang 1 phut.
echo.

if "%DBPASS%"=="" (
    "%MYSQLEXE%" -u root --default-character-set=utf8mb4 < "%SQLFILE%"
) else (
    "%MYSQLEXE%" -u root -p"%DBPASS%" --default-character-set=utf8mb4 < "%SQLFILE%"
)

if errorlevel 1 goto :fail

echo.
echo ==========================================================
echo   NAP DU LIEU THANH CONG
echo ==========================================================
echo.
echo Bay gio ca 6 du an con deu chay duoc tren:
echo   http://localhost/HieuWebsite/
echo.
echo Tai khoan quan tri demo (dung chung ca 6 du an):
echo   Email    : admin@hieumini.vn
echo   Mat khau : demo123
echo.
pause
exit /b 0

:fail
echo.
echo ==========================================================
echo   NAP DU LIEU THAT BAI
echo ==========================================================
echo.
echo Kiem tra:
echo   1. MySQL trong XAMPP da bat chua (o mau xanh)?
echo   2. Mat khau root co dung khong?
echo   3. Neu cong 3306 bi doi, sua trong tep .bat: them -P ^<cong^>
echo.
pause
exit /b 1
