del /F /S /Q ..\cache\api_v3\*
del /F /S /Q ..\cache\generator\*

php generate.php

copy /Y output\adminConsoleClient\* ..\admin_console\lib\Kaltura
copy /Y output\batchClient\* ..\batch\client
copy /Y output\php5full\* ..\tests\unit_test\lib