<?php

require_once('utils.php');

chdir(dirname(__file__));

$fileList = listDir(dirname(__file__));
foreach ($fileList as $file)
{
	if(endsWith($file, '.tar.gz'))
		executeCommand('tar', "-zxvf {$file}");		
}
