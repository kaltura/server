<?php

define('KALTURA_ROOT_PATH', realpath(dirname(__FILE__) . '/../../../'));
require_once(KALTURA_ROOT_PATH . '/infra/bootstrap_base.php');
require_once(KALTURA_ROOT_PATH . '/infra/KAutoloader.php');

define("KALTURA_API_PATH", KALTURA_ROOT_PATH . "/api_v3");

// Autoloader
require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "lib", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "services", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "alpha", "plugins", "*")); // needed for testmeDoc
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "generator")); // needed for testmeDoc
KAutoloader::setClassMapFilePath(KAutoloader::buildPath(KALTURA_API_PATH, "cache", "KalturaClassMap.cache"));
//KAutoloader::dumpExtra();
KAutoloader::register();

// Timezone
date_default_timezone_set(kConf::get("date_default_timezone")); // America/New_York

error_reporting(E_ALL);
KalturaLog::setLogger(new KalturaStdoutLogger());

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();

kCurrentContext::$ps_vesion = 'ps3';

if($argc < 2)
{
	echo "Entry id must be supplied as attribute\n";
	exit;
}
$entryId = $argv[1];
$config = array();
foreach($argv as $arg)
{
	$matches = null;
	if(preg_match('/(.*)=(.*)/', $arg, $matches))
		$config[$matches[1]] = $matches[2];
}

$entry = entryPeer::retrieveByPK($entryId);
$mrss = kMrssManager::getEntryMrss($entry);

file_put_contents('entry.xml', $mrss);
//exit;

if(!$mrss)
	return;
	
$xml = new DOMDocument();
if(!$xml->loadXML($mrss))
	return;
	
$xslPath = dirname(__FILE__) . '/submit.xsl';
$xsl = new DOMDocument();
$xsl->load($xslPath);

$varNodes = $xsl->getElementsByTagName('variable');
foreach($varNodes as $varNode)
{
	$nameAttr = $varNode->attributes->getNamedItem('name');
	if(!$nameAttr)
		continue;
		
	$name = $nameAttr->value;
	if($name && isset($config[$name]))
	{
		$varNode->textContent = $config[$name];
		$varNode->appendChild($xsl->createTextNode($config[$name]));
		KalturaLog::debug("Set config $name to [" . $config[$name] . "]");
	}
}
file_put_contents('out.xsl', $xsl->saveXML());

$proc = new XSLTProcessor;
$proc->registerPHPFunctions();
$proc->importStyleSheet($xsl);

$xml = $proc->transformToDoc($xml);
if(!$xml)
	return;
	
//$xsdPath = dirname(__FILE__) . '/submit.xsd';
//if($xsdPath && !$xml->schemaValidate($xsdPath))		
//	return;

file_put_contents('out.xml', $xml->saveXML());
echo $xml->saveXML();

echo time() . "\n";

