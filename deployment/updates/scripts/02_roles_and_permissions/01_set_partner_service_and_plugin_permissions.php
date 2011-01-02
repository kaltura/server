<?php

$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;

$stopFile = dirname(__FILE__).'/stop_permission_migration'; // creating this file will stop the script
$partnerLimitEachLoop = 20;

//------------------------------------------------------

require_once(dirname(__FILE__).'/../../../bootstrap.php');


// stores the last handled admin kuser id, helps to restore in case of crash
$lastPartnerFile = 'last_partner';
$lastPartner = -10;
if(file_exists($lastPartnerFile)) {
	$lastPartner = file_get_contents($lastPartnerFile);
	KalturaLog::log('last partner file already exists with value - '.$lastPartner);
}
if(!$lastPartner)
	$lastPartner = 0;

$partners = getPartners($lastPartner, $partnerLimitEachLoop);


while(count($partners))
{
	foreach($partners as $partner)
	{
		if (file_exists($stopFile)) {
			die('STOP FILE CREATED');
		}

		$lastPartner = $partner->getId();
		KalturaLog::log('-- partner id ' . $lastPartner);
		
		$newPermissions = array();
		
		// allowed plugins
		$enabledPlugins = $partner->getFromCustomData("enabledPlugins", null, 0);
		if ($enabledPlugins) {
			foreach ($enabledPlugins as $pluginName => $enabled)
			{
				if (!$enabled) {
					continue;
				}
				$permission = new Permission();
				$permission->setPartnerId($partner->getId());
				$permission->setName(PermissionPeer::getPermissionNameFromPluginName($pluginName));
				$permission->setFriendlyName($pluginName . ' Plugin');
				$permission->setDescription('Permission to use '.$pluginName.' plugin');
				$permission->setStatus(PermissionStatus::ACTIVE);
				$permission->setType(PermissionType::PLUGIN);
				$newPermissions[] = $permission;
			}
		}
		
		// analytics tab
		$allowedAnalyticsTab = $partner->getFromCustomData("enableAnalyticsTab", null, 0);
		if ($allowedAnalyticsTab) {
			$analyticsTabPermission = new Permission();
			$analyticsTabPermission->setPartnerId($partner->getId());
			$analyticsTabPermission->setName(PermissionName::FEATURE_ANALYTICS_TAB);
			$analyticsTabPermission->setFriendlyName('Analytics tab feature');
			$analyticsTabPermission->setDescription('Permission  to use analytics tab');
			$analyticsTabPermission->setStatus(PermissionStatus::ACTIVE);
			$analyticsTabPermission->setType(PermissionType::SPECIAL_FEATURE);
			$newPermissions[] = $analyticsTabPermission;
		}
		
		// silverlight
		$allowedSilverLight = $partner->getFromCustomData("enableSilverLight", null, 0);
		if ($allowedSilverLight) {
			$silverLightPermission = new Permission();
			$silverLightPermission->setPartnerId($partner->getId());
			$silverLightPermission->setName(PermissionName::FEATURE_SILVERLIGHT);
			$silverLightPermission->setFriendlyName('Silvelight feature');
			$silverLightPermission->setDescription('Permission to use Silverlight');
			$silverLightPermission->setStatus(PermissionStatus::ACTIVE);
			$silverLightPermission->setType(PermissionType::SPECIAL_FEATURE);
			$newPermissions[] = $silverLightPermission;
		}
		
		// vast
		$allowedVast = $partner->getFromCustomData("enableVast", null, 0);
		if ($allowedVast) {
			$vastPermission = new Permission();
			$vastPermission->setPartnerId($partner->getId());
			$vastPermission->setName(PermissionName::FEATURE_VAST);
			$vastPermission->setFriendlyName('VAST feature');
			$vastPermission->setDescription('Permission to use VAST');
			$vastPermission->setStatus(PermissionStatus::ACTIVE);
			$vastPermission->setType(PermissionType::SPECIAL_FEATURE);
			$newPermissions[] = $vastPermission;
		}
		
		// 508 players
		$allowed508Players = $partner->getFromCustomData("enable508Players", null, 0);
		if ($allowed508Players) {
			$players508Permission = new Permission();
			$players508Permission->setPartnerId($partner->getId());
			$players508Permission->setName(PermissionName::FEATURE_508_PLAYERS);
			$players508Permission->setFriendlyName('508 players feature');
			$players508Permission->setDescription('Permission to use 508 players');
			$players508Permission->setStatus(PermissionStatus::ACTIVE);
			$players508Permission->setType(PermissionType::SPECIAL_FEATURE);
			$newPermissions[] = $players508Permission;
		}
		
		// live stream
		$allowedLiveStream = $partner->getFromCustomData("liveEnabled", null, 0);
		if ($allowedLiveStream) {
			$livePermission = new Permission();
			$livePermission->setPartnerId($partner->getId());
			$livePermission->setName(PermissionName::FEATURE_LIVE_STREAM);
			$livePermission->setFriendlyName('Live stream feature');
			$livePermission->setDescription('Permission to use live stream');
			$livePermission->setStatus(PermissionStatus::ACTIVE);
			$livePermission->setType(PermissionType::SPECIAL_FEATURE);
			$newPermissions[] = $livePermission;
		}
				
		
		if (!$dryRun) {
			foreach($newPermissions as $permission) {
				KalturaLog::log('SAVING new permission for partner ['.$partner->getId().']:');
				
				PermissionPeer::addToPartner($permission, $partner->getId());
				KalturaLog::log(print_r($permission, true));
			}	
		}
		else {
			foreach($newPermissions as $permission) {
				KalturaLog::log('DRY RUN ONLY - new permission for partner ['.$partner->getId().']:');
				KalturaLog::log(print_r($permission, true));
				// dry run - no saving!
			}
		}		
				
		file_put_contents($lastPartnerFile, $lastPartner);
	}
	
	PartnerPeer::clearInstancePool();
	
	$partners = getPartners($lastPartner, $partnerLimitEachLoop);
}

KalturaLog::log('Done' . $dryRun ? 'DRY RUN!' : 'REAL RUN!');
echo 'Done' . $dryRun ? 'DRY RUN!' : 'REAL RUN!';

function getPartners($lastPartner, $partnerLimitEachLoop)
{
	$c = new Criteria();
	$c->add(PartnerPeer::ID, $lastPartner, Criteria::GREATER_THAN);
	$c->addAscendingOrderByColumn(PartnerPeer::ID);
	$c->setLimit($partnerLimitEachLoop);
	return PartnerPeer::doSelect($c);
}

