del /F /S /Q ..\cache\api_v3\*
del /F /S /Q ..\cache\generator\*

php generate.php

xcopy /Y /S /R output\adminConsoleClient\* ..\admin_console\lib\Kaltura
xcopy /Y /S /R output\batchClient\* ..\batch\client
xcopy /Y /S /R output\php5full\* ..\tests\unit_test\lib