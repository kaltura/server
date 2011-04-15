del /F /S /Q ..\cache\api_v3\*
del /F /S /Q ..\cache\generator\*

php generate.php

xcopy /Y /S /R C:\web\content\generator\output\php5ZendClientAdminConsole\* ..\admin_console\lib
xcopy /Y /S /R C:\web\content\generator\output\batchClient\* ..\batch\client