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

KalturaGlobalData::clearValue(); //Clears the old global data as we are deploying a new data now.

//Save the partner id into the global data file
KalturaGlobalData::setData("@SERVICE_URL@", $config->serviceUrl);
KalturaGlobalData::setData("@TEST_PARTNER_ID@", $newPartner->id);
KalturaGlobalData::setData("@TEST_PARTNER_ADMIN_SECRET@", $newPartner->adminSecret);
KalturaGlobalData::setData("@TEST_PARTNER_SECRET@", $newPartner->secret);

$config->partnerId = $newPartner->id; //Set the new test partner id
$client = new KalturaClient($config); // create a client for the new partner

$ks = $client->session->start($newPartner->adminSecret, null, KalturaSessionType::ADMIN, $newPartner->id, 86400, null);
$client->setKs($ks);

KalturaTestDeploymentHelper::setPartner($newPartner);

KalturaTestDeploymentHelper::addPermissions($client);

KalturaTestDeploymentHelper::addBaseData($client);


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
	 * The deployment new created partner
	 * @var KalturaPartner
	 */
	private static $partner = null;
	
	/**
	 * 
	 * The system default partner
	 * @var int
	 */
	const SYSTEM_DEFAULT_PARTNER = 0;
	
	/**
	 * 
	 * Sets the deploymenr helper partner
	 * @param KalturaPartner - The test partner
	 */
	public static function setPartner($partner)
	{
		KalturaTestDeploymentHelper::$partner = $partner;
		
	}
	
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
			$addedPermissions[] = KalturaTestDeploymentHelper::createPermission("METADATA_PLUGIN_PERMISSION", KalturaPermissionType::PLUGIN);
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
		
		$config = new KalturaConfiguration();
		$config->serviceUrl = $client->getConfig()->serviceUrl;
		$config->partnerId = self::$partner->id;
		
		$client = new KalturaClient($config); // create a client for the partner
		$ks = $client->session->start(self::$partner->adminSecret, null, KalturaSessionType::ADMIN, self::$partner->id, 86400, null);
		$client->setKs($ks);
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
		
		$partnerId = KalturaTestDeploymentHelper::$partner->id;
		
		//if we are on local host then we can fins the KMC ui Confs (through the server side)
		if($client->getConfig()->serviceUrl == "http://localhost/") 
		{
			//WE need to get the 
			$kmc_swf_version = kConf::get('kmc_version');
		
			/** uiconf listing work **/
			/** fill $this->confs with all uiconf objects for all modules **/
			$kmcGeneralUiConf = KalturaTestDeploymentHelper::getAllKMCUiconfs('kmc', $kmc_swf_version , self::SYSTEM_DEFAULT_PARTNER);
			
			/** for each module, create separated lists of its uiconf, for each need **/
			/** kmc general uiconfs **/
			$kmc_general = KalturaTestDeploymentHelper::find_confs_by_usage_tag($kmcGeneralUiConf, "kmc_kmcgeneral", false, $kmcGeneralUiConf);
			$kmc_permissions = KalturaTestDeploymentHelper::find_confs_by_usage_tag($kmcGeneralUiConf, "kmc_kmcpermissions", false, $kmcGeneralUiConf);

			KalturaGlobalData::setData("@TEST_PARTNER_KMC_UI_CONF@", $kmc_general->getId());
			KalturaGlobalData::setData("@TEST_PARTNER_PERMISSIONS_UI_CONF@", $kmc_permissions->getId());
			KalturaGlobalData::setData("@TEST_PARTNER_DASHBOARD_UI_CONF@", $kmc_general->getId()); // TODO: fix this and see what is the real uiConf needed here
			KalturaGlobalData::setData("@TEST_PARTNER_USER_ID@", KalturaTestDeploymentHelper::$partner->adminUserId);
		}
		
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
			KalturaGlobalData::setData("@ENTRY_WITH_DURATION_ID@", $results->objects[0]->id); //Saves the first object in the response
		else
		{
			KalturaGlobalData::setData("@ENTRY_WITH_DURATION_ID@", $defaultEntry->id); //Saves the default entry id
			//throw new Exception("No entries with duration were found, EXITING!");
			print("No entries with duration were found! using the default entry id\n");
		}
			
		$flavorAssest = $client->flavorParams->listAction();
		KalturaGlobalData::setData("@DEFAULT_FLAVOR_PARAMS_ID@", $flavorAssest->objects[0]->id);
		
		self::addUsers($client);
		self::addMetadataSearchData($client);
		self::addDWHdata($client);
	}
	
	/**
	 * 
	 * Creates the default puser
	 * @param string $puserId
	 */
	private static function createDefualtUser($puserId)
	{
		$user = new KalturaUser();
		$user->id = $puserId;
		$user->email = "$puserId@mailinator.com";
		$user->firstName = $puserId;
		$user->fullName = $puserId;
		$user->password = "Passme1!";
		return $user;
	}
	
	/**
	 * 
	 * Adds asnd sets the test partner users 
	 * @param unknown_type $client
	 */
	private static function addUsers(KalturaClient $client)
	{
		$user1 = self::createDefualtUser("puser1");
		$user2 = self::createDefualtUser("puser2");
		$user3 = self::createDefualtUser("puser3");
		
		$userAdded1 = $client->user->add($user1);
		KalturaGlobalData::setData("@TEST_USER1@", $userAdded1->id);
		
		$userAdded2 = $client->user->add($user2);
		KalturaGlobalData::setData("@TEST_USER2@", $userAdded2->id);
		
		$userAdded3 = $client->user->add($user3);
		KalturaGlobalData::setData("@TEST_USER3@", $userAdded3->id);
	}
	
	/**
	 * 
	 * Add the data 
	 */
	private static function addDWHdata(KalturaClient $client)
	{
		$partnerId = $client->getConfig()->partnerId;
		for($i = 0; $i < 1000; $i++)
		{
			$event = new KalturaStatsEvent();
			$event->partnerId = $partnerId;
			$event->eventType= KalturaStatsEventType::PLAY;
			$event->entryId = KalturaGlobalData::getData("@DEFAULT_ENTRY_ID@");
			$event->clientVer = "test client";
			$event->sessionId = "test session";
			$event->referrer = "http://kaltura.com/" . $i % 10;
			$event->uiconfId = KalturaGlobalData::getData("@UI_CONF_ID@");
					
			try
			{
				$client->stats->collect($event);
			}
			catch (Exception $e)
			{
				//Currently do nothing
			}
		}
		
		KalturaGlobalData::setData("@PLAYS@", $i);
		
		//Log rotating only if the service url is localhost
		if($client->getConfig()->serviceUrl == "http://localhost")
		{
			$logRotateConfString = "/opt/kaltura/log/kaltura_apache_access.log {\n
						 rotate 5\n
						 daily\n
						 missingok\n
						 compress\n
						 nodateext\n
						 notifempty\n
						 sharedscripts\n
						 postrotate\n
						        /usr/sbin/apachectl -k restart\n
						 endscript\n
						 lastaction\n
						        mv /opt/kaltura/log/kaltura_apache_access.log.1.gz /opt/kaltura/log/kaltura_apache_access.log.$partnerId.gz\n
						 endscript\n
						}";
	
			$logRotatePath = "/tmp/log_rotate.conf";
			$file = fopen("$logRotatePath", "w+");
			fwrite($file, $logRotateConfString);
			fclose($file);

			//Now log rotate on local machine
			exec("logrotate -f $logRotatePath");
		
//			//run hourly
//			exec("/opt/kaltura/dwh/etlsource/execute/etl_hourly.sh");
//			
//			//run daily
//			exec("/opt/kaltura/dwh/etlsource/execute/etl_daily.sh");	
		}
	}
	
	/**
	 * 
	 * Gets all the kmc ui confs
	 * @param string $module_tag
	 * @param string $module_version
	 * @param int $template_partner_id
	 */
	public static function getAllKMCUiconfs($module_tag, $module_version, $template_partner_id)
	{
		$c = new Criteria();
		$c->addAnd(uiConfPeer::PARTNER_ID, $template_partner_id);
//		$c->addAnd(uiConfPeer::TAGS, "%".$module_tag."\_".$module_version."%", Criteria::LIKE); // no need to check for the kmc version
		$c->addAnd(uiConfPeer::TAGS, "%autodeploy%", Criteria::LIKE);
		return uiConfPeer::doSelect($c);
	}
	
	/**
	 * 
	 * Finds the ui confs by their usage tag
	 * @param array $confs
	 * @param unknown_type $tag
	 * @param bool $allow_array
	 * @param array $alternateConfs
	 */
	public static function find_confs_by_usage_tag($confs, $tag, $allow_array = false, $alternateConfs = array())
	{
	  $uiconfs = array();
	  foreach($confs as $uiconf)
	  {
	    $tags = explode(",", $uiconf->getTags());
	    $trimmed_tags = KalturaTestDeploymentHelper::TrimArray($tags);
	    if(in_array($tag, $trimmed_tags))
	    {
			if($allow_array)
			{
				$uiconfs[] = $uiconf;
			}
			else
			{
				return $uiconf;
			}
	    }
	  }
	  
	  if($allow_array)
	  {
		// if we didnt find uiconfs and we have alternate uiconf list -
		// 	call myself with the alternate uiconfs, return whatever was returned.
		if(!count($uiconfs) && count($alternateConfs))
		{
			return self::find_confs_by_usage_tag($alternateConfs, $tag, $allow_array);
		}
		// we either found uiconfs from the template or we didn't find but we don't have alternate
		return $uiconfs;
	  }
	  
	  // requested single and not array, and no valid uiconf found. try calling myself with alternate
	  if(!count($alternateConfs))
	  {
		return new uiConf();
		
	  }
	  else
	  {
		return self::find_confs_by_usage_tag($alternateConfs, $tag, $allow_array);
	  }
	}
	
	/**
	 * 
	 * Trims the given array
	 * @param mixed $arr
	 */
	public static function TrimArray($arr)
	{
	    if (!is_array($arr))
	    {
	    	return $arr; 
	    }
	
	    while (list($key, $value) = each($arr))
	    {
			if (is_array($value))
			{
			    $arr[$key] = KalturaTestDeploymentHelper::TrimArray($value);
			}
			else 
			{
			    $arr[$key] = trim($value);
			}
	    }
	    
	    return $arr;
	}
	
	protected static function addMetadataSearchData(KalturaClient $client)
	{
		//add metadata profile
		$metadataProfile = new KalturaMetadataProfile();
		$metadataProfile->name = 'Metadata profile for tests';
		$metadataProfile->createMode = KalturaMetadataProfileCreateMode::API;
		$metadataProfile->systemName = 'Metadata profile for tests';

		$metadataProfileXsd = '<xsd:schema xmlns:xsd="http://www.w3.org/2001/XMLSchema">   <xsd:element name="metadata">     <xsd:complexType>       <xsd:sequence>         <xsd:element id="md_90192848-AAE47-03390173D13A"         name="Startdate" minOccurs="0" maxOccurs="1" type="dateType">           <xsd:annotation>             <xsd:documentation></xsd:documentation>             <xsd:appinfo>               <label>startdate</label>               <key>startdate</key>               <searchable>true</searchable>               <description></description>             </xsd:appinfo>           </xsd:annotation>         </xsd:element>         <xsd:element id="md_D7EAD7A91-6360-BF2F-03393913BE11"         name="Enddate" minOccurs="0" maxOccurs="1" type="dateType">           <xsd:annotation>             <xsd:documentation></xsd:documentation>             <xsd:appinfo>               <label>enddate</label>               <key>enddate</key>               <searchable>true</searchable>               <description></description>             </xsd:appinfo>           </xsd:annotation>         </xsd:element> 		<xsd:element id="md_D7EADC16-791-6360-BF2F-03393913Bs11"         name="MyNumber" minOccurs="0" maxOccurs="1" type="intType">           <xsd:annotation>             <xsd:documentation></xsd:documentation>             <xsd:appinfo>               <label>MyNumber</label>               <key>MyNumber</key>               <searchable>true</searchable>               <description></description>             </xsd:appinfo>           </xsd:annotation>         </xsd:element> 		<xsd:element id="md_D7EADC16-7A91-6360F-03393913Bs11"         name="MyNumber2" minOccurs="0" maxOccurs="1" type="intType">           <xsd:annotation>             <xsd:documentation></xsd:documentation>             <xsd:appinfo>               <label>MyNumber2</label>               <key>MyNumber2</key>               <searchable>true</searchable>               <description></description>             </xsd:appinfo>           </xsd:annotation>         </xsd:element> 		<xsd:element id="md_D7EADC16-7A91-6360-BF2F-033932s11"         name="MyNumber3" minOccurs="0" maxOccurs="1" type="intType">           <xsd:annotation>             <xsd:documentation></xsd:documentation>             <xsd:appinfo>               <label>MyNumber3</label>               <key>MyNumber3</key>               <searchable>true</searchable>               <description></description>             </xsd:appinfo>           </xsd:annotation>         </xsd:element>         <xsd:element id="md_C084580E-43B0-38A7-9F23-033B3"         name="MyTextList" minOccurs="0" maxOccurs="1">           <xsd:annotation>             <xsd:documentation></xsd:documentation>             <xsd:appinfo>               <label>my text list</label>               <key>my text list</key>               <searchable>true</searchable>               <description></description>             </xsd:appinfo>           </xsd:annotation>           <xsd:simpleType>             <xsd:restriction base="listType">               <xsd:enumeration value="a1" />               <xsd:enumeration value="b2" />               <xsd:enumeration value="c3" />             </xsd:restriction>           </xsd:simpleType>         </xsd:element> 		<xsd:element id="md_D7EADC16-7ABF2F-033932s11"         name="MyTest" minOccurs="0" maxOccurs="1" type="textType">           <xsd:annotation>             <xsd:documentation></xsd:documentation>             <xsd:appinfo>               <label>MyTest</label>               <key>MyTest</key>               <searchable>true</searchable>               <description></description>             </xsd:appinfo>           </xsd:annotation>         </xsd:element>       </xsd:sequence>     </xsd:complexType>   </xsd:element>   <xsd:complexType name="textType">     <xsd:simpleContent>       <xsd:extension base="xsd:string" />     </xsd:simpleContent>   </xsd:complexType>     <xsd:complexType name="intType">     <xsd:simpleContent>       <xsd:extension base="xsd:long" />     </xsd:simpleContent>   </xsd:complexType>   <xsd:complexType name="dateType">     <xsd:simpleContent>       <xsd:extension base="xsd:long" />     </xsd:simpleContent>   </xsd:complexType>   <xsd:complexType name="objectType">     <xsd:simpleContent>       <xsd:extension base="xsd:string" />     </xsd:simpleContent>   </xsd:complexType>   <xsd:simpleType name="listType">     <xsd:restriction base="xsd:string" />   </xsd:simpleType> </xsd:schema>';
		
		$metadataClient = KalturaMetadataClientPlugin::get($client);
		$metadataProfile = $metadataClient->metadataProfile->add($metadataProfile, $metadataProfileXsd);
		
		KalturaGlobalData::setData("@METADATA_SEARCH_PROFILE_ID@", $metadataProfile->id);
		KalturaGlobalData::setData("@METADATA_SEARCH_FIELD_NAME1@", '/*[local-name()=\'metadata\']/*[local-name()=\'MyTest\']');
		KalturaGlobalData::setData("@METADATA_SEARCH_FIELD_VALUE1@", 'myTest');
		KalturaGlobalData::setData("@METADATA_SEARCH_FIELD_NAME2@", '/*[local-name()=\'metadata\']/*[local-name()=\'Startdate\']');
		KalturaGlobalData::setData("@METADATA_SEARCH_FIELD_VALUE2@", '1310503500');
		KalturaGlobalData::setData("@METADATA_SEARCH_FIELD_NAME3@", '/*[local-name()=\'metadata\']/*[local-name()=\'MyNumber\']');
		
		
		//add entries
		$entries = array();
		
		$xmlData = array (
			0 => '<metadata><Startdate>1310503400</Startdate><Enddate>1310504000</Enddate><MyNumber>6</MyNumber><MyNumber2>2</MyNumber2><MyNumber3>3</MyNumber3><MyTextList>a1</MyTextList><MyTest>myTest</MyTest></metadata>',
			1 => '<metadata><Startdate>1310503000</Startdate><Enddate>1310508000</Enddate><MyNumber>3</MyNumber><MyNumber2>4</MyNumber2><MyNumber3>5</MyNumber3><MyTextList>b2</MyTextList><MyTest>just some words...</MyTest></metadata>',
			2 => '<metadata><Startdate>1310504000</Startdate><Enddate>1310508000</Enddate><MyNumber>2</MyNumber><MyNumber2>5</MyNumber2><MyNumber3>6</MyNumber3><MyTextList>b2</MyTextList><MyTest>just some words...</MyTest></metadata>',
			3 => '<metadata><Startdate>1310508000</Startdate><Enddate>1310509000</Enddate><MyNumber>5</MyNumber><MyNumber2>2</MyNumber2><MyNumber3>8</MyNumber3><MyTextList>a1</MyTextList><MyTest>myTest</MyTest></metadata>',
			4 => '<metadata><Startdate>1310504000</Startdate><Enddate>1310509000</Enddate><MyNumber>4</MyNumber><MyNumber2>4</MyNumber2><MyNumber3>4</MyNumber3><MyTextList>a1</MyTextList><MyTest>myTest</MyTest></metadata>'
		);
				
		$entry = new KalturaMediaEntry();		
		for ($i =0 ; $i < count($xmlData) ; $i++)
		{
			$entry->id = null;
			$entry->name ='Entry For metadataSearch ' . $i;
			$entry->type = KalturaEntryType::MEDIA_CLIP;
			$entry->mediaType = KalturaMediaType::VIDEO;
			$newEntry = $client->media->add($entry, KalturaEntryType::MEDIA_CLIP);
			
			$contentResource = new KalturaUrlResource();
			$contentResource->url = "http://sites.google.com/site/demokmc/Home/titanicin5seconds.flv";
			$client->media->addContent($newEntry->id, $contentResource);
			
			//add metadata 
			sleep(1); //sync with sphinx
			$entries[$i] = $newEntry->id;

			$metadataClient->metadata->add($metadataProfile->id, KalturaMetadataObjectType::ENTRY, $newEntry->id, $xmlData[$i]);
		}
		
		$expectedResults = $entries[4] . ',' . $entries[3];
		
		KalturaGlobalData::setData("@METADATA_SEARCH_ENTRIES_IDS@", $expectedResults);
	}
}