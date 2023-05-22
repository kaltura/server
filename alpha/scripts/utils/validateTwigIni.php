<?php
ini_set("memory_limit","256M");

require_once(__DIR__ . '/../bootstrap.php');

for ($i=1; $i < $argc; $i++) { 
    $filename = $argv[$i];

    KalturaLog::info("Validating [$filename]");

    $tmpFileName = kConf::get("cache_root_path") . '/twig/' . $filename;
    @unlink($tmpFileName);
    try {
        IniUtils::parseIniFile( $filename, true );
    } catch (\Throwable $th) {
        KalturaLog::crit("Validation failed on [$filename] with error [$th]");
        KalturaLog::info('Faulty ini content:' . PHP_EOL . file_get_contents($filename) . PHP_EOL);
        exit(1);
    }
    
    print_r(file_get_contents($tmpFileName) . PHP_EOL);
}
KalturaLog::info('File validation was successful');
