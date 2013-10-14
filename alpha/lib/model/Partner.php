<?php

/**
 * Subclass for representing a row from the 'partner' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class Partner extends BasePartner
{	
	const BATCH_PARTNER_ID = -1;
	const ADMIN_CONSOLE_PARTNER_ID = -2;
	
	const PARTNER_THAT_DOWS_NOT_EXIST = -1000;
	
	const VALIDATE_WRONG_LOGIN = -1;
	const VALIDATE_WRONG_PASSWORD = -2;
	const VALIDATE_TOO_MANY_INVALID_LOGINS = -3;
	const VALIDATE_PARTNER_BLOCKED = -4;
	const VALIDATE_LKS_DISABLED = -10;
	
	const PARTNER_STATUS_DELETED = 0;
	const PARTNER_STATUS_ACTIVE = 1;
	const PARTNER_STATUS_CONTENT_BLOCK = 2;
	const PARTNER_STATUS_FULL_BLOCK = 3;
	
	const CONTENT_BLOCK_SERVICE_CONFIG_ID = 'services_limited_partner.ct';
	const FULL_BLOCK_SERVICE_CONFIG_ID = 'services_block.ct';
	
	const MAX_ALLOWD_INVALID_LOGIN_COUNT = 10;
	
	const MAX_ACCESS_CONTROLS = 24;
	
	//this is not enforced anymore, but for default pager size when listing ctagoeries (since we didn't have pager before flacon)
	const MAX_NUMBER_OF_CATEGORIES = 1500;
	
	// added by Tan-Tan, 06/10/09
	const PARTNER_TYPE_KMC = 1;
	const PARTNER_TYPE_OTHER = 2;
	const PARTNER_TYPE_BATCH = 3;
	
	const PARTNER_TYPE_WIKI = 100;
	const PARTNER_TYPE_WORDPRESS = 101;
	const PARTNER_TYPE_DRUPAL = 102;
	const PARTNER_TYPE_DEKIWIKI = 103;
	const PARTNER_TYPE_MOODLE = 104;
	const PARTNER_TYPE_COMMUNITY_EDITION = 105;
	const PARTNER_TYPE_JOOMLA = 106;
	const PARTNER_TYPE_BLACKBOARD = 107;
	const PARTNER_TYPE_SAKAI = 108;
	const PARTNER_TYPE_ADMIN_CONSOLE = 109;
	
	public static $s_content_root ;
	
	public function save(PropelPDO $con = null)
	{
		PartnerPeer::removePartnerFromCache( $this->getId() );
		
		return parent::save ( $con ) ;		
	}
	
	public function validateSecret ( $partner_secret , $partner_key , &$ks_max_expiry_in_seconds , $admin = false )
	{
		if ( $this->getInvalidLoginCount() > self::MAX_ALLOWD_INVALID_LOGIN_COUNT )
		{
//			return self::VALIDATE_TOO_MANY_INVALID_LOGINS;
		}
		
		$secret_to_match = $admin ? $this->getAdminSecret() : $this->getSecret() ;
		if ( $partner_secret == $secret_to_match )
		{
			$ks_max_expiry_in_seconds = $this->getKsMaxExpiryInSeconds();
			if ( $this->getInvalidLoginCount() > 0 )
			{
				$this->setInvalidLoginCount( 0 ); // reset the invalid login count 
				$this->save();
			}
			return true;
		}
		else
		{
			// same invalid count is done both for secret and for admin_secret - 
			// TODO - split counts ?
			$this->setInvalidLoginCount( $this->getInvalidLoginCount() + 1 );
			$this->save();
			
			return self::VALIDATE_WRONG_PASSWORD;
		}
	}
	
	
	// TODO - this should be part of the data on a partner in the DB
	public static function allowMultipleRoughcuts ( $partner_id )
	{
		return false;
		//if ( in_array ( $partner_id , array ( 1, 2, 8, 18 ) ) ) return false; // only for wikia 
		//return true;
	}
	
	public function getExtraData ( $lang = null )
	{
		if ( empty ( $lang ) ) $lang = "en";
		$path = self::getPartnerContentPath( );
		$path .= "/" . $this->getId() . "/Config{$lang}.txt";
		if ( !file_exists ( $path ))
			return null;
		return file_get_contents( $path );
	}
	
	// TODO - this will be called many times - cache with memcache in the best format we find 
	public function getExtraDataParsed ( $lang = null )
	{
		if ( empty ( $lang ) ) $lang = "en";
		
		$extra_data_str = $this->getExtraData( $lang );
		if ( empty (  $extra_data_str ) )
			return null;
		
		$lines = explode ( "\n" , $extra_data_str );
		
		$name_value = array();
		foreach ( $lines as $line )
		{
			list ( $name , $value ) = explode ( "=" , $line , 2); // stop after the second '=" - the value side might have it in it's content 
			$name_value[$name] = $value; 
		}
		return $name_value;
	}	
	
	public static function getPartnerContentPath ( )
	{
		if ( ! self::$s_content_root )
		{
			self::$s_content_root = myContentStorage::getFSContentRootPath(); 
		}
		
		return self::$s_content_root ;
	}
	
	public function getWidgetImagePath()
	{
		return myContentStorage::getGeneralEntityPath("partner/widget", $this->getId(), $this->getId(), ".gif" );
	}
	
	public function getName ()
	{
		return $this->getPartnerName();
	}
	
	public function setName ( $v)
	{
		return $this->setPartnerName( $v );
	}
	
	public function getSubp ()
	{
		return 100 * $this->getId();
	}
	
	public function getSubpid ()
	{
		return $this->getSubp();
	}
	
	public function getDefaultWidgetId()
	{
		return "_" . $this->getId(); 	
	}
	
	private $m_partner_stats;
	public function getPartnerStats()
	{
		return $this->m_partner_stats;
	}
	
	public function setPartnerStats( $v)
	{
		$this->m_partner_stats = $v;
	}
	
	private static $s_config_params = array ( );

	public function getUseDefaultKshow()	{		return $this->getFromCustomData( "useDefaultKshow" , null , true );	}
	public function setUseDefaultKshow( $v )	{		return $this->putInCustomData( "useDefaultKshow", $v );	}
		
	public function getShouldForceUniqueKshow()
	{
		return $this->getFromCustomData( "forceUniqueKshow" , null , false );
	}
	
	public function setShouldForceUniqueKshow( $v )
	{
		return $this->putInCustomData( "forceUniqueKshow", $v );	
	}
	
	public function getReturnDuplicateKshow()
	{
		return $this->getFromCustomData( "returnDuplicateKshow" , null , true );
	}
	
	public function setReturnDuplicateKshow( $v )
	{
		return $this->putInCustomData( "returnDuplicateKshow", $v );
	}

	public function getAllowQuickEdit()
	{
		return $this->getFromCustomData( "allowQuickEdit" , null , true );
	}
	
	public function setAllowQuickEdit( $v )
	{
		return $this->putInCustomData( "allowQuickEdit", $v );
	}

	
	public function getConversionString()
	{
		return $this->getFromCustomData( "conversionString" , null  );
	}
	
	public function setConversionString( $v )
	{
		return $this->putInCustomData( "conversionString", $v );
	}	

	public function getFlvConversionString()
	{
		return $this->getFromCustomData( "flvConversionString" , null  );
	}
	
	public function setFlvConversionString( $v )
	{
		return $this->putInCustomData( "flvConversionString", $v );
	}	
	
	public function getPasswordStructureValidations()
	{
		return $this->getFromCustomData( "passwordStructureValidation" , null  );
	}
	
	public function setPasswordStructureValidations( $v )
	{
		return $this->putInCustomData( "passwordStructureValidation", $v );
	}
	
	
	public function getInvalidPasswordStructureMessage(){
		$invalidPasswordStructureMessage = kConf::get('invalid_password_structure_message');
		$structureValidations = $this->getPasswordStructureValidations();
		if($structureValidations && is_array($structureValidations)){
			$invalidPasswordStructureMessage ='';
			foreach ($structureValidations as $structureValidation){
				$invalidPasswordStructureMessage.= $structureValidation[1];	
			}
		}
		return $invalidPasswordStructureMessage;
	}
	
	public function getPasswordStructureRegex(){
		$passwordStructureRegex = null;
		$structureValidations = $this->getPasswordStructureValidations();
		if($structureValidations && is_array($structureValidations)){
			$passwordStructureRegex = array();
			foreach ($structureValidations as $structureValidation){
				if($structureValidation[0])
					$passwordStructureRegex[] = $structureValidation[0];	
			}
		}		
		return $passwordStructureRegex;
	}
	
	
	
	/**
	 * @deprecated getDefaultConversionProfileId should be used and is used by the new conversion profiles
	 * @deprecated once the old conversion mechanism is completely obsolete - have this changed to the DEFAULT_COVERSION_PROFILE_TYPE 
	 * @return string
	 */
	public function getDefConversionProfileType()
	{
		$res = $this->getFromCustomData( "defConversionProfileType" , null , ConversionProfile::DEFAULT_COVERSION_PROFILE_TYPE  );
		if ( $res ) return  $res;
		return ConversionProfile::DEFAULT_COVERSION_PROFILE_TYPE;
		//return $this->getFromCustomData( "defConversionProfileType" , null , null  );
	}
	
	/**
	 * @param string $v
	 * @deprecated setDefaultConversionProfileId should be used and is used by the new conversion profiles 
	 */
	public function setDefConversionProfileType( $v )
	{
		return $this->putInCustomData( "defConversionProfileType", $v );
	}	

	
	/**
	 * @deprecated getDefaultConversionProfileId should be used and is used by the new conversion profiles 
	 * @return string
	 */
	public function getCurrentConversionProfileType()
	{
		$res = $this->getFromCustomData( "curConvProfType" , null );
		return $res;
	}
	
	/**
	 * @param string $v
	 * @deprecated setDefaultConversionProfileId should be used and is used by the new conversion profiles 
	 */
	public function setCurrentConversionProfileType( $v )
	{
		return $this->putInCustomData( "curConvProfType", $v );
	}	
	
	/**
	 * Get the default conversion profile id for the partner
	 * 
	 * @return int 
	 */
	public function getDefaultConversionProfileId()
	{
		return $this->getFromCustomData("defaultConversionProfileId");
	}
	
	/**
	 * Set the default access control profile id for the partner
	 *  
	 * @param int $v
	 * @return int
	 */
	public function setDefaultAccessControlId($v)
	{
		$this->putInCustomData("defaultAccessControlId", $v);
	}
	
	/**
	 * Get the default access control profile id for the partner
	 * 
	 * @return int 
	 */
	public function getDefaultAccessControlId()
	{
		return $this->getFromCustomData("defaultAccessControlId");
	}
	
	/**
	 * Set the default conversion profile id for the partner
	 *  
	 * @param int $v
	 * @return int
	 */
	public function setDefaultConversionProfileId($v)
	{
		$this->putInCustomData("defaultConversionProfileId", $v);
	}
	
	public function getNotificationsConfig()
	{
		return $this->getFromCustomData( "notificationsConfig" , null  );
	}
	
	public function setNotificationsConfig( $v )
	{
		return $this->putInCustomData( "notificationsConfig", $v );
	}	
	
	public function getAllowMultiNotification()
	{
		return $this->getFromCustomData( "allowMultiNotification" , null  );
	}
	
	public function setAllowMultiNotification( $v )
	{
		return $this->putInCustomData( "allowMultiNotification", $v );
	}

	public function getAllowLks()
	{
		return $this->getFromCustomData( "allowLks" , false  );
	}
	
	public function setAllowLks( $v )
	{
		return $this->putInCustomData( "allowLks", $v );
	}		
	
	public function getMaxUploadSize()
	{
		return $this->getFromCustomData( "maxUploadSize" , null, "150"  );
	}
	
	public function setMaxUploadSize( $v )
	{
		return $this->putInCustomData( "maxUploadSize", $v );
	}

	public function getMergeEntryLists()
	{
		return $this->getFromCustomData( "mergeEntryLists" , false  );
	}
	
	public function setMergeEntryLists( $v )
	{
		return $this->putInCustomData( "mergeEntryLists", $v );
	}

	public function getPartnerSpecificServices()
	{
		return $this->getFromCustomData( "partnerSpecificServices" , false  );
	}
	
	public function setPartnerSpecificServices( $v )
	{
		return $this->putInCustomData( "partnerSpecificServices", $v );
	}

	
	public function getAllowAnonymousRanking()	{		return $this->getFromCustomData( "allowAnonymousRanking" , null, false  );	}
	public function setAllowAnonymousRanking( $v )	{		return $this->putInCustomData( "allowAnonymousRanking", $v );	}
	
	public function getMatchIp()	{		return $this->getFromCustomData( "matchIp" , null, false  );	}
	public function setMatchIp( $v )	{		return $this->putInCustomData( "matchIp", $v );	}

	public function getDefThumbOffset()	{		return $this->getFromCustomData( "defThumbOffset" , false, 3 );	}
	public function setDefThumbOffset( $v )	{		return $this->putInCustomData( "defThumbOffset", $v );	}
	
	public function getDefThumbDensity()	{		return $this->getFromCustomData( "defThumbDensity" , false  );	}
	public function setDefThumbDensity( $v )	{		return $this->putInCustomData( "defThumbDensity", $v );	}
	
	public function getHost()	{		return $this->getFromCustomData( "host" , null, false  );	}
	public function setHost( $v )	{		return $this->putInCustomData( "host", $v );	}
		
	public function getCdnHost()	{		return $this->getFromCustomData( "cdnHost" , null, false  );	}
	public function setCdnHost( $v )	{		return $this->putInCustomData( "cdnHost", $v );	}

	public function getPlaybackCdnHost()    {               return $this->getFromCustomData( "playbackCdnHost" , null, false  ); }
	public function setPlaybackCdnHost( $v )        {               return $this->putInCustomData( "playbackCdnHost", $v ); }

	public function getDefaultDeliveryCode()    {               return $this->getFromCustomData( "defaultDeliveryCode" , null, false  ); }
	public function setDefaultDeliveryCode( $v )        {               return $this->putInCustomData( "defaultDeliveryCode", $v ); }
	
	public function getThumbnailHost()	{		return $this->getFromCustomData( "thumbnailHost" , null, false  );	}
	public function setThumbnailHost( $v )	{		return $this->putInCustomData( "thumbnailHost", $v );	}	
		
	public function getForceCdnHost()	{		return $this->getFromCustomData( "forceCdnHost" , null, false  );	}
	public function setForceCdnHost( $v )	{		return $this->putInCustomData( "forceCdnHost", $v );	}	

	public function getEnforceHttpsApi()	{		return $this->getFromCustomData( "enforceHttpsApi" , null, false  );	}
	public function setEnforceHttpsApi( $v )	{		return $this->putInCustomData( "enforceHttpsApi", $v );	}
	
	public function getFeaturesStatus()	
	{		
		$featuresStatus = $this->getFromCustomData(null, 'featuresStatuses');
		if (is_null($featuresStatus)){
			$featuresStatus = array();
		}
		
		return $featuresStatus;
	}
	
	public function getJobTypeQuota($jobType, $jobSubType) {
		$jobTypeQuota = $this->getFromCustomData("jobTypeQuota");
		if(!$jobTypeQuota) 
			return null;
		
		if(isset($jobTypeQuota[$jobType . '-' . $jobSubType]))
			return $jobTypeQuota[$jobType . '-' . $jobSubType];
		if(isset($jobTypeQuota[$jobType . '-*']))
			return $jobTypeQuota[$jobType . '-*'];
		if(isset($jobTypeQuota['*']))
			return $jobTypeQuota['*'];
		
		return null;
	
	}
	
	public function setJobTypeQuota(array $v) { return $this->putInCustomData("jobTypeQuota", $v ); }
		
	public function addFeaturesStatus($type, $value = 1)
	{
		$newFeatureStatus = new kFeatureStatus();
		$newFeatureStatus->setType($type);
		$newFeatureStatus->setValue($value);
		
		$this->putInCustomData($type, $newFeatureStatus, 'featuresStatuses');
		$this->save();
	}
	
	public function removeFeaturesStatus($type)
	{
		$this->removeFromCustomData($type, 'featuresStatuses');
		$this->save();
	}
	
	public function getFeaturesStatusByType($type)
	{
		return $this->getFromCustomData($type, 'featuresStatuses');
	}
	
	public function resetFeaturesStatusByType($type)
	{
		$openStatuses = array(BatchJob::BATCHJOB_STATUS_ALMOST_DONE,
							  BatchJob::BATCHJOB_STATUS_RETRY,
							  BatchJob::BATCHJOB_STATUS_PENDING,
							  BatchJob::BATCHJOB_STATUS_QUEUED,
							  BatchJob::BATCHJOB_STATUS_PROCESSING,
							  BatchJob::BATCHJOB_STATUS_PROCESSED,
							  BatchJob::BATCHJOB_STATUS_MOVEFILE
							);
		
		$criteria = new Criteria();
		$criteria->add(BatchJobPeer::PARTNER_ID, $this->getId());
		$criteria->add(BatchJobPeer::JOB_TYPE, BatchJobType::INDEX);
		$criteria->add(BatchJobPeer::JOB_SUB_TYPE, $type);
		$criteria->add(BatchJobPeer::STATUS, $openStatuses, Criteria::IN);
		
		$batchJob = BatchJobPeer::doSelectOne($criteria);
		
		if($batchJob)
		{
			$this->addFeaturesStatus($type);
		}
		else
		{
			$this->removeFeaturesStatus($type);
		}
	}
	
	/**
	 * @return bool
	 * @deprecated
	 */
	public function getRestrictThumbnailByKs()	{		return $this->getFromCustomData( "restrictThumbnailByKs" , null, false  );	}
	
	/**
	 * @return bool
	 * @deprecated
	 */
	public function setRestrictThumbnailByKs( $v )	{		return $this->putInCustomData( "restrictThumbnailByKs", $v );	}

	public function getSupportAnimatedThumbnails()	{		return $this->getFromCustomData( "supportAnimatedThumbnails" , null, false  );	}
	public function setSupportAnimatedThumbnails( $v )	{		return $this->putInCustomData( "supportAnimatedThumbnails", $v );	}
		
	public function getRtmpUrl()	{		return $this->getFromCustomData( "rtmpUrl" , null, false  );	}
	public function setRtmpUrl( $v )	{		return $this->putInCustomData( "rtmpUrl", $v );	}	
		
	public function getIisHost()	{		return $this->getFromCustomData( "iisHost" , null, false  );	}
	public function setIisHost( $v )	{		return $this->putInCustomData( "iisHost", $v );	}	
	
	public function getLandingPage()	{		return $this->getFromCustomData( "landingPage" , null, null  );	}
	public function setLandingPage( $v )	{		return $this->putInCustomData( "landingPage", $v );	}	

	public function getUserLandingPage()	{		return $this->getFromCustomData( "userLandingPage" , null, null  );	}
	public function setUserLandingPage( $v )	{		return $this->putInCustomData( "userLandingPage", $v );	}	
	
	public function getMaxConccurentImports()	{		return $this->getFromCustomData( "maxConccurentImports" , null, null  );	}
	public function setMaxConccurentImports( $v )	{		return $this->putInCustomData( "maxConccurentImports", $v );	}

	public function getIsFirstLogin() { return (bool)$this->getFromCustomData("isFirstLogin", null, false); } // if not set to true explicitly, default will be false
	public function setIsFirstLogin( $v ) { $this->putInCustomData("isFirstLogin", (bool)$v); } 
	
	public function getTemplatePartnerId() { return $this->getFromCustomData("templatePartnerId", null, 0); }
	public function setTemplatePartnerId( $v ) { $this->putInCustomData("templatePartnerId", (int)$v); } 
	
	public function getIgnoreSeoLinks() { return $this->getFromCustomData("ignoreSeoLinks", null, false); }
	public function setIgnoreSeoLinks( $v ) { $this->putInCustomData("ignoreSeoLinks", (bool)$v); } 
	
	public function getLicensedJWPlayer() { return $this->getFromCustomData("licensedJWPlayer", null, 0); }
	public function setLicensedJWPlayer( $v ) { $this->putInCustomData("licensedJWPlayer", (int)$v); } 

	public function getAddEntryMaxFiles() { return $this->getFromCustomData("addEntryMaxFiles", null, myFileUploadService::MAX_FILES); }
	public function setAddEntryMaxFiles( $v ) { $this->putInCustomData("addEntryMaxFiles", (int)$v); }
	
	private function getCategoriesLockTime() { return $this->getFromCustomData("categoriesLockTime", null, 0); }
	private function setCategoriesLockTime( $v ) { $this->putInCustomData("categoriesLockTime", (int)$v); }
		
	private function getCategoriesLock() { return $this->getFromCustomData("categoriesLock", 'category', false); }
	private function setCategoriesLock( $v ) { $this->putInCustomData("categoriesLock", (bool)$v, 'category'); }
	
	public function getAdSupported() { return $this->getFromCustomData("adSupported", null, 0); }
	public function setAdSupported( $v ) { $this->putInCustomData("adSupported", (int)$v); } 

	public function getMaxBulkSize() { return $this->getFromCustomData("maxBulk", null, null); }
	public function setMaxBulkSize( $v ) { $this->putInCustomData("maxBulk", (int)$v); } 

	public function getStorageServePriority() { return $this->getFromCustomData("storageServePriority", null, 0); }
	public function setStorageServePriority( $v ) { $this->putInCustomData("storageServePriority", (int)$v); } 
	
	public function getStorageDeleteFromKaltura() { return $this->getFromCustomData("storageDeleteFromKaltura", null, 0); }
	public function setStorageDeleteFromKaltura( $v ) { $this->putInCustomData("storageDeleteFromKaltura", (int)$v); } 
	
	public function getAppStudioExampleEntry() { return $this->getFromCustomData("appStudioExampleEntry", null); }
	public function setAppStudioExampleEntry( $v ) { $this->putInCustomData("appStudioExampleEntry", $v); } 
	
	public function getAppStudioExamplePlayList0() { return $this->getFromCustomData("appStudioExamplePlayList0", null); }
	public function setAppStudioExamplePlayList0( $v ) { $this->putInCustomData("appStudioExamplePlayList0", $v); } 
	
	public function getAppStudioExamplePlayList1() { return $this->getFromCustomData("appStudioExamplePlayList1", null); }
	public function setAppStudioExamplePlayList1( $v ) { $this->putInCustomData("appStudioExamplePlayList1", $v); }

	/** Partner Packges and classification **/
	public function getPartnerPackageClassOfService() { return $this->getFromCustomData("partnerPackageClassOfService", null); }
	public function setPartnerPackageClassOfService( $v ) { $this->putInCustomData("partnerPackageClassOfService", $v); } 
	
	public function getVerticalClasiffication() { return $this->getFromCustomData("verticalClasiffication", null); }
	public function setVerticalClasiffication( $v ) { $this->putInCustomData("verticalClasiffication", $v); } 
		
	/** added DelivryBlockCountries param for having per-partner ability to block serving of files to specific country **/
	public function getDelivryBlockCountries() { return $this->getFromCustomData("delivryBlockCountries", null); }
	public function setDelivryBlockCountries( $v ) { $this->putInCustomData("delivryBlockCountries", $v); }
	
	/** partner CRM information **/
	public function getCrmId() { return $this->getFromCustomData("crmId", null); }
	public function setCrmId( $v ) { $this->putInCustomData("crmId", $v); }
	
	public function getCrmLink() { return $this->getFromCustomData("crmLink", null); }
	public function setCrmLink( $v ) { $this->putInCustomData("crmLink", $v); }	
	
	/** partner is for internal usage only **/
	public function getInternalUse() { return $this->getFromCustomData("internalUse", false); }
	public function setInternalUse( $v ) { $this->putInCustomData("internalUse", $v); }	
	
	/** added deliveryRestrictions param for having per-partner ability to block serving of files to specific cdns and protocols **/
	public function getDeliveryRestrictions() { return $this->getFromCustomData("deliveryRestrictions", null); }
	public function setDeliveryRestrictions( $v ) { $this->putInCustomData("deliveryRestrictions", $v); }
			
	/** added disableAkamaiHDNetwork param for having per-partner ability to disable Akamai HD Network feature (GUI in KMC preview & embed) **/
	public function getDisableAkamaiHDNetwork() { return $this->getFromCustomData("disableAkamaiHDNetwork", null); }
	public function setDisableAkamaiHDNetwork( $v ) { $this->putInCustomData("disableAkamaiHDNetwork", $v); }

	public function getImportRemoteSourceForConvert() { return $this->getFromCustomData("importRemoteSourceForConvert", null, false); }
	public function setImportRemoteSourceForConvert( $v ) { $this->putInCustomData("importRemoteSourceForConvert", $v); }
	
	/** bulkupload notification email **/
	public function getEnableBulkUploadNotificationsEmails() {return $this->getFromCustomData("enableBulkUploadNotificationsEmails", null, false); }
	public function setEnableBulkUploadNotificationsEmails($v) { $this->putInCustomData("enableBulkUploadNotificationsEmails", $v); }
	
	public function getKSVersion() { return $this->getFromCustomData( "ksVersion" , null, 1  );	}
	public function setKSVersion( $v ) { return $this->putInCustomData( "ksVersion", $v );	}
	
	public function getShouldApplyAccessControlOnEntryMetadata() { return $this->getFromCustomData( "shouldApplyAccessControlOnEntryMetadata" , null, false ); }
	public function setShouldApplyAccessControlOnEntryMetadata( $v ) { return $this->putInCustomData( "shouldApplyAccessControlOnEntryMetadata", $v ); }

	public function getDefaultDeliveryType() { return $this->getFromCustomData("defaultDeliveryType", null); }
	public function setDefaultDeliveryType( $v ) { $this->putInCustomData("defaultDeliveryType", $v); }

	public function getDefaultEmbedCodeType() { return $this->getFromCustomData("defaultEmbedCodeType", null); }
	public function setDefaultEmbedCodeType( $v ) { $this->putInCustomData("defaultEmbedCodeType", $v); }
	
	public function getDisabledDeliveryTypes() { return $this->getFromCustomData("disabledDeliveryTypes", array()); }
	public function setDisabledDeliveryTypes(array $v ) { $this->putInCustomData("disabledDeliveryTypes", $v); }
	
	public function getDeliveryTypes()
	{
		$map = kConf::getMap('players');
		$deliveryTypes = $map['delivery_types'];
		
		$disabledDeliveryTypes = $this->getDisabledDeliveryTypes();
		if($disabledDeliveryTypes)
		{
			foreach($disabledDeliveryTypes as $disabledDeliveryType)
			{
				if(isset($deliveryTypes[$disabledDeliveryType]))
					unset($deliveryTypes[$disabledDeliveryType]);
			}
		}
			
		return $deliveryTypes;
	} 
	
	public function getEmbedCodeTypes()
	{
		$map = kConf::getMap('players');
		return $map['embed_code_types'];
	} 
	
	public function getBulkUploadNotificationsEmail() 
	{ 
		$email = $this->getFromCustomData("bulkUploadNotificationsEmail", null, null);

		if (is_null($email))
			return $this->getAdminEmail();
			
		return $email;			
	}
	
	public function setBulkUploadNotificationsEmail($v) { $this->putInCustomData("bulkUploadNotificationsEmail", $v); }
	
	
	
	/** monitor Usage Expiry **/
	public function getExtendedFreeTrail() { return $this->getFromCustomData("extendedFreeTrail", null); }
	public function setExtendedFreeTrail( $v ) { $this->putInCustomData("extendedFreeTrail", $v); }
	
	public function getExtendedFreeTrailExpiryReason() { return $this->getFromCustomData("extendedFreeTrailExpiryReason", null); }
	public function setExtendedFreeTrailExpiryReason( $v ) { $this->putInCustomData("extendedFreeTrailExpiryReason", $v); }
	
	public function getExtendedFreeTrailExpiryDate() { return $this->getFromCustomData("extendedFreeTrailExpiryDate", null); }
	public function setExtendedFreeTrailExpiryDate( $v ) 
	{ 
		$this->setExtendedFreeTrailEndsWarning(false);
		$this->putInCustomData("extendedFreeTrailExpiryDate", $v); 
	}
	
	public function getExtendedFreeTrailEndsWarning() { return $this->getFromCustomData("extendedFreeTrailEndsWarning", null, false); }
	public function setExtendedFreeTrailEndsWarning( $v ) { $this->putInCustomData("extendedFreeTrailEndsWarning", $v); }

	public function getApiAccessControlId() { return $this->getFromCustomData("apiAccessControlId", null, null); }
	public function setApiAccessControlId( $v ) { $this->putInCustomData("apiAccessControlId", $v); }

	
	/** 27Apr2011 - added fields for new registration form **/
	// first name
	public function getFirstName() { return $this->getFromCustomData("firstName", null); }
	public function setFirstName( $v ) { $this->putInCustomData("firstName", $v); }

	// last name
	public function getLastName() { return $this->getFromCustomData("lastName", null); }
	public function setLastName( $v ) { $this->putInCustomData("lastName", $v); }

	// country
	public function getCountry() { return $this->getFromCustomData("country", null); }
	public function setCountry( $v ) { $this->putInCustomData("country", $v); }

	// state
	public function getState() { return $this->getFromCustomData("state", null); }
	public function setState( $v ) { $this->putInCustomData("state", $v); }

	// logout url for partners integrating a single sign on solution
	public function getLogoutUrl() { return $this->getFromCustomData('logoutUrl', null); }
	public function setLogoutUrl($v) { $this->putInCustomData('logoutUrl', $v); }
	
	//kmc language
	public function setKMCLanguage($v) { $this->putInCustomData('language', $v, 'KMC');}
	public function getKMCLanguage() { return $this->getFromCustomData('language', 'KMC', null);}
	
	//default entitlement scope for ks
	public function setDefaultEntitlementEnforcement($v) { $this->putInCustomData('defaultEntitlementEnforcement', $v, 'entitlement');}
	public function getDefaultEntitlementEnforcement() 
	{
		return $this->getFromCustomData('defaultEntitlementEnforcement', 'entitlement', false);
	}
	
	//category work group size - to index all category members on each entry if category size is a group.
	public function setCategoryGroupSize($v) { $this->putInCustomData('categoryGroupSize', $v, 'entitlement');}
	public function getCategoryGroupSize() { return $this->getFromCustomData('categoryGroupSize', 'entitlement', null);}
	
	//strip image profiles and comments when generating the thumbnail
	public function setStripThumbProfile($v) { $this->putInCustomData('stripThumbProfile', $v);}
	public function getStripThumbProfile() { return $this->getFromCustomData('stripThumbProfile');}
	
	// additionalParams - key/value array
	public function getAdditionalParams() 
	{ 
		$obj = $this->getFromCustomData("additionalParams", null);
		return is_array($obj) ? $obj : array();
	}
	
	public function setAdditionalParams($v) 
	{ 
		if (!is_array($v))
			$v = array();
		$this->putInCustomData("additionalParams", $v); 
	}
	
	public function lockCategories()
	{
		$this->setCategoriesLock(true);
		$this->save();
	}
	
	public function unlockCategories()
	{
		$this->setCategoriesLock(false);
		$this->save();
	}	
	
	public function isCategoriesLocked()
	{
		return $this->getCategoriesLock();
		}

	public function getOpenId ()
	{
		return "http://www.kaltura.com/openid/pid/" . $this->getId();
	}
	
	public function getServiceConfig ()
	{
		$service_config_id = $this->getServiceConfigId() ;
		return  myServiceConfig::getInstance ( $service_config_id );	
	}
	
	public function getMaxLoginAttempts()
	{
		$maxAttempts = $this->getFromCustomData('max_login_attempts', null, null);
		if (!$maxAttempts) {
			$maxAttempts = kConf::get('user_login_max_wrong_attempts');
		}
		return $maxAttempts;
	}
	
	public function setMaxLoginAttempts($maxAttempts)
	{
		$this->putInCustomData('max_login_attempts', $maxAttempts, null);
	}
	
	
	public function getLoginBlockPeriod()
	{
		$blockPeriod = $this->getFromCustomData('login_blocked_period', null, null);
		if (!$blockPeriod) {
			$blockPeriod = kConf::get('user_login_block_period');
		}
		return $blockPeriod;
	}
	
	public function setLoginBlockPeriod($blockPeriod)
	{
		$this->putInCustomData('login_blocked_period', $blockPeriod, null);
	}
	
	
	public function getNumPrevPassToKeep()
	{
		$prevPass = $this->getFromCustomData('num_prev_passwords_to_keep', null, null);
		if (!$prevPass) {
			$prevPass = kConf::get('user_login_num_prev_passwords_to_keep');
		}
		return $prevPass;
	}
	
	public function setNumPrevPassToKeep($numToKeep)
	{
		$this->putInCustomData('num_prev_passwords_to_keep', $numToKeep, null);
	}

	
	public function getPassReplaceFreq()
	{
		$replaceFreq = $this->getFromCustomData('password_replace_freq', null, null);
		if (!$replaceFreq) {
			$replaceFreq = kConf::get('user_login_password_replace_freq');
		}
		return $replaceFreq;
	}
	
	public function setPassReplaceFreq($replaceFreq)
	{
		$this->putInCustomData('password_replace_freq', $replaceFreq, null);
	}
	
	public function setLoginUsersQuota($v)				{$this->putInCustomData('login_users_quota', $v);}
	public function setAdminLoginUsersQuota($v)			{$this->putInCustomData('admin_login_users_quota', $v);}
	public function setPublishersQuota($v)				{$this->putInCustomData('publishers_quota', $v);}
	public function setBandwidthQuota($v)				{$this->putInCustomData('bandwidth_quota', $v);}
	public function setStreamEntriesQuota($v)			{$this->putInCustomData('stream_entries_quota', $v);}
	public function setEntriesQuota($v)					{$this->putInCustomData('entries_quota', $v);}
	public function setMonthlyStorage($v)				{$this->putInCustomData('monthly_storage', $v);}
	public function setMonthlyStorageAndBandwidth($v)	{$this->putInCustomData('monthly_storage_and_bandwidth', $v);}
	public function setEndUsers($v)						{$this->putInCustomData('end_users', $v);}
	public function setAccessControls($v)				{$this->putInCustomData('access_controls', $v);}
	
	public function setLoginUsersOveragePrice($v)		{$this->putInCustomData('login_users_overage_price', $v);}
	public function setAdminLoginUsersOveragePrice($v)	{$this->putInCustomData('admin_login_users_overage_price', $v);}
	public function setPublishersOveragePrice($v)		{$this->putInCustomData('publishers_overage_price', $v);}
	public function setBandwidthOveragePrice($v)		{$this->putInCustomData('bandwidth_overage_price', $v);}
	public function setStreamEntriesOveragePrice($v)	{$this->putInCustomData('stream_entries_overage_price', $v);}
	public function setEntriesOveragePrice($v)			{$this->putInCustomData('entries_overage_price', $v);}
	public function setMaxLoginAttemptsOveragePrice($v)	{$this->putInCustomData('login_attempts_overage_price', $v);}
	public function setMaxBulkSizeOveragePrice($v)		{$this->putInCustomData('bulk_size_overage_price', $v);}
	public function setMonthlyStorageOveragePrice($v)	{$this->putInCustomData('monthly_storage_overage_price', $v);}
	public function setMonthlyStorageAndBandwidthOveragePrice($v)	{$this->putInCustomData('monthly_storage_and_bandwidth_overage_price', $v);}
	public function setEndUsersOveragePrice($v)			{$this->putInCustomData('end_users_overage_price', $v);}
	
	public function setAdminLoginUsersOverageUnit($v)	{$this->putInCustomData('admin_login_users_overage_unit', $v);}
	public function setPublishersOverageUnit($v)		{$this->putInCustomData('publishers_overage_unit', $v);}
	public function setBandwidthOverageUnit($v)		{$this->putInCustomData('bandwidth_overage_unit', $v);}
	public function setStreamEntriesOverageUnit($v)	{$this->putInCustomData('stream_entries_overage_unit', $v);}
	public function setEntriesOverageUnit($v)			{$this->putInCustomData('entries_overage_unit', $v);}
	public function setMonthlyStorageOverageUnit($v)	{$this->putInCustomData('monthly_storage_overage_unit', $v);}
	public function setMonthlyStorageAndBandwidthOverageUnit($v)	{$this->putInCustomData('monthly_storage_and_bandwidth_overage_unit', $v);}
	public function setEndUsersOverageUnit($v)			{$this->putInCustomData('end_users_overage_unit', $v);}
	public function setLoginUsersOverageUnit($v)          {$this->putInCustomData('login_users_overage_unit', $v);}
    public function setMaxLoginAttemptsOverageUnit($v)    {$this->putInCustomData('login_attempts_overage_unit', $v);}
    public function setMaxBulkSizeOverageUnit($v)         {$this->putInCustomData('bulk_size_overage_unit', $v);}
    public function setAutoModerateEntryFilter($v)       {$this->putInCustomData('auto_moderate_entry_filter', $v);}
    public function setCacheFlavorVersion($v)       {$this->putInCustomData('cache_flavor_version', $v);}
    
	public function getLoginUsersQuota()				{return $this->getFromCustomData('login_users_quota', null, 0);}
	public function getAdminLoginUsersQuota()			{return $this->getFromCustomData('admin_login_users_quota', null, 3);}
	public function getPublishersQuota()				{return $this->getFromCustomData('publishers_quota', null, 0);}
	public function getBandwidthQuota()					{return $this->getFromCustomData('bandwidth_quota', null, 0);}
	public function getStreamEntriesQuota()				{return $this->getFromCustomData('stream_entries_quota', null, 0);}
	public function getEntriesQuota()					{return $this->getFromCustomData('entries_quota', null, 0);}
	public function getMonthlyStorage()					{return $this->getFromCustomData('monthly_storage');}
	public function getMonthlyStorageAndBandwidth()		{return $this->getFromCustomData('monthly_storage_and_bandwidth');}
	public function getEndUsers()						{return $this->getFromCustomData('end_users');}
	public function getAccessControls()					{return $this->getFromCustomData('access_controls', null, self::MAX_ACCESS_CONTROLS);}
	
	public function getLoginUsersOveragePrice()			{return $this->getFromCustomData('login_users_overage_price');}
	public function getAdminLoginUsersOveragePrice()	{return $this->getFromCustomData('admin_login_users_overage_price');}
	public function getPublishersOveragePrice()			{return $this->getFromCustomData('publishers_overage_price');}
	public function getBandwidthOveragePrice()			{return $this->getFromCustomData('bandwidth_overage_price');}
	public function getStreamEntriesOveragePrice()		{return $this->getFromCustomData('stream_entries_overage_price');}
	public function getEntriesOveragePrice()			{return $this->getFromCustomData('entries_overage_price');}
	public function getMaxLoginAttemptsOveragePrice()	{return $this->getFromCustomData('login_attempts_overage_price');}
	public function getMaxBulkSizeOveragePrice()		{return $this->getFromCustomData('bulk_size_overage_price');}
	public function getMonthlyStorageOveragePrice()		{return $this->getFromCustomData('monthly_storage_overage_price');}
	public function getMonthlyStorageAndBandwidthOveragePrice()	{return $this->getFromCustomData('monthly_storage_and_bandwidth_overage_price');}
	public function getEndUsersOveragePrice()			{return $this->getFromCustomData('end_users_overage_price');}
	
	public function getAdminLoginUsersOverageUnit()		{return $this->getFromCustomData('admin_login_users_overage_unit');}
	public function getPublishersOverageUnit()			{return $this->getFromCustomData('publishers_overage_unit');}
	public function getBandwidthOverageUnit()			{return $this->getFromCustomData('bandwidth_overage_unit');}
	public function getStreamEntriesOverageUnit()		{return $this->getFromCustomData('stream_entries_overage_unit');}
	public function getEntriesOverageUnit()				{return $this->getFromCustomData('entries_overage_unit');}
	public function getMonthlyStorageOverageUnit()		{return $this->getFromCustomData('monthly_storage_overage_unit');}
	public function getMonthlyStorageAndBandwidthOverageUnit()	{return $this->getFromCustomData('monthly_storage_and_bandwidth_overage_unit');}
	public function getEndUsersOverageUnit()			{return $this->getFromCustomData('end_users_overage_unit');}
	public function getLoginUsersOverageUnit()          {return $this->getFromCustomData('login_users_overage_unit');}
    public function getMaxLoginAttemptsOverageUnit()    {return $this->getFromCustomData('login_attempts_overage_unit');}
    public function getMaxBulkSizeOverageUnit()         {return $this->getFromCustomData('bulk_size_overage_unit');}
	public function getAutoModerateEntryFilter()         {return $this->getFromCustomData('auto_moderate_entry_filter');}
    public function getCacheFlavorVersion()       {return $this->getFromCustomData('cache_flavor_version');}
	
	
	/**
	 * @return kAkamaiLiveParams
	 */
	public function getAkamaiLiveParams()
	{
		$akamaiLiveParams = unserialize($this->getFromCustomData('akamai_live_params'));
		if (!$akamaiLiveParams) {
			return null;
		}
		return $akamaiLiveParams;
	}
	
	public function setAkamaiLiveParams($akamaiLiveParams)
	{		
		$content = serialize($akamaiLiveParams);
		$this->putInCustomData('akamai_live_params', $content);
	}
	
	/**
	 * @return array
	 */
	public function getAkamaiUniversalStreamingLiveParams ()
	{
		$akamaiUniversalStreamingLiveParams = $this->getFromCustomData('akamai_universal_streaming_live_params');
		if (!$akamaiUniversalStreamingLiveParams)
			return null;
		
		return $akamaiUniversalStreamingLiveParams;
	}
	
	/**
	 * @param array $v
	 */
	public function setAkamaiUniversalStreamingLiveParams ($v)
	{
		$this->putInCustomData('akamai_universal_streaming_live_params', $v);
	}
	
	
	const CUSTOM_DATA_DEFAULT_LIVE_STREAM_ENTRY_SOURCE_TYPE = 'default_live_stream_entry_source_type';
	
	public function setDefaultLiveStreamEntrySourceType($v)	
		{$this->putInCustomData(self::CUSTOM_DATA_DEFAULT_LIVE_STREAM_ENTRY_SOURCE_TYPE, $v);}
    
	public function getDefaultLiveStreamEntrySourceType()
    {
        $defaultSourceType = $this->getFromCustomData(self::CUSTOM_DATA_DEFAULT_LIVE_STREAM_ENTRY_SOURCE_TYPE, null, null);
        if (is_null($defaultSourceType)) {
            $kc = kConf::get('default_live_stream_entry_source_type');
            $evalResult= eval("\$defaultSourceType = $kc;");
            if ($evalResult === false){
            	$defaultSourceType = EntrySourceType::AKAMAI_LIVE;
            } 
        }
        return $defaultSourceType;
    }
    

    const CUSTOM_DATA_LIVE_STREAM_PROVISION_PARAMS = 'live_stream_provision_params';
    
	public function setLiveStreamProvisionParams($v)
		{$this->putInCustomData(self::CUSTOM_DATA_LIVE_STREAM_PROVISION_PARAMS, $v);}
    
	public function getLiveStreamProvisionParams()
    {
        $provisionParams = $this->getFromCustomData(self::CUSTOM_DATA_LIVE_STREAM_PROVISION_PARAMS, null, null);
        if (is_null($provisionParams)) {
            $provisionParams = "";
        }
        return $provisionParams;
    }
	
    
	public function getAdminLoginUsersNumber()
	{
		$c = new Criteria();
		$c->addAnd(kuserPeer::PARTNER_ID, $this->getId());
		$c->addAnd(kuserPeer::IS_ADMIN, true, Criteria::EQUAL);
		$c->addAnd(kuserPeer::STATUS, KuserStatus::DELETED, Criteria::NOT_EQUAL);
		return kuserPeer::doCount($c);
	}
	
	public function setPassResetUrlPrefixName($name)
	{
		$this->putInCustomData('pass_reset_url_prefix_name', $name);
	}
	
	public function getPassResetUrlPrefixName()
	{
		return $this->getFromCustomData('pass_reset_url_prefix_name');
	}
	
	public function setAdminSessionRoleId($roleId)
	{
		if ($roleId)
		{
			$userRole = UserRolePeer::retrieveByPK($roleId);
			if (!$userRole || !in_array($userRole->getPartnerId(),array($this->getId(),PartnerPeer::GLOBAL_PARTNER) ) )
			{
				throw new kPermissionException("A user role with ID [$roleId] does not exist", kPermissionException::USER_ROLE_NOT_FOUND);
			}
		}
		else {
			$roleId = null;
		}
		$this->putInCustomData('admin_session_role_id', $roleId);
	}
		
	public function getAdminSessionRoleId()
	{
		$id = $this->getFromCustomData('admin_session_role_id');
		if (!$id) {
			$id = UserRolePeer::getIdByStrId(UserRoleId::PARTNER_ADMIN_ROLE);
		}
		return $id;
	}
	
	
	public function setUserSessionRoleId($roleId)
	{
		if ($roleId)
		{
			$userRole = UserRolePeer::retrieveByPK($roleId);
			if (!$userRole || !in_array($userRole->getPartnerId(),array($this->getId(),PartnerPeer::GLOBAL_PARTNER) ) )
			{
				throw new kPermissionException("A user role with ID [$roleId] does not exist", kPermissionException::USER_ROLE_NOT_FOUND);
			}
		}
		else {
			$roleId = null;
		}
		$this->putInCustomData('user_session_role_id', $roleId);
	}
	
	
	public function getUserSessionRoleId()
	{
		$id = $this->getFromCustomData('user_session_role_id');
		if (!$id) {
			$id = UserRolePeer::getIdByStrId(UserRoleId::BASE_USER_SESSION_ROLE);
		}
		return $id;
	}
	
	public function setAlwaysAllowedPermissionNames($names)
	{
		$names = trim($names, ',');
		$this->putInCustomData('always_allowed_permission_names', $names);
	}
	
	public function getAlwaysAllowedPermissionNames()
	{
		$names = $this->getFromCustomData('always_allowed_permission_names');
		$namesArray = explode(',', $names);
		if (!count($namesArray) || !in_array(PermissionName::ALWAYS_ALLOWED_ACTIONS, $namesArray)) {
			$names = PermissionName::ALWAYS_ALLOWED_ACTIONS.','.$names;
		}
		$names = trim($names, ',');
		return $names;
	}
	
	public function setLanguage ($v)
	{
		$this->putInCustomData('language', $v);
	}
	
	public function getLanguage ()
	{
		return $this->getFromCustomData('language', null, 'en');
	}
	
	/**
	 * Code to be run after persisting the object
	 * @param PropelPDO $con
	 */
	public function postSave(PropelPDO $con = null)
	{
		// update plugin permissions in the database
		if (is_array($this->setEnabledPlugins))
		{
			foreach($this->setEnabledPlugins as $pluginName => $enabled)
			{
				if ($enabled) {
					PermissionPeer::enablePlugin($pluginName, $this->getId());
				}
				else {
					PermissionPeer::disablePlugin($pluginName, $this->getId());
				}
			}
		}
		
		// update special services permissions in the database
		if (is_array($this->setEnabledServices))
		{
			foreach($this->setEnabledServices as $permissionName => $enabled)
			{
				if ($enabled) {
					PermissionPeer::enableForPartner($permissionName, PermissionType::SPECIAL_FEATURE, $this->getId());
				}
				else {
					PermissionPeer::disableForPartner($permissionName, $this->getId());
				}
			}
		}
				
		$this->setEnabledPlugins = array();
		$this->setEnabledServices = array();
		
		
		
		$ksObj = kSessionUtils::crackKs(kCurrentContext::$ks);
		$currentKuser = null;
		if(is_object($ksObj)){
			$currentKuser = kuserPeer::getKuserByEmail($ksObj->user, -2);
		}
		if ($currentKuser) 
		{
			$allowedPartners = $currentKuser->getAllowedPartners();
			if (isset($allowedPartners) && !empty($allowedPartners)) {
				$partnersArray = array_map('trim', explode(',', $allowedPartners));
				if (!in_array($this->getId(), $partnersArray)) {
					$currentKuser->setAllowedPartners($allowedPartners.','.$this->getId());
				}
			} else {
				$currentKuser->setAllowedPartners($this->getId());
			}
			
			$currentKuser->save();
		}
		
		
	}
	
	public function postUpdate(PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
			return parent::postUpdate($con);
		
		// update the owner kuser deatils if required
		$adminNameModified = $this->isColumnModified(PartnerPeer::ADMIN_NAME);
		$adminEmailModified = $this->isColumnModified(PartnerPeer::ADMIN_EMAIL);
		if ( $adminNameModified || $adminEmailModified )
		{
			$ownerKuserId = $this->getAccountOwnerKuserId();
			if ($ownerKuserId) {
				$ownerKuser = kuserPeer::retrieveByPK($ownerKuserId);
				if ($adminNameModified) {
					$ownerKuser->setFullName($this->getAdminName());
				}
				if ($adminEmailModified) {
					$ownerKuser->setEmail($this->getAdminEmail());
				}
				$ownerKuser->save();
			}	
		}
	
		$objectDeleted = false;
		if($this->isColumnModified(PartnerPeer::STATUS) && $this->getStatus() == Partner::PARTNER_STATUS_DELETED)
			$objectDeleted = true;
		
		$ret = parent::postUpdate($con);
	
		if ($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
		
		return $ret;
	}
	
	
	public function setRoleCacheDirtyAt($time)
	{
		$this->putInCustomData('role_cache_dirty_at', $time);
	}
	
	public function getRoleCacheDirtyAt()
	{
		return $this->getFromCustomData('role_cache_dirty_at');
	}
	
	public function setI18nTemplatePartnerId ($v)
	{
		$this->putInCustomData('i18n_template_partner_id', $v);
	}
	
	public function getI18nTemplatePartnerId ()
	{
		$this->getFromCustomData('i18n_template_partner_id');
	}
	
	// -------------------------------------------------
	// -- start of account owner kuser related functions
	// -------------------------------------------------
		
	
	/**
	 * @throws kUserException::USER_NOT_FOUND
	 * @throws kPermissionException::ACCOUNT_OWNER_NEEDS_PARTNER_ADMIN_ROLE
	 */
	public function setAccountOwnerKuserId($kuserId, $doChecks = true) //$doChecks needed to support user migration and can later be deleted
	{	
		$kuser = kuserPeer::retrieveByPK($kuserId);
		if ($doChecks)
		{
			if (!$kuser || $kuser->getPartnerId() != $this->getId()) {
				throw new kUserException('', kUserException::USER_NOT_FOUND);
			}
			$kuserRoles = explode(',', $kuser->getRoleIds());
			if (!in_array($this->getAdminSessionRoleId(), $kuserRoles)) {
				throw new kPermissionException('', kPermissionException::ACCOUNT_OWNER_NEEDS_PARTNER_ADMIN_ROLE);
			}
		}
		if ($kuser) {
			$this->setAdminName($kuser->getFullName());
			$this->setAdminEmail($kuser->getEmail());
		}
		$this->putInCustomData('account_owner_kuser_id', $kuserId);
	}
	
	public function getAccountOwnerKuserId()
	{
		return $this->getFromCustomData('account_owner_kuser_id');
	}
	
	/**
	 * @return puserId of the kuser currently set as the account owner
	 */
	public function getAdminUserId()
	{
		$ownerKuserId = $this->getAccountOwnerKuserId();
		if (!$ownerKuserId) {
			return null;
		}
		$ownerKuser = kuserPeer::retrieveByPK($ownerKuserId);
		if (!$ownerKuser) {
			KalturaLog::err('Cannot find kuser with id ['.$ownerKuserId.'] set as account of partner id ['.$this->getId().']');
			return null;
		}
		return $ownerKuser->getPuserId();
	}
	
	/**
	 * Change the kuser set as the account owner to the one with puserId = $adminUserId
	 * @param string $adminUserId puserId of the new kuser
	 */
	public function setAdminUserId($adminUserId)
	{
		$adminKuser = kuserPeer::getKuserByPartnerAndUid($this->getId(), $adminUserId);
		if (!$adminKuser) {
			throw new kCoreException("User Id [$adminUserId] not found", kCoreException::INVALID_USER_ID); // TODO - don't use API objects in core object
		}
		$this->setAccountOwnerKuserId($adminKuser->getId());
	}
		
	// -----------------------------------------------
	// -- end of account owner kuser related functions
	// -----------------------------------------------
	
	
	
	// ------------------------------------
	// -- start of enabled special features
	// ------------------------------------
		
	/**
	 * Temporary array to hold plugin permissions status until next object save..
	 * @var array
	 */
	private $setEnabledPlugins  = array();
	/**
	 * Temporary array to hold special service permissions status until next object save..
	 * @var array
	 */
	private $setEnabledServices = array();
	
	
	// plugins
	public function getPluginEnabled($pluginName) 
	{ 
		if (isset($this->setEnabledPlugins[$pluginName]))
		{
			return $this->setEnabledPlugins[$pluginName];
		}
		else
		{
			$permission =  PermissionPeer::isAllowedPlugin($pluginName, $this->getId());
			return $permission ? true : false;
		}
	}
	
	public function setPluginEnabled($pluginName, $enabled) 
	{ 
		$this->setEnabledPlugins[$pluginName] = $enabled;
	} 
	
	
	public function setEnabledService($enabled, $permissionName)
	{
		$this->setEnabledServices[$permissionName] = $enabled;
	}
	
	public function getEnabledService($permissionName)
	{
		if (isset($this->setEnabledServices[$permissionName]))
		{
			return $this->setEnabledServices[$permissionName];
		}
		else
		{		
			$permission = PermissionPeer::isValidForPartner($permissionName, $this->getId());
			return $permission ? true : false;
		}
	}
	
	
	// analytics tab
	public function getEnableAnalyticsTab() {
		return $this->getEnabledService(PermissionName::FEATURE_ANALYTICS_TAB);
	}
	
	public function setEnableAnalyticsTab( $v ) {
		$this->setEnabledService($v, PermissionName::FEATURE_ANALYTICS_TAB);
	}
		
	// silverlight
	public function getEnableSilverLight() {
		return $this->getEnabledService(PermissionName::FEATURE_SILVERLIGHT);
	}
	
	public function setEnableSilverLight( $v ) {
		$this->setEnabledService($v, PermissionName::FEATURE_SILVERLIGHT);
	}
	
	// vast
	public function getEnableVast() {
		return $this->getEnabledService(PermissionName::FEATURE_VAST);
	}
	
	public function setEnableVast( $v ) {
		$this->setEnabledService($v, PermissionName::FEATURE_VAST);
	}
	
	// 508 players
	public function getEnable508Players() {
		return $this->getEnabledService(PermissionName::FEATURE_508_PLAYERS);
	}
	
	public function setEnable508Players( $v ) {
		$this->setEnabledService($v, PermissionName::FEATURE_508_PLAYERS);
	}
	
	// live stream
	public function getLiveStreamEnabled() {
		return $this->getEnabledService(PermissionName::FEATURE_LIVE_STREAM);
	}
	
	public function setLiveStreamEnabled( $v ) {
		$this->setEnabledService($v, PermissionName::FEATURE_LIVE_STREAM);
	}
	
	
	// ----------------------------------
	// -- end of enabled special features
	// ----------------------------------
	
	public function getCacheInvalidationKeys()
	{
		return array("partner:id=".$this->getId());
	}	
	
	public function getWidgetSessionRoleId() {
		$id = $this->getFromCustomData ( 'widget_session_role_id' );
		if (! $id) {
			$id = UserRolePeer::getIdByStrId ( UserRoleId::WIDGET_SESSION_ROLE );
		}
		return $id;
	}
	
	/**
	 * @return AccessControl
	 */
	public function getApiAccessControl()
	{
		$id = $this->getApiAccessControlId();
		if ($id)
			return accessControlPeer::retrieveByPK($id);
		else
			return null;
	}	

	public function validateApiAccessControl()
	{
		if (kIpAddressUtils::isInternalIp())
			return true;

		if ($this->getEnforceHttpsApi() && infraRequestUtils::getProtocol() != infraRequestUtils::PROTOCOL_HTTPS)
		{
			KalturaLog::err('Action was accessed over HTTP while the partner is configured for HTTPS access only');
			return false;
		}

		$accessControl = $this->getApiAccessControl();
		if (is_null($accessControl))
			return true;

		$context = new kEntryContextDataResult();
		
		$scope = new accessControlScope();
		$scope->setKs(kCurrentContext::$ks);
		$scope->setContexts(array(ContextType::PLAY));
		
		$disableCache = $accessControl->applyContext($context, $scope);
		if ($disableCache)
			kApiCache::disableCache();

		if(count($context->getMessages()))
		{
			header("X-Kaltura-API-Access-Control: ".implode(', ', $context->getMessages()));
		}

		if(count($context->getActions()))
		{
			$actions = $context->getActions();
			foreach($actions as $action)
			{
				/* @var $action kAccessControlAction */
				if($action->getType() == RuleActionType::BLOCK)
				{
					KalturaLog::err('Action was blocked by API access control');
					return false;
				}
			}
		}
		return true;
	}
}
