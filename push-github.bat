@echo off
chcp 65001 >nul
setlocal EnableDelayedExpansion

set "REPO=https://github.com/tranvanminhhieu06-gif/hieumini.git"

echo.
echo ==========================================================
echo   HieuMini - Day ma nguon len GitHub
echo   Repo: %REPO%
echo ==========================================================
echo.

cd /d "%~dp0"
echo Thu muc lam viec: %CD%
echo.

REM ---------- Kiem tra Git ----------
git --version >nul 2>&1
if errorlevel 1 (
    echo [LOI] Chua cai Git tren may nay.
    echo.
    echo Tai ve tai: https://git-scm.com/download/win
    echo Cai xong, mo lai tep nay.
    echo.
    pause
    exit /b 1
)

REM ---------- Canh bao neu thieu .gitignore ----------
if not exist ".gitignore" (
    echo [CANH BAO] Khong tim thay .gitignore
    echo Tiep tuc co the day nham tep nhay cam len GitHub.
    echo.
    choice /c YN /m "Van tiep tuc"
    if errorlevel 2 exit /b 1
)

REM ---------- Khoi tao kho Git ----------
if not exist ".git" (
    echo [1/6] Khoi tao kho Git moi...
    git init
    if errorlevel 1 goto :fail
) else (
    echo [1/6] Da co kho Git, bo qua buoc khoi tao.
)

REM ---------- Dat remote ----------
echo [2/6] Cau hinh remote origin...
git remote get-url origin >nul 2>&1
if errorlevel 1 (
    git remote add origin %REPO%
) else (
    git remote set-url origin %REPO%
)
if errorlevel 1 goto :fail

REM ---------- Dat nhanh main ----------
echo [3/6] Chuyen sang nhanh main...
git branch -M main
if errorlevel 1 goto :fail

REM ---------- Them tep ----------
echo [4/6] Them tep vao vung chuan bi...
git add -A
if errorlevel 1 goto :fail

REM ---------- Kiem tra ro ri mat khau ----------
echo [5/6] Kiem tra ro ri thong tin nhay cam...
git diff --cached --name-only | findstr /i /c:".env" /c:"password" /c:"secret" >nul 2>&1
if not errorlevel 1 (
    echo.
    echo [CANH BAO] Co tep ten giong tep chua thong tin nhay cam:
    git diff --cached --name-only | findstr /i /c:".env" /c:"password" /c:"secret"
    echo.
    choice /c YN /m "Van muon day len GitHub"
    if errorlevel 2 (
        echo Da huy. Hay them cac tep tren vao .gitignore roi chay lai.
        pause
        exit /b 1
    )
)

REM ---------- Commit ----------
echo [6/6] Tao commit va day len GitHub...
git diff --cached --quiet
if not errorlevel 1 (
    echo Khong co thay doi nao moi de commit.
) else (
    git commit -m "HieuMini: cong trung bay 6 du an website PHP MySQL"
    if errorlevel 1 goto :fail
)

echo.
echo Dang day len GitHub. Lan dau Git se hoi dang nhap tai khoan GitHub.
echo.
git push -u origin main
if errorlevel 1 goto :pushfail

echo.
echo ==========================================================
echo   THANH CONG
echo ==========================================================
echo.
echo Xem ma nguon tai:
echo   https://github.com/tranvanminhhieu06-gif/hieumini
echo.
echo Buoc tiep theo: mo DEPLOY.md, lam theo Buoc 4 de tao dich vu tren Render.
echo.
pause
exit /b 0

:pushfail
echo.
echo [LOI] Day len that bai.
echo.
echo Cac nguyen nhan thuong gap:
echo   - Dang nhap sai tai khoan GitHub
echo   - Repo tren GitHub da co san commit khac. Thu lenh:
echo       git pull --rebase origin main
echo       git push -u origin main
echo   - Repo chua duoc tao. Vao github.com tao repo ten "hieumini" truoc.
echo.
pause
exit /b 1

:fail
echo.
echo [LOI] Lenh Git that bai o buoc tren. Xem thong bao loi de biet chi tiet.
echo.
pause
exit /b 1
