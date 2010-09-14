rm -fr ../api_v3/cache/*
rm -fr cache/*
php generate.php
cp output/adminConsoleClient/* ../admin_console/lib/Kaltura
cp output/batchClient/* ../batch/client