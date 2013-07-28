<?php
/**
 * @package deployment
 * @subpackage base.permissions
 *
 * Adds all system default permissions
 */

chdir(__DIR__);
require_once(__DIR__ . '/../../bootstrap.php');

myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_MASTER;

$criteria = new Criteria();
$criteria->add(entryPeer::PARTNER_ID, 99);
$templateEntries = entryPeer::doCount($criteria);
if($templateEntries)
{
	KalturaLog::info("Content already ingested.");
	exit(0);
}

$dirPath = __DIR__ . '/init_content';
$scriptPath = realpath(__DIR__ . '/../../../') . '/tests/standAloneClient/exec.php';

KalturaLog::info("Adding content from directory [$dirPath]");
$dir = dir($dirPath);
/* @var $dir Directory */


$fileNames = array();
while (false !== ($fileName = $dir->read()))
{
	$filePath = realpath("$dirPath/$fileName");
	if($fileName[0] == '.' || is_dir($filePath) || preg_match('/template.xml$/', $fileName))
		continue;

	$fileNames[] = $fileName;
}
$dir->close();

sort($fileNames);
KalturaLog::info("Handling files [" . print_r($fileNames, true) . "]");


$returnValue = null;
foreach($fileNames as $fileName)
{
	$filePath = realpath("$dirPath/$fileName");
	KalturaLog::info("Adding content from file [$filePath]");
	passthru("php $scriptPath $filePath", $returnValue);
	if($returnValue !== 0)
		exit(-1);
}


