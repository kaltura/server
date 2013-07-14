<?php

define('KALTURA_ROOT_PATH', realpath(dirname(__FILE__) . '/../../../../..'));
require_once(KALTURA_ROOT_PATH . '/infra/KAutoloader.php');

define("KALTURA_API_PATH", KALTURA_ROOT_PATH . "/api_v3");

require_once(KALTURA_ROOT_PATH . '/alpha/config/kConf.php');
// Autoloader
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "lib", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "services", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "alpha", "plugins", "*")); // needed for testmeDoc
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "generator")); // needed for testmeDoc
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/plugins/' . basename(__FILE__) . '.cache');
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

$entryId = '0_kntco0ij';

/*$matches = null;
if (preg_match ( "/x0y.*.err/" , '/pub/in/x0y.title.err' , $matches))
{
	print_r($matches);
	print_r(preg_split ("/\./", $matches[0]));
}
else
{
 echo 'non';
}
return;
if(isset($argv[1]))
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

		$fileTransferMgr = kFileTransferMgr::getInstance(kFileTransferMgrType::FTP);
		if(!$fileTransferMgr)
			throw new Exception("SFTP manager not loaded");
			
		$fileTransferMgr->login('ftp-int.vzw.real.com', 'vp_foxsports', 'X4ul3ap');
		print_r($fileTransferMgr->listDir("/pub/in"));
//		$fileTransferMgr->putFile($destFile, $srcFile, true);

		return;*/
$entry = entryPeer::retrieveByPKNoFilter($entryId);
$mrss = kMrssManager::getEntryMrss($entry);
$allParts = explode('</item>', $mrss);
$add  = '<customData metadataProfileId="1"><metadata><ShortTitle>Tan-Tan test 1 long title</ShortTitle> <StatskeysFull> <statskeys><statskey>  <statskeyId>230</statskeyId> <statskeyName>More Sports</statskeyName>   <statskeyType>Sport</statskeyType>   <parentId>0</parentId> </statskey> <statskey>  <statskeyId>220</statskeyId> <statskeyName>Golf</statskeyName> <statskeyType>Sport</statskeyType>   <parentId>230</parentId>   </statskey> <statskey> <statskeyId>222</statskeyId> <statskeyName>LPGA</statskeyName>   <statskeyType>League</statskeyType>   <parentId>220</parentId> </statskey> <statskey>  <statskeyId>2241</statskeyId>   <statskeyName>Annika Sorenstam</statskeyName> <statskeyType>Player</statskeyType> <parentId>222</parentId> </statskey><statskey>  <statskeyId>433</statskeyId> <statskeyName>Premier League</statskeyName> <statskeyType>League</statskeyType> <parentId>177</parentId> </statskey> <statskey><statskeyId>568</statskeyId><statskeyName>Manchester United</statskeyName> <statskeyType>Team</statskeyType> <parentId>433</parentId></statskey></statskeys></StatskeysFull></metadata></customData>';
$mrss = $allParts[0] . $add . '</item>';

file_put_contents('mrss.xml', $mrss);
KalturaLog::debug("MRSS [$mrss]");

$distributionJobData = new KalturaDistributionSubmitJobData();
$delData = new KalturaDistributionDeleteJobData();

$dbDistributionProfile = DistributionProfilePeer::retrieveByPK(7);
$distributionProfile = new KalturaIdeticDistributionProfile();
$distributionProfile->fromObject($dbDistributionProfile);
$distributionJobData->distributionProfileId = $distributionProfile->id;
$delData->distributionProfileId = $distributionProfile->id;


$distributionJobData->distributionProfile = $distributionProfile;
$delData->distributionProfile = $distributionProfile;

$dbEntryDistribution = EntryDistributionPeer::retrieveByPK(38);
$entryDistribution = new KalturaEntryDistribution();
$entryDistribution->fromObject($dbEntryDistribution);
$distributionJobData->entryDistributionId = $entryDistribution->id;
$distributionJobData->entryDistribution = $entryDistribution;
$delData->entryDistributionId = $entryDistribution->id;
$delData->entryDistribution = $entryDistribution;

//$myp = new IdeticDistributionProfile();
//print_r($myp->validateForSubmission($dbEntryDistribution, "submit"));
//return;

$providerData = new KalturaIdeticDistributionJobProviderData($distributionJobData);
$distributionJobData->providerData = $providerData;
$delData->providerData = $providerData;

file_put_contents('out.xml', $providerData->xml);
KalturaLog::debug("XML [$providerData->xml]");

return;
$engine = new IdeticDistributionEngine();
$engine->submit($distributionJobData);
//print($distributionJobData->remoteId);
//$distributionJobData->remoteId = '4dc79a4a86040';
$delData->remoteId = $distributionJobData->remoteId;

//print($engine->update($distributionJobData));
echo $engine->delete($delData);


//$xml = new KDOMDocument();
//if(!$xml->loadXML($mrss))
//{
//	KalturaLog::err("MRSS not is not valid XML:\n$mrss\n");
//	exit;
//}
//
//$xslPath = 'submit.xsl';
//$xsl = new KDOMDocument();
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

