 @echo off
    for /f "tokens=1-3 delims=/ " %%i in ("%date%") do (
      set day=%%i
      set month=%%j
      set year=%%k
   )
  
set datestr=%day%_%month%_%year%
echo datestr is %datestr%
set BACKUP_FILE="C:/Google Drive/mibase_%datestr%.backup"
SET PGPASSWORD=123456
echo on
pg_dump -i -h localhost -p 5432 -U postgres -F c -b -v -f %BACKUP_FILE% siscamp
SET PGPASSWORD=
