@echo off
setlocal

rem This batch file is runnable over Win10.

rem get version
for /f "delims=" %%a in (version.txt) do (
	set "version=%%a"
)

echo VERSION
echo %version%

rem set output path
set "outpath=.\trunk\%version%"
mkdir %outpath%\

copy *.md %outpath%\
copy *.txt %outpath%\
copy *.php %outpath%\

cd %outpath%

rem create zip
set "zipfile=..\..\release\copya-%version%.zip"
del %zipfile%

tar -a -c -f %zipfile% *
dir %zipfile%

endlocal
pause
echo on
