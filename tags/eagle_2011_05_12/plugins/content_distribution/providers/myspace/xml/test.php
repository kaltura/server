<?php

define('KALTURA_ROOT_PATH', realpath(dirname(__FILE__) . '/../../../../..'));
echo KALTURA_ROOT_PATH;
require_once(KALTURA_ROOT_PATH . '/infra/bootstrap_base.php');
require_once(KALTURA_ROOT_PATH . '/infra/KAutoloader.php');

define("KALTURA_API_PATH", KALTURA_ROOT_PATH . "/api_v3");

require_once(KALTURA_ROOT_PATH . '/alpha/config/kConf.php');
// Autoloader
require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "lib", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "services", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "alpha", "plugins", "*")); // needed for testmeDoc
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "generator")); // needed for testmeDoc
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/plugins/classMap.cache');
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

$entryId = '0_g0bhfji7';

/*if(isset($argv[1]))
	$entryId = $argv[1];

foreach($argv as $arg)
{
	$matches = null;
	if(preg_match('/(.*)=(.*)/', $arg, $matches))
	{
		$field = $matches[1];
//		$providerData->$field = $matches[2];
	}
}
*/
$entry = entryPeer::retrieveByPKNoFilter($entryId);
$mrss = kMrssManager::getEntryMrss($entry);
file_put_contents('mrss.xml', $mrss);
KalturaLog::debug("MRSS [$mrss]");

$distributionJobData = new KalturaDistributionSubmitJobData();

$dbDistributionProfile = DistributionProfilePeer::retrieveByPK(1);
$distributionProfile = new KalturaMyspaceDistributionProfile();
$distributionProfile->fromObject($dbDistributionProfile);
$distributionJobData->distributionProfileId = $distributionProfile->id;


$distributionJobData->distributionProfile = $distributionProfile;

$dbEntryDistribution = EntryDistributionPeer::retrieveByPK(45);
$entryDistribution = new KalturaEntryDistribution();
$entryDistribution->fromObject($dbEntryDistribution);
$distributionJobData->entryDistributionId = $entryDistribution->id;
$distributionJobData->entryDistribution = $entryDistribution;

//$myp = new MyspaceDistributionProfile();
//print_r($myp->validateForSubmission($dbEntryDistribution, "submit"));
//return;

$providerData = new KalturaMyspaceDistributionJobProviderData($distributionJobData);
$distributionJobData->providerData = $providerData;

file_put_contents('out.xml', $providerData->xml);
KalturaLog::debug("XML [$providerData->xml]");


$engine = new MyspaceDistributionEngine();
$engine->submit($distributionJobData);


//$xml = new DOMDocument();
//if(!$xml->loadXML($mrss))
//{
//	KalturaLog::err("MRSS not is not valid XML:\n$mrss\n");
//	exit;
//}
//
//$xslPath = 'submit.xsl';
//$xsl = new DOMDocument();
//$xsl->load($xslPath);
//			
//// set variables in the xsl
//$varNodes = $xsl->getElementsByTagName('variable');
//foreach($varNodes as $varNode)
//{
//	$nameAttr = $varNode->attributes->getNamedItem('name');
//	if(!$nameAttr)
//		continue;
//		
//	$name = $nameAttr->value;
//	if($name && $distributionJobData->$name)
//	{
//		$varNode->textContent = $distributionJobData->$name;
//		$varNode->appendChild($xsl->createTextNode($distributionJobData->$name));
//		KalturaLog::debug("Set variable [$name] to [{$distributionJobData->$name}]");
//	}
//}
//
//$proc = new XSLTProcessor;
//$proc->registerPHPFunctions();
//$proc->importStyleSheet($xsl);
//
//$xml = $proc->transformToDoc($xml);
//if(!$xml)
//{
//	KalturaLog::err("Transform returned false");
//	exit;
//}
//
//$xml = $xml->saveXML();
//
//file_put_contents('out.xml', $xml);
//KalturaLog::debug("XML [$xml]");
