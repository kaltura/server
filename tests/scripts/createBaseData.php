<?php
require_once dirname(__FILE__) . '/../bootstrap.php';

$serviceUrl = 'http://localhost/';  //Default url is local host if no prameter is given
if(isset($argv[1]))
{
	print("using serviceUrl: $argv[1] \n");
	$serviceUrl = $argv[1];
}
else
	print("Service url wasn't inserted using default: http://localhost/ \n");

$config = new KalturaConfiguration();
$config->serviceUrl = $serviceUrl;

//$config->serviceUrl = 'http://hudsontest2.kaltura.dev/';
//$config->serviceUrl = 'http://devtests.kaltura.dev/';

$client = new KalturaClient($config);
$cmsPassword = 'Roni123!';
$partner = KalturaTestDeploymentHelper::createTestPartner();

$newPartner = $client->partner->register($partner, $cmsPassword); //create the new test partner

print("New test partner is: " . print_r($newPartner, true));

//Save the partner id into the global data file
KalturaGlobalData::setData("@SERVICE_URL@", $config->serviceUrl);
KalturaGlobalData::setData("@TEST_PARTNER_ID@", $newPartner->id);
KalturaGlobalData::setData("@TEST_PARTNER_ADMIN_SECRET@", $newPartner->adminSecret);
KalturaGlobalData::setData("@TEST_PARTNER_SECRET@", $newPartner->secret);

$config->partnerId = $newPartner->id; //Set the new test partner id
$client = new KalturaClient($config); // create a client for the new partner

$ks = $client->session->start($newPartner->adminSecret, null, KalturaSessionType::ADMIN, $newPartner->id, null, null);
$client->setKs($ks);

KalturaTestDeploymentHelper::addBaseData($client);

KalturaTestDeploymentHelper::addPermissions($client);

/**
 * 
 * Helper class for the test deployment
 * @author Roni
 *
 */
class KalturaTestDeploymentHelper
{
	/**
	 * 
	 * Creates a default test partner
	 * @return KalturaPartner - The test partner
	 */
	public static function createTestPartner()
	{
		$partner = new KalturaPartner();
		$partner->name = 'Test Partner';
		$partner->adminName = 'Test admin name'; 
		$partner->adminEmail = "test@mailinator.com";
		$partner->description = "partner for tests";
		return $partner;
	}

