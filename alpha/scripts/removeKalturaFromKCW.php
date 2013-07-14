 <?php

ini_set("memory_limit","256M");

require_once(__DIR__ . '/bootstrap.php');

if (!$argc)
{
	die('pleas provide partner id as input' . PHP_EOL . 
		'to run script: ' . basename(__FILE__) . ' X' . PHP_EOL . 
		'whereas X is partner id' . PHP_EOL);
}

$partnerId = $argv[0];

$dbConf = kConf::getDB();
DbManager::setConfig ( $dbConf );
DbManager::initialize ();

$c = new Criteria();
$c->add(uiConfPeer::SWF_URL, "%kcw%",Criteria::LIKE);
$c->add(uiConfPeer::OBJ_TYPE , uiConf::UI_CONF_TYPE_CW, Criteria::EQUAL);
$c->add(uiConfPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);

$kcwUiconfs = uiConfPeer::doSelect($c);


if (!count($kcwUiconfs))
{
	exit;
}

$fileName = "/manual_uiconfs_paths.log";
$flog = fopen($fileName,'a+');
//Run a loop for each uiConf to get its filesync key, thus acquiring its confile
foreach ($kcwUiconfs as $kcwUiconf)
{
	/* @var $kcwUiconf uiConf */
	$kcwUiconfFilesyncKey = $kcwUiconf->getSyncKey(uiConf::FILE_SYNC_UICONF_SUB_TYPE_DATA);
	$kcwConfile = kFileSyncUtils::file_get_contents($kcwUiconfFilesyncKey, false , false);
	
	if (!$kcwConfile)
	{
		continue;
	}
		
	$kcwConfileXML = new SimpleXMLElement($kcwConfile);

	$path = '//provider[@id="kaltura" or @name="kaltura"]';
	
	$nodesToRemove = $kcwConfileXML->xpath($path);
	
	if (!count($nodesToRemove))
	{
		continue;
	}
	
	
	if ($kcwUiconf->getCreationMode() != uiConf::UI_CONF_CREATION_MODE_MANUAL)
	{
		//No point in this "for" loop if we can't save the UIConf.
		foreach ($nodesToRemove as $nodeToRemove)
		{
			$nodeToRemoveDom = dom_import_simplexml($nodeToRemove);

			$nodeToRemoveDom->parentNode->removeChild($nodeToRemoveDom);
		}
		$kcwConfile = $kcwConfileXML->saveXML();
		$kcwUiconf->setConfFile($kcwConfile);
		$kcwUiconf->save();
	}
	else
	{
		$confilePath = $kcwUiconf->getConfFilePath()."\n";
		fwrite($flog, $confilePath);
	}
	//$kcw_uiconf_filesync_key = $kcw_uiconf->getSyncKey(uiConf::FILE_SYNC_UICONF_SUB_TYPE_DATA);
	//kFileSyncUtils::file_put_contents($kcw_uiconf_filesync_key, $kcw_confile , false);
}
fclose($flog);
