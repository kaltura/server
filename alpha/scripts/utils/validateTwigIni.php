<?php
ini_set("memory_limit","256M");

require_once(__DIR__ . '/../bootstrap.php');

$filename = $argv[1];
$tmpFileName = kConf::get("cache_root_path") . '/twig/' . $filename;
@unlink($tmpFileName);
IniUtils::parseIniFile( $filename, true );

print_r(file_get_contents($tmpFileName));
