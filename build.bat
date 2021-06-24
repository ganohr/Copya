@echo off
setlocal

for /f "delims=" %%a in (version.txt) do (
	set "version=%%a"
)

echo VERSION
echo %version%

set "outpath=.\trunk\%version%"
mkdir %outpath%\

rem copy *.md %outpath%\
copy *.txt %outpath%\
copy *.css %outpath%\
copy *.php %outpath%\

cd %outpath%

set "zipfile_ver=..\..\release\copya-%version%.zip"
del %zipfile_ver%

tar -a -c -f %zipfile_ver% *

set "zipfile=..\..\release\copya.zip"
del %zipfile%

copy %zipfile_ver% %zipfile%

endlocal
rem pause
echo on
