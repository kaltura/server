<?php
/**
 * @package deployment
 * @subpackage base.permissions
 *
 * Adds all system default permissions
 */

chdir(__DIR__);
require_once('../../bootstrap.php');

$criteria = new Criteria();
$criteria->add(entryPeer::PARTNER_ID, 99);
$templateEntries = entryPeer::doCount($criteria);
if($templateEntries)
{
	KalturaLog::info("Content already ingested.");
	exit(0);
}

$dirPath = __DIR__ . '/init_content';
$scriptPath = realpath(__DIR__ . '/../../../') . '/tests/standAloneClient/test.php';

KalturaLog::info("Adding content from directory [$dirPath]");
$dir = dir($dirPath);
/* @var $dir Directory */

$returnValue = null;
while (false !== ($fileName = $dir->read()))
{
	$filePath = realpath("$dirPath/$fileName");
	if($fileName[0] == '.' || is_dir($filePath) || preg_match('/template.xml$/', $fileName))
		continue;

	KalturaLog::info("Adding content from file [$filePath]");
	passthru("php $scriptPath $filePath", $returnValue);
	if($returnValue !== 0)
		exit(-1);
}