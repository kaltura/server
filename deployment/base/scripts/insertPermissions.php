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

$returnValue = null;
while (false !== ($fileName = $dir->read()))
{
	$filePath = realpath("$dirPath/$fileName");
	if($fileName[0] == '.')
		continue;

	KalturaLog::info("Adding permissions from file [$filePath]");
	passthru("php $scriptPath $filePath", $returnValue);
	if($returnValue !== 0)
		exit(-1);
}