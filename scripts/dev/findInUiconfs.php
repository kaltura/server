<?php

$swf_url_like = '%kdp%';
$search = 'tremor';
if(isset($argv[1])) $search = $argv[1];

$include_features = true;
if(isset($argv[2]) && $argv[2] === 'false') $include_features = false;

$format = 'text';
if(isset($argv[2]) && $argv[2] === 'csv') $format = 'csv';
if(isset($argv[3]) && $argv[3] === 'csv') $format = 'csv';

ini_set("memory_limit","256M");

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../'));
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "metadata", "*"));
KAutoloader::setClassMapFilePath('../cache/classMap.cache');
KAutoloader::register();

error_reporting(E_ALL);
//KalturaLog::setLogger(new KalturaStdoutLogger());

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();

$uiConfsCriteria = new Criteria();
$uiConfsCriteria->add(uiConfPeer::SWF_URL, $swf_url_like, Criteria::LIKE);
$uiConfsCriteria->setLimit(100);

$uiConfs = uiConfPeer::doSelect($uiConfsCriteria);
if($format == 'csv')
{
	echo 'uiconf,partner,swf_url,files'.PHP_EOL;
}
while(count($uiConfs))
{
	foreach($uiConfs as $uiConf)
	{
		$foundFiles = array();
		
		$key = $uiConf->getSyncKey(uiConf::FILE_SYNC_UICONF_SUB_TYPE_DATA);
		$content = kFileSyncUtils::file_get_contents($key, true, false);
		if(stripos($content, $search) > 0)
			$foundFiles[] = kFileSyncUtils::getLocalFilePathForKey($key, false);
			
		if($include_features)
		{
			$key = $uiConf->getSyncKey(uiConf::FILE_SYNC_UICONF_SUB_TYPE_FEATURES);
			$content = kFileSyncUtils::file_get_contents($key, true, false);
			if(stripos($content, $search) > 0)
				$foundFiles[] = kFileSyncUtils::getLocalFilePathForKey($key, false);
		}
			
		if(!count($foundFiles))
			continue;
			
		if($format == 'text')
		{
			echo "ui_conf[" . $uiConf->getId() . "] partner[" . $uiConf->getpartnerId() . "] swf_url[" . $uiConf->getSwfUrl() . "] files[" . implode(', ', $foundFiles) . "]\n";
		}
		if($format == 'csv')
		{
			echo $uiConf->getId().','.$uiConf->getpartnerId().','.$uiConf->getSwfUrl().',"'.(implode(', ', $foundFiles)).'"'.PHP_EOL;
		}
	}
	
	$offset = $uiConfsCriteria->getOffset() + count($uiConfs);
	$uiConfsCriteria->setOffset($offset);
	$uiConfs = uiConfPeer::doSelect($uiConfsCriteria);
}
