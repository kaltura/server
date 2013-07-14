<?php
/**
 * @package deployment
 * @subpackage base.permissions
 *
 * Adds all system default permissions
 * Arguments:
 *  d - Directory of permission files
 */

chdir(__DIR__);
require_once(__DIR__ . '/../../bootstrap.php');

$dirPath = realpath(__DIR__ . '/../../') . '/permissions';
$scriptPath = realpath(__DIR__ . '/../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$options = getopt('d:');
if(isset($options['d']))
{
	$dirPath = $options['d'];
	if(!file_exists($dirPath) || !is_dir($dirPath))
	{
		echo "Path [$dirPath] is not a valid dorectory";
		exit(-1);
	}
}

KalturaLog::info("Adding permissions from directory [$dirPath]");
$dir = dir($dirPath);
/* @var $dir Directory */

$files = array();
$returnValue = null;
while (false !== ($fileName = $dir->read()))
{
	if($fileName[0] == '.' || is_dir("$dirPath/$fileName"))
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
