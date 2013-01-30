<?php
/**
 * @package deployment
 * @subpackage base.permissions
 * 
 * Adds all system default permissions
 */

chdir(__DIR__);
require_once('../../bootstrap.php');

$dirPath = realpath(__DIR__ . '/../../') . '/permissions';
$scriptPath = realpath(__DIR__ . '/../../../') . '/scripts/utils/permissions/addPermissionsAndItems.php';

KalturaLog::info("Adding permissions from directory [$dirPath]");
$dir = dir($dirPath);
/* @var $dir Directory */

$files = array();
$returnValue = null;
while (false !== ($fileName = $dir->read()))
{
	if($fileName[0] == '.')
		continue;

	$files[] = $fileName;
}

// execute first all the files that add permissions
foreach($files as $index => $fileName)
{
	$filePath = realpath("$dirPath/$fileName");
	if(!preg_match('/\[permissions\]/', file_get_contents($filePath)))
		continue;
	
	unset($files[$index]);
	KalturaLog::info("Adding permissions from file [$filePath]");
	passthru("php $scriptPath $filePath", $returnValue);
	if($returnValue !== 0)
		exit(-1);
}

// execute all other files
foreach($files as $index => $fileName)
{
	$filePath = realpath("$dirPath/$fileName");
	KalturaLog::info("Adding permissions from file [$filePath]");
	passthru("php $scriptPath $filePath", $returnValue);
	if($returnValue !== 0)
		exit(-1);
}
