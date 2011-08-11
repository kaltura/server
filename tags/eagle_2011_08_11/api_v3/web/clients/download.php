<?php
require_once("..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."bootstrap.php");
KalturaLog::setContext("CLIENTS");
KalturaLog::debug(__FILE__ . " start");
$requestedName = isset($_GET["name"]) ? $_GET['name'] : null;
if (!$requestedName)
	die("File not found");

$generatorOutputPath = KAutoloader::buildPath(KALTURA_ROOT_PATH, "generator", "output");
$generatorConfigPath = KAutoloader::buildPath(KALTURA_ROOT_PATH, "generator", "config.ini");
$config = new Zend_Config_Ini($generatorConfigPath);
foreach($config as $name => $item)
{
	if ($name === $requestedName && $item->get("public-download"))
	{
		$fileName = $name.".tar.gz";
		$outputFilePath = KAutoloader::buildPath($generatorOutputPath, $fileName);
		$outputFilePath = realpath($outputFilePath);
		header("Content-disposition: attachment; filename=$fileName");
		kFile::dumpFile($outputFilePath, "application/gzip");
		die;
	}
}
die("File not found");
KalturaLog::debug(__FILE__ . " end");