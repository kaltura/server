<?php
define ('DEBUG', true);
//require_once ( "./define.php" );

//require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'lib/myContentStorage.class.php');
//require_once(SF_ROOT_DIR.'/../infra/bootstrap_base.php');
//define("KALTURA_API_PATH", KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR."api_v3");

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../../'));
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::setClassMapFilePath('../../cache/classMap.cache');
KAutoloader::register();

error_reporting(E_ALL);

ini_set("memory_limit","256M");

DbManager::setConfig(kConf::getDB());
DbManager::initialize();

KalturaLog::setLogger(new KalturaStdoutLogger());

$partner_id = @$argv[1];

$should_do_flavors_to_web = @$argv[2];
$force_upgrade = '';
$force_upgrade = @$argv[2];

$partner = PartnerPeer::retrieveByPK($partner_id);
if(!$partner)
{
	die('no such partner.'.PHP_EOL);
}

if($partner->getKmcVersion() == '2')
{
	echo 'Partner is already on Andromeda KMC'.PHP_EOL;
	die;
}

$count = checkFlavors($partner);
if($count && $should_do_flavors_to_web != 'convert_flavors')
{
	echo "found $count flavors with only 'mbr' tag, if you want to convert them run:".PHP_EOL;
	echo "      php {$argv[0]} {$argv[1]} convert_flavors".PHP_EOL;
	echo "if you don't want to convert them, run:".PHP_EOL;
	echo "      php {$argv[0]} {$argv[1]} skip_flavors".PHP_EOL;
	if($force_upgrade == 'force')
	{
		convertFlavorsTags($partner);
		if(DEBUG)
		{
			die('this was dry-run, exiting...'.PHP_EOL);
		}
		else
		{
			echo $count." flavors were fixed, going to upgrade partner".PHP_EOL;
		}
	}
	else
	{
		die;
	}
}
elseif($count && $should_do_flavors_to_web == 'skip_flavors')
{
	echo 'not converting flavors tags and going on... '.PHP_EOL;
	if(DEBUG)
	{
		die('this was dry-run, exiting...'.PHP_EOL);
	}	
}
elseif($count && $should_do_flavors_to_web == 'convert_flavors')
{
	convertFlavorsTags($partner);
	if(DEBUG)
	{
		die('this was dry-run, exiting...'.PHP_EOL);
	}
}
else
{
	echo 'no flavor tags to convert'.PHP_EOL;
}
/* one time running to fix partner 99
if($partner_id == 99)
{
	$partner->setDefaultConversionProfileId(1);
	$partner->save();
	exit();
}
*/

// if getDefaultConversionProfileId - don't do anything.
$defaultConversionProfileId = $partner->getDefaultConversionProfileId();
if($defaultConversionProfileId)
{
	// do nothing for that partner now.
	echo 'partner already have getDefaultConversionProfileId: '.$partner->getDefaultConversionProfileId().PHP_EOL;
        $partner->setKmcVersion('2');
        $partner->save();
	exit();
}

$currentConversionProfileType = $partner->getCurrentConversionProfileType();
if($currentConversionProfileType)
{
	// convert old to new (or check if has new)
	$oldCp = ConversionProfilePeer::retrieveByPK($currentConversionProfileType);
	if(!$oldCp->getConversionProfile2Id())
	{
		echo 'converting old now... '.PHP_EOL;
		myConversionProfileUtils::createConversionProfile2FromConversionProfile($oldCp);
	}
	// set new id on defaultConversionProfileId
	$partner->setDefaultConversionProfileId($oldCp->getConversionProfile2Id());
	$partner->setKmcVersion('2');
	$partner->save();
	echo 'partner set to DefaultConversionProfileId '.$partner->getDefaultConversionProfileId().' (from: '.$oldCp->getConversionProfile2Id().')'.PHP_EOL;
	exit();
}

// no currentConversionProfileType, lets see what on default
$defConversionProfileType = $partner->getFromCustomData('defConversionProfileType');
if(!is_null($defConversionProfileType))
{
	$oldCp = myConversionProfileUtils::getConversionProfile($partner->getId(), $defConversionProfileType);
	if(!$oldCp->getConversionProfile2Id() && $oldCp->getPartnerId() == $partner->getId())
	{
		myConversionProfileUtils::createConversionProfile2FromConversionProfile($oldCp);
		// set new id on defaultConversionProfileId
		$partner->setDefaultConversionProfileId($oldCp->getConversionProfile2Id());
		$partner->setKmcVersion('2');
		$partner->save();
		echo 'converted old default conversion profile. DefaultConversionProfileId: '.$partner->getDefaultConversionProfileId().PHP_EOL;
		exit();
	}
}

// if didn't exit so far, copy from template
$sourcePartner = PartnerPeer::retrieveByPK(kConf::get('template_partner_id'));
myPartnerUtils::copyConversionProfiles($sourcePartner, $partner);
echo 'copied from template partner. DefaultConversionProfileId: '.$partner->getDefaultConversionProfileId().PHP_EOL;

$partner->setKmcVersion('2');
$partner->save();
exit();

function checkFlavors(Partner $partner)
{
	$c = new Criteria();
	flavorAssetPeer::setDefaultCriteriaFilter(false);
	$c->addAnd(flavorAssetPeer::PARTNER_ID, $partner->getId());
	$c->addAnd(flavorAssetPeer::TAGS, 'mbr');
	$c->addAnd(flavorAssetPeer::FILE_EXT, 'flv');
	$c->addAnd(flavorAssetPeer::FLAVOR_PARAMS_ID, 0);
	
	$flavorsCount = flavorAssetPeer::doCount($c);
	
	flavorAssetPeer::setDefaultCriteriaFilter(true);
	
	return $flavorsCount;
}

function convertFlavorsTags(Partner $partner)
{
	$c = new Criteria();
	flavorAssetPeer::setDefaultCriteriaFilter(false);
	$c->addAnd(flavorAssetPeer::PARTNER_ID, $partner->getId());
	$c->addAnd(flavorAssetPeer::TAGS, 'mbr');
	$c->addAnd(flavorAssetPeer::FILE_EXT, 'flv');
	$c->addAnd(flavorAssetPeer::FLAVOR_PARAMS_ID, 0);
	
	$flavors = flavorAssetPeer::doSelect($c);
	
	foreach($flavors as $flavor)
	{
		if(DEBUG)
		{
			echo "select tags,partner_id,is_original,file_ext,id from flavor_asset where id = '{$flavor->getId()}';".PHP_EOL;
			echo "update flavor_asset set tags = 'mbr,web' where id = '{$flavor->getId()}';".PHP_EOL;
		}
		else
		{
			$flavor->setTags('mbr,web');
			$flavor->save();
		}
	}
	
	flavorAssetPeer::setDefaultCriteriaFilter(true);
}

function getPartnerStatusForUiConfs(Partner $partner)
{
	
}

function getPartnerStatusForEntries(Partner $partner)
{
	
}
