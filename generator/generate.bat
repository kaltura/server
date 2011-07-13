del /F /S /Q ..\cache\api_v3\*
del /F /S /Q ..\cache\generator\*

php generate.php %*

xcopy /Y /S /R C:\web\content\clientlibs\php5ZendClientAdminConsole\* ..\admin_console\lib
xcopy /Y /S /R C:\web\content\clientlibs\batchClient\* ..\batch\client
xcopy /Y /S /R C:\web\content\clientlibs\testsClient\* ..\tests\lib

del /F /S /Q ..\cache\batch\*
