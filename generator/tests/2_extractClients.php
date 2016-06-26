<?php

require_once(__DIR__ . '/utils.php');

if ($argc < 2)
	die("Usage:\n\tphp " . basename(__file__) . " <root dir>\n");
	
$rootDir = fixSlashes($argv[1]);

chdir($rootDir);

$fileList = listDir($rootDir);
foreach ($fileList as $file)
{
	if(endsWith($file, '.tar.gz'))
		executeCommand('tar', "-zxvf {$file}");		
}