	/**
	 * 
	 * Adds the permissions for the test partner
	 * @param $client - KalturaClient
	 */
	public static function addPermissions(KalturaClient $client)
	{
		$partnerId = $client->getConfig()->partnerId;
		
		//Get the admin partner id and secret from the application.ini
		$adminConsoleIniPath = KALTURA_ROOT_PATH . "/admin_console/configs/application.ini";
		$adminIni = new Zend_Config_Ini($adminConsoleIniPath);
		$adminProductionSettings = $adminIni->get('production');
		$adminConsolePartnerId = $adminProductionSettings->settings->partnerId;
		$adminConsolePartnerSecret = $adminProductionSettings->settings->secret;
	
		$adminConfig = new KalturaConfiguration($adminConsolePartnerId);
		$adminConfig->serviceUrl = $client->getConfig()->serviceUrl; //The same service url of the test partner client		 
		$adminClient = new KalturaClient($adminConfig);

		//TODO: get this from the installation or outside input
		$ks = $adminClient->user->loginByLoginId('admin@kaltura.com', 'admin'); 
		$adminClient->setKs($ks);
		
		$addedPermissions = array();

		try 
		{
			$addedPermissions[] = KalturaTestDeploymentHelper::createPermission("CUEPOINT_PLUGIN_PERMISSION", KalturaPermissionType::PLUGIN);
			$addedPermissions[] = KalturaTestDeploymentHelper::createPermission("CODECUEPOINT_PLUGIN_PERMISSION", KalturaPermissionType::PLUGIN);
			$addedPermissions[] = KalturaTestDeploymentHelper::createPermission("ADCUEPOINT_PLUGIN_PERMISSION", KalturaPermissionType::PLUGIN);
			$addedPermissions[] = KalturaTestDeploymentHelper::createPermission("ANNOTATION_PLUGIN_PERMISSION", KalturaPermissionType::PLUGIN);
			$addedPermissions[] = KalturaTestDeploymentHelper::createPermission("DROPFOLDER_PLUGIN_PERMISSION", KalturaPermissionType::PLUGIN);
			$addedPermissions[] = KalturaTestDeploymentHelper::createPermission("CONTENTDISTRIBUTION_PLUGIN_PERMISSION", KalturaPermissionType::PLUGIN);
		}
		catch (Exception $e)
		{
			print("Exception was raised during permission adding: " . $e->getMessage() . "\n");
		}
		
		$systemPartnerPlugin = KalturaSystemPartnerClientPlugin::get($adminClient);
		$partner = $systemPartnerPlugin->systemPartner->get($partnerId);
		$partnerConfig = $systemPartnerPlugin->systemPartner->getConfiguration($partnerId);

		$partnerConfig->storageServePriority = KalturaStorageServePriority::KALTURA_ONLY;
		
		$newConfig = new KalturaSystemPartnerConfiguration();
		
		foreach ($addedPermissions as $permission)
		{
			$newConfig->permissions[] = $permission;
		} 

		if($newConfig->permissions && count($newConfig->permissions))
		{
			//Clean the id from the permissions
			foreach ($newConfig->permissions as &$permission)
			{
				$permission->id = null;	
				$permission->partnerId = null;
				$permission->createdAt = null;
				$permission->updatedAt = null;
				$permission->status = KalturaPermissionStatus::ACTIVE;
			}
		}

		$result = $systemPartnerPlugin->systemPartner->updateConfiguration($partnerId, $newConfig);
	}

	/**
	 * 
	 * creates permission for the given permission name
	 * @param string $permissionName
	 * @param KalturaPermissionType $permissionType
	 */
	public static function createPermission($permissionName, $permissionType = null)
	{
		$permission = new KalturaPermission();
		$permission->name = $permissionName;
		$permission->type = $permissionType;
		return $permission;
	}
	
	/**
	 * 
	 * Adds the base data for the test partner
	 * @param $client KalturaClient
	 */
	public static function addBaseData($client)
	{
		$uiConfs = $client->uiConf->listAction();
		KalturaGlobalData::setData("@UI_CONF_ID@", $uiConfs->objects[0]->id);
		
		$accessControls = $client->accessControl->listAction();
		KalturaGlobalData::setData("@DEFAULT_ACCESS_CONTROL@", $accessControls->objects[0]->id);
		
		$entry = new KalturaMediaEntry();
		$entry->name ="Entry For flavor asset test";
		$entry->type = KalturaEntryType::MEDIA_CLIP;
		$entry->mediaType = KalturaMediaType::VIDEO;
		$defaultEntry = $client->media->add($entry, KalturaEntryType::MEDIA_CLIP);
		
		$contentResource = new KalturaUrlResource();
		$contentResource->url = "http://sites.google.com/site/demokmc/Home/titanicin5seconds.flv";
		$client->media->addContent($defaultEntry->id, $contentResource);
			
		KalturaGlobalData::setData("@DEFAULT_ENTRY_ID@", $defaultEntry->id);

		//Add entry with duration from the dedault entries of the new partner
		$filter = new KalturaMediaEntryFilter();
		$filter->durationGreaterThan = 10;
		$filter->typeEqual = KalturaEntryType::MEDIA_CLIP;
		$results = $client->media->listAction($filter);
		
		if($results->totalCount)
			KalturaGlobalData::setData("@ENTRY_WITH_DURATION@", $results->objects[0]->id); //Saves the first object in the response
		else
			throw new Exception("No entries with duration were found, EXITING!");
			
		$flavorAssest = $client->flavorParams->listAction();
		KalturaGlobalData::setData("@DEFAULT_FLAVOR_PARAMS_ID@", $flavorAssest->objects[0]->id);
	}
}