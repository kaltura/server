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
	const HOSTED_PAGES_PARTNER_ID = -3;
	const MONITORING_PARTNER_ID = -4;
	const MEDIA_SERVER_PARTNER_ID = -5;
	const PLAY_SERVER_PARTNER_ID = -6;
	const EP_PARTNER_ID = -11;
	const SELF_SERVE_PARTNER_ID = -12;
	const KME_PARTNER_ID = -13;
	const BI_PARTNER_ID = -15;
	const AUTH_BROKER_PARTNER = -17;

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
	const PARTNER_STATUS_READ_ONLY = 4;
	
	const CONTENT_BLOCK_SERVICE_CONFIG_ID = 'services_limited_partner.ct';
	const FULL_BLOCK_SERVICE_CONFIG_ID = 'services_block.ct';
	
	const MAX_ACCESS_CONTROLS = 24;
	const GLOBAL_ACCESS_LIMITATIONS = 'global_access_limitations';
	
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
	
	const CUSTOM_DATA_USAGE_WARNINGS = 'usageWarnings';
	
	public static $s_content_root ;
	
	const CDN_HOST_WHITE_LIST = 'CDNHostWhiteList';

	const HTML_PURIFIER_BEHAVIOUR = "htmlPurifierBehaviour";

	const HTML_PURIFIER_BASE_LIST_USAGE = "htmlPurifierBaseListUsage";

	const PUBLISHER_ENVIRONMENT_TYPE = "publisherEnvironmentType";
	const OVP_ENVIRONMENT_URL = "ovpEnvironmentUrl";
	const OTT_ENVIRONMENT_URL = "ottEnvironmentUrl";

	private $partnerUsagePercent;

	const CUSTOM_DATA_LIVE_STREAM_INPUTS = 'live_stream_inputs';

	const CUSTOM_DATA_LIVE_STREAM_OUTPUTS = 'live_stream_outputs';

	const PARTNER_MAX_LIVE_STREAM_INPUTS_DEFAULT = 10;

	const PARTNER_MAX_LIVE_STREAM_OUTPUTS_DEFAULT = 10;

	const CUSTOMER_DATA_RTC_ENV = 'rtc_env_name';

	const RTC_SERVER_NODE_ENV = 'rtc_server_node_env';

	const ANALYTICS_HOST = "analytics_host";

	const CUSTOM_DATA_ALLOWED_FROM_EMAIL_WHITELIST = 'allowedFromEmailWhiteList';

	const CUSTOM_DATE_SHARED_STORAGE_STORAGE_PROFILE_ID = 'shared_storage_profile_id';

	const LIVE_CONCURRENT_BY_ADMIN_TAG = 'live_concurrent_by_admin_tag';

	const ALL_PARTNERS_WILD_CHAR = "*";
	
	const SECONDARY_SECRET_ROLE = 'secondary_secret_role';

	const ALLOWED_EMAIL_DOMAINS_FOR_ADMINS = 'allowed_email_domains_for_admins';
	
	const EXCLUDED_ADMIN_ROLE_NAME = 'excluded_admin_role_name';
	
	const ALLOWED_DOMAINS = 'allowed_domains';
	
	const TRIGRAM_PERCENTAGE = 'trigram_percentage';
	
	const MAX_WORDS_FOR_NGRAM = 'max_words_for_ngram';
	
	const TWO_FACTOR_AUTHENTICATION_MODE = 'two_factor_authentication_mode';
	
	const PURIFY_IMAGE_CONTENT = 'purify_image_content';
	
	const HIDE_SECRETS = 'hideSecrets';

	const IS_SELF_SERVE = 'isSelfServe';
	
	const EVENT_PLATFORM_ALLOWED_TEMPLATES = 'event_platform_allowed_templates';
	
	const RECYCLE_BIN_RETENTION_PERIOD = 'recycle_bin_retention_period';
	
	private $cdnWhiteListCache = array();

	const CUSTOM_DATE_MAX_METADATA_INDEX_LENGTH = 'max_metadata_index_length';
	
	const CUSTOM_ANALYTICS_DOMAIN = 'custom_analytics_domain';

	public function save(PropelPDO $con = null)
	{
		PartnerPeer::removePartnerFromCache( $this->getId() );
		
		return parent::save ( $con ) ;
	}
	
	public function validateSecret ( $partner_secret , $partner_key , &$ks_max_expiry_in_seconds , $admin = false )
	{
		$additionalSecrets = $this->getEnabledAdditionalAdminSecrets();
		if ($partner_secret === $this->getAdminSecret() ||
			in_array($partner_secret, $additionalSecrets, true) ||
			(!$admin && $partner_secret === $this->getSecret()))
		{
			$partnerKsMaxExpiryInSeconds = $this->getKsMaxExpiryInSeconds();
			if(!$partnerKsMaxExpiryInSeconds)
			{
				// This handles cases where the partner setting is the default mysql value which is null or for some reason set to 0 which is invalid
				$partnerKsMaxExpiryInSeconds = dateUtils::DAY;
			}

			if ($ks_max_expiry_in_seconds || $ks_max_expiry_in_seconds != '')
			{
				$ks_max_expiry_in_seconds = min($ks_max_expiry_in_seconds, $partnerKsMaxExpiryInSeconds);
			}
			else
			{
				$ks_max_expiry_in_seconds = $partnerKsMaxExpiryInSeconds;
			}

			return true;
		}
		else
		{
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

	public function getAllowedFromEmailWhiteList()
	{
		return $this->getFromCustomData( self::CUSTOM_DATA_ALLOWED_FROM_EMAIL_WHITELIST);
	}

	public function setAllowedFromEmailWhiteList( $emails )
	{
		$emails =  implode(',',array_map('trim',explode(',',$emails)));
		$this->putInCustomData( self::CUSTOM_DATA_ALLOWED_FROM_EMAIL_WHITELIST, $emails);
	}

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
		return (int)$this->getFromCustomData( "allowQuickEdit" , null , true );
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
	 * Get the default live conversion profile id for the partner
	 * 
	 * @return int 
	 */
	public function getDefaultLiveConversionProfileId()
	{
		return $this->getFromCustomData("defaultLiveConversionProfileId");
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
	
	/**
	 * Set the live default conversion profile id for the partner
	 *  
	 * @param int $v
	 * @return int
	 */
	public function setDefaultLiveConversionProfileId($v)
	{
		$this->putInCustomData("defaultLiveConversionProfileId", $v);
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
		return (int)$this->getFromCustomData( "allowMultiNotification" , null  );
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

	public function getDefThumbQuality()
	{
		return $this->getFromCustomData( "defThumbQuality" , null , 0);
	}

	public function setDefThumbQuality( $v )
	{
		return $this->putInCustomData( "defThumbQuality", $v );
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
		
	public function getPlayServerHost()	{		return $this->getFromCustomData( "playServerHost");	}
	public function setPlayServerHost( $v )	{		return $this->putInCustomData( "playServerHost", $v );	}

	public function getDefaultDeliveryCode()    {               return $this->getFromCustomData( "defaultDeliveryCode" , null, false  ); }
	public function setDefaultDeliveryCode( $v )        {               return $this->putInCustomData( "defaultDeliveryCode", $v ); }
	
	public function getThumbnailHost()	{		return $this->getFromCustomData( "thumbnailHost" , null, false  );	}
	public function setThumbnailHost( $v )	{		return $this->putInCustomData( "thumbnailHost", $v );	}	
	
	public function getThumbnailCacheAge()	{		return $this->getFromCustomData( "thumbnailCacheAge" , null, 0);	}
	public function setThumbnailCacheAge( $v )	{		return $this->putInCustomData( "thumbnailCacheAge", $v);	}
		
	public function getForceCdnHost()	{		return $this->getFromCustomData( "forceCdnHost" , null, false  );	}
	public function setForceCdnHost( $v )	{		return $this->putInCustomData( "forceCdnHost", $v );	}	

	public function getEnforceHttpsApi()	{		return $this->getFromCustomData( "enforceHttpsApi" , null, false  );	}
	public function setEnforceHttpsApi( $v )	{		return $this->putInCustomData( "enforceHttpsApi", $v );	}
	
	public function getEnforceDelivery()	{
		return $this->getFromCustomData( "enforceDelivery" , null, false  );
	}
	public function setEnforceDelivery( $v )	{
		return $this->putInCustomData( "enforceDelivery", $v );
	}
	
	public function getAssetsPerEntryLimitation()    		{	return $this->getFromCustomData( "assetsPerEntryAllowed" , null, false  ); 	}
	public function setAssetsPerEntryLimitation( $v )       {	return $this->putInCustomData( "assetsPerEntryAllowed", $v ); 				}
	
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
		$criteria = new Criteria();
		$criteria->add(BatchJobLockPeer::PARTNER_ID, $this->getId());
		$criteria->add(BatchJobLockPeer::JOB_TYPE, BatchJobType::INDEX);
		$criteria->add(BatchJobLockPeer::JOB_SUB_TYPE, $type);
		
		$batchJob = BatchJobLockPeer::doSelectOne($criteria);
		
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

	public function getStorageServePriority() { return $this->getFromCustomData("storageServePriority", null, StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY); }
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
	
	private function getDisabledDeliveryTypes() { return $this->getFromCustomData("disabledDeliveryTypes", array()); }
	private function setDisabledDeliveryTypes(array $v ) { $this->putInCustomData("disabledDeliveryTypes", $v); }

	public function getEnabledAdditionalAdminSecrets()
	{
		return $this->getFromCustomData("enabledAdditionalAdminSecrets", null, array());
	}

	/**
	 * @param array<string> $v
	 */
	public function setEnabledAdditionalAdminSecrets($v)
	{
		$enabled = $this->getEnabledAdditionalAdminSecrets();
		//in case additional was moved to primary, do not disable it
		$primaryAdminSecretArray = array($this->getAdminSecret());
		/** @noinspection PhpParamsInspection */
		$removedEnabledSecrets = array_diff($enabled, $v, $primaryAdminSecretArray);
		if ($removedEnabledSecrets)
		{
			/** @var array $oldDisabled */
			$oldDisabled = $this->getDisabledAdditionalAdminSecrets();
			//unique - in case of secret that was enabled disabled several times.
			$merged = array_merge($removedEnabledSecrets, $oldDisabled);
			$newDisabled = array_unique($merged);
			$this->setDisabledAdditionalAdminSecrets($newDisabled);
		}
		//In case secret Has been re-enabled Remove it from disabled
		/** @var array $disabled */
		$disabled = $this->getDisabledAdditionalAdminSecrets();
		$newDisabled = array_diff($disabled, $v, $primaryAdminSecretArray);
		$this->setDisabledAdditionalAdminSecrets($newDisabled);
		$this->putInCustomData( "enabledAdditionalAdminSecrets", $v);
	}

	/**
	 * disabled admin secret is only accessible from setEnabledAdditionalAdminSecrets
	 */
	private function getDisabledAdditionalAdminSecrets()
	{
		return $this->getFromCustomData("disabledAdditionalAdminSecrets", null, array());
	}


	/**
	 * disabled admin secret is only populate using the setEnabledAdditionalAdminSecrets function
	 * @param $v
	 */
	private function setDisabledAdditionalAdminSecrets($v)
	{
		$this->putInCustomData( "disabledAdditionalAdminSecrets", $v);
	}

	public function getCustomDeliveryTypes()
	{
		$customDeliveryTypes = array();

		// take the disabled types from the old field
		$disabledDeliveryTypes = $this->getDisabledDeliveryTypes();
		if (is_array($disabledDeliveryTypes))
		{
			foreach($disabledDeliveryTypes as $disabledDeliveryType)
			{
				$customDeliveryTypes[$disabledDeliveryType] = false;
			}
		}

		// override with the new field that supports enable/disable
		$customDeliveryTypesCustomData = $this->getFromCustomData("customDeliveryTypes", array());
		if (is_array($customDeliveryTypesCustomData))
		{
			foreach($customDeliveryTypesCustomData as $deliveryType => $enabled)
			{
				$customDeliveryTypes[$deliveryType] = $enabled;
			}
		}

		return $customDeliveryTypes;
	}

	public function setCustomDeliveryTypes(array $v)
	{
		$this->putInCustomData("customDeliveryTypes", $v);
		$this->setDisabledDeliveryTypes(array()); // erase the old custom data field
	}

	public function getDeliveryTypes()
	{
		$map = kConf::getMap('players');
		$availableDeliveryTypes = $map['delivery_types'];
		$customDeliveryTypes = $this->getCustomDeliveryTypes();
		$deliveryTypes = array();

		foreach($availableDeliveryTypes as $deliveryType => $deliveryInfo)
		{
			// if this delivery was custom configured, check if it should be used
			if (isset($customDeliveryTypes[$deliveryType]))
			{
				$customDeliveryTypeEnabled = $customDeliveryTypes[$deliveryType];
				if ($customDeliveryTypeEnabled)
					$deliveryTypes[$deliveryType] = $deliveryInfo;
			}
			// if delivery was not custom configured, check if it's enabled by default
			elseif(isset($deliveryInfo['enabledByDefault']) && $deliveryInfo['enabledByDefault'])
			{
				$deliveryTypes[$deliveryType] = $deliveryInfo;
			}
		}

		return $deliveryTypes;
	} 
	
	public function getMediaServersConfiguration ()
	{
		return $this->getFromCustomData('mediaServersConfiguration', null, null);
	}
	
	public function setMediaServersConfiguration ($v)
	{
		$this->putInCustomData('mediaServersConfiguration', $v);
	}
	
	public function setDeliveryProfileIds($params)
	{
		$this->putInCustomData('delivery_profile_ids', $params);
	}
	
	public function getDeliveryProfileIds()
	{
		return $this->getFromCustomData('delivery_profile_ids', null, array());
	}
	
	public function setLiveDeliveryProfileIds($params)
	{
		$this->putInCustomData('live_delivery_profile_ids', $params);
	}
	
	public function getLiveDeliveryProfileIds()
	{
		return $this->getFromCustomData('live_delivery_profile_ids', null, array());
	}

	public function getESearchLanguages()
	{
		return $this->getFromCustomData('e_search_languages', null,  array());
	}

	public function setESearchLanguages($params)
	{
		$this->putInCustomData('e_search_languages', $params);
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
	public function setExtendedFreeTrail( $v )
	{
		$this->putInCustomData("extendedFreeTrail", $v);
		if ( $v )
		{
			$this->setUsageLimitWarning(null);
		}
	}
	
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

	// Status change reason for audit logs
	public function getStatusChangeReason() { return $this->getFromCustomData('statusChangeReason'); }	
	public function setStatusChangeReason( $v ) { return $this->putInCustomData('statusChangeReason', $v); }
	
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
	
	//use time aligned renditions in playManifest for live entries
	public function setTimeAlignedRenditions($v) { $this->putInCustomData('timeAlignedRenditions', $v);}
	public function getTimeAlignedRenditions() { return $this->getFromCustomData('timeAlignedRenditions', null); }
	
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
	public function setMaxLiveStreamInputs($v)			{$this->putInCustomData(self::CUSTOM_DATA_LIVE_STREAM_INPUTS, $v);}
	public function setMaxLiveStreamOutputs($v)			{$this->putInCustomData(self::CUSTOM_DATA_LIVE_STREAM_OUTPUTS, $v);}
	public function setMaxLiveRtcStreamInputs($v)		{$this->putInCustomData('live_rtc_stream_inputs', $v);}
	
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
	public function setBandwidthOverageUnit($v)			{$this->putInCustomData('bandwidth_overage_unit', $v);}
	public function setStreamEntriesOverageUnit($v)		{$this->putInCustomData('stream_entries_overage_unit', $v);}
	public function setEntriesOverageUnit($v)			{$this->putInCustomData('entries_overage_unit', $v);}
	public function setMonthlyStorageOverageUnit($v)	{$this->putInCustomData('monthly_storage_overage_unit', $v);}
	public function setMonthlyStorageAndBandwidthOverageUnit($v)	{$this->putInCustomData('monthly_storage_and_bandwidth_overage_unit', $v);}
	public function setEndUsersOverageUnit($v)			{$this->putInCustomData('end_users_overage_unit', $v);}
	public function setLoginUsersOverageUnit($v)		{$this->putInCustomData('login_users_overage_unit', $v);}
    public function setMaxLoginAttemptsOverageUnit($v)	{$this->putInCustomData('login_attempts_overage_unit', $v);}
    public function setMaxBulkSizeOverageUnit($v)		{$this->putInCustomData('bulk_size_overage_unit', $v);}
    public function setAutoModerateEntryFilter($v)		{$this->putInCustomData('auto_moderate_entry_filter', $v);}
    public function setCacheFlavorVersion($v)			{$this->putInCustomData('cache_flavor_version', $v);}
    public function setCacheThumbnailVersion($v)		{$this->putInCustomData('cache_thumb_version', $v);}
    public function setBroadcastUrlManager($v)			{$this->putInCustomData('broadcast_url_manager', $v);}
    public function setPrimaryBroadcastUrl($v)			{$this->putInCustomData('primary_broadcast_url', $v);}
	public function setSecondaryBroadcastUrl($v)		{$this->putInCustomData('secondary_broadcast_url', $v);}
	public function setLiveStreamPlaybackUrlConfigurations($v)		{$this->putInCustomData('live_stream_playback_url_configurations', $v);}
	public function setLastFreeTrialNotificationDay($v)	{$this->putInCustomData('last_free_trial_notification_day', $v);}
	public function setTemplateEntriesNum($v)			{$this->putInCustomData('template_entries_num', $v);}
	public function setTemplateCategoriesNum($v)		{$this->putInCustomData('template_categories_num', $v);}
	public function setTemplateCustomMetadataNum($v)	{$this->putInCustomData('template_custom_metadata_num', $v);}
	public function setInitialPasswordSet($v)			{$this->putInCustomData('initial_password_set', $v);}
	public function setMarketoCampaignId($v)			{$this->putInCustomData('marketo_campaign_id', $v);}
	public function setExcludedAdminRoleName($v)			{$this->putInCustomData(self::EXCLUDED_ADMIN_ROLE_NAME, $v);}
	public function setAllowedDomains($v)		{$this->putInCustomData(self::ALLOWED_DOMAINS,$v);}

	public function getLoginUsersQuota()				{return $this->getFromCustomData('login_users_quota', null, 0);}
	public function getExcludedAdminRoleName()			{return $this->getFromCustomData(self::EXCLUDED_ADMIN_ROLE_NAME, null, '');}
	public function getAllowedDomains()  				{return $this->getFromCustomData(self::ALLOWED_DOMAINS,null,'');}
	public function getAdminLoginUsersQuota()			{return $this->getFromCustomData('admin_login_users_quota', null, 3);}
	public function getPublishersQuota()				{return $this->getFromCustomData('publishers_quota', null, 0);}
	public function getBandwidthQuota()					{return $this->getFromCustomData('bandwidth_quota', null, 0);}
	public function getStreamEntriesQuota()				{return $this->getFromCustomData('stream_entries_quota', null, 0);}
	public function getEntriesQuota()					{return $this->getFromCustomData('entries_quota', null, 0);}
	public function getMonthlyStorage()					{return $this->getFromCustomData('monthly_storage');}
	public function getMonthlyStorageAndBandwidth()		{return $this->getFromCustomData('monthly_storage_and_bandwidth');}
	public function getEndUsers()						{return $this->getFromCustomData('end_users');}
	public function getAccessControls()					{return $this->getFromCustomData('access_controls', null, self::MAX_ACCESS_CONTROLS);}
	public function getMaxLiveStreamInputs()
	{
		$live_stream_inputs = $this->getFromCustomData(self::CUSTOM_DATA_LIVE_STREAM_INPUTS);
		if (!$live_stream_inputs)
			$live_stream_inputs = self::PARTNER_MAX_LIVE_STREAM_INPUTS_DEFAULT;

		return $live_stream_inputs;
	}
	public function getMaxLiveStreamOutputs()
	{
		$live_stream_outputs = $this->getFromCustomData(self::CUSTOM_DATA_LIVE_STREAM_OUTPUTS);
		if (!$live_stream_outputs)
			$live_stream_outputs = self::PARTNER_MAX_LIVE_STREAM_OUTPUTS_DEFAULT;

		return $live_stream_outputs;
	}
	public function getMaxLiveRtcStreamInputs()			{
		$liveRtcStreamInputs = $this->getFromCustomData('live_rtc_stream_inputs');
		if ($liveRtcStreamInputs === null)
			$liveRtcStreamInputs = kConf::get('live_rtc_concurrent_streams', 'local', 2);
		return $liveRtcStreamInputs;
	}

	public function setMaxConcurrentLiveByAdminTag($limitsArray)			{
		$this->putInCustomData(self::LIVE_CONCURRENT_BY_ADMIN_TAG, $limitsArray);
	}
	public function setSecondarySecretRoleId($v)        {$this->putInCustomData(self::SECONDARY_SECRET_ROLE, $v);}
	
	public function getSecondarySecretRoleId()          {return $this->getFromCustomData(self::SECONDARY_SECRET_ROLE);}
	public function getMaxConcurrentLiveByAdminTag()			{
		$defaultValues = kConf::get('ConcurrentLiveLimitByAdminTag_DefaultValues', 'local', array());
		return array_replace($defaultValues, (array)$this->getFromCustomData(self::LIVE_CONCURRENT_BY_ADMIN_TAG));
	}

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
	public function getLoginUsersOverageUnit()			{return $this->getFromCustomData('login_users_overage_unit');}
    public function getMaxLoginAttemptsOverageUnit()	{return $this->getFromCustomData('login_attempts_overage_unit');}
    public function getMaxBulkSizeOverageUnit()			{return $this->getFromCustomData('bulk_size_overage_unit');}
	public function getAutoModerateEntryFilter()		{return $this->getFromCustomData('auto_moderate_entry_filter');}
    public function getCacheFlavorVersion()				{return $this->getFromCustomData('cache_flavor_version');}
    public function getCacheThumbnailVersion()			{return $this->getFromCustomData('cache_thumb_version');}
    public function getBroadcastUrlManager()			{return $this->getFromCustomData('broadcast_url_manager');}
	public function getPrimaryBroadcastUrl()			{return $this->getFromCustomData('primary_broadcast_url');}
	public function getSecondaryBroadcastUrl()			{return $this->getFromCustomData('secondary_broadcast_url');}
	public function getLiveStreamPlaybackUrlConfigurations()		 	{return $this->getFromCustomData('live_stream_playback_url_configurations', null, array());}
	public function getLastFreeTrialNotificationDay()	{return $this->getFromCustomData('last_free_trial_notification_day');}
	public function getTemplateEntriesNum()				{return $this->getFromCustomData('template_entries_num', null, 0);}
	public function getTemplateCategoriesNum()			{return $this->getFromCustomData('template_categories_num', null, 0);}
	public function getTemplateCustomMetadataNum()		{return $this->getFromCustomData('template_custom_metadata_num', null, 0);}
	public function getInitialPasswordSet()				{return $this->getFromCustomData('initial_password_set', null, 0);}
	public function getMarketoCampaignId()				{return $this->getFromCustomData('marketo_campaign_id', null, 0);}
	
	
	public function getStatus()
	{
		$status = $this->status;
		if ($this->status === Partner::PARTNER_STATUS_ACTIVE && $this->partner_parent_id !== null && $this->partner_parent_id !== $this->id)
		{
			$partnerParentId = PartnerPeer::retrieveByPK($this->partner_parent_id);
			if ($partnerParentId && $partnerParentId->getStatus() === Partner::PARTNER_STATUS_READ_ONLY)
			{
				$status = $partnerParentId->getStatus();
			}
		}
		return $status;
	}

	public function setLiveStreamBroadcastUrlConfigurations($key, $value)
    {
    	$this->putInCustomData($key, $value, 'live_stream_broadcast_url_configurations');
    }
    
	public function getLiveStreamBroadcastUrlConfigurations($dc = null)
	{
		$config = (!is_null($dc) ? kConf::get($dc, kConfMapNames::BROADCAST) : kConf::getMap(kConfMapNames::BROADCAST));
		
		$partnerConfig = $this->getFromCustomData($dc, 'live_stream_broadcast_url_configurations');
		if($partnerConfig)
		{
			$config = kConf::mergeConfigItem($config, $partnerConfig, true);
		}
		
		return $config;
	}
	
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

	public static function getAdminUserCriteria($partnerId)
	{
		$c = KalturaCriteria::create(kuserPeer::OM_CLASS);
		$c->addAnd(kuserPeer::PARTNER_ID, $partnerId);
		$c->addAnd(kuserPeer::LOGIN_DATA_ID, NULL, Criteria::ISNOTNULL);
		$c->addAnd(kuserPeer::IS_ADMIN , true);
		$c->addAnd(kuserPeer::STATUS, KuserStatus::ACTIVE, Criteria::EQUAL);
		$c->applyFilters();
		return $c;
	}

	public static function getAdminLoginUsersList($partnerId)
	{
		$c = self::getAdminUserCriteria($partnerId);
		return kuserPeer::doSelect($c);
	}
    
	public function getAdminLoginUsersNumber()
	{
		$c = self::getAdminUserCriteria($this->getId());
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
		// add ALWAYS_ALLOWED_ACTIONS only when always_allowed_permission_names was not specified explicitly
		// it's required to support the scenario where ALWAYS_ALLOWED_ACTIONS should be disabled (by specifying a "dummy" permission in always_allowed_permission_names)
		if (is_null($names) || !trim($names)) {
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
		{
			$objectDeleted = true;
		}
		
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
		return $this->getFromCustomData('i18n_template_partner_id');
	}
	
	
	public function setAudioThumbEntryId($v)
	{
		if ($v)
		{
			$this->putInCustomData('audioThumbEntryId', $v);
			$entry = entryPeer::retrieveByPK($v);
			$dataArr = explode('.',$entry->getData());
			$this->setAudioThumbEntryVersion($dataArr[0]);
		}
		else
		{
			$this->removeFromCustomData('audioThumbEntryId');
			$this->removeFromCustomData('audioThumbEntryVersion');
		}
	}
	
	
	public function getAudioThumbEntryId()
	{
		return $this->getFromCustomData('audioThumbEntryId');
	}
	
	public function setAudioThumbEntryVersion($v)
	{
		$this->putInCustomData('audioThumbEntryVersion', $v);
	}
	
	public function getAudioThumbEntryVersion()
	{
		return $this->getFromCustomData('audioThumbEntryVersion');
	}
	
	public function setLiveThumbEntryId($v)
	{
		if ($v)
		{
			$this->putInCustomData('liveThumbEntryId', $v);
			$entry = entryPeer::retrieveByPK($v);
			$dataArr = explode('.',$entry->getData());
			$this->setLiveThumbEntryVersion($dataArr[0]);
		}
		else
		{
			$this->removeFromCustomData('liveThumbEntryId');
			$this->removeFromCustomData('liveThumbEntryVersion');
		}
	}
	
	
	public function getLiveThumbEntryId()
	{
		return $this->getFromCustomData('liveThumbEntryId');
	}
	
	public function setLiveThumbEntryVersion($v)
	{
		$this->putInCustomData('liveThumbEntryVersion', $v);
	}
	
	public function getLiveThumbEntryVersion()
	{
		return $this->getFromCustomData('liveThumbEntryVersion');
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

	public function getAdminUser()
	{
		$ownerKuserId = $this->getAccountOwnerKuserId();
		if (!$ownerKuserId)
		{
			return null;
		}

		$ownerKuser = kuserPeer::retrieveByPK($ownerKuserId);
		if (!$ownerKuser)
		{
			KalturaLog::err('Cannot find kuser with id ['.$ownerKuserId.'] set as account of partner id ['.$this->getId().']');
			return null;
		}

		return $ownerKuser;
	}

	/**
	 * @return puserId of the kuser currently set as the account owner
	 */
	public function getAdminUserId()
	{
		$ownerKuser = $this->getAdminUser();
		if (!$ownerKuser)
		{
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
		return array("partner:id=".strtolower($this->getId()));
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
		
		if (!$this->validateGlobalApiAccessLimitations())
		{
			return false;
		}
		
		$accessControl = $this->getApiAccessControl();
		if (is_null($accessControl))
		{
			return true;
		}

		return $this->applyAccessControlContext($accessControl);
	}
	
	protected function validateGlobalApiAccessLimitations()
	{
		$globalAccessLimitationsConfiguration = kConf::get(self::GLOBAL_ACCESS_LIMITATIONS, kConfMapNames::RUNTIME_CONFIG, null);
		if ($globalAccessLimitationsConfiguration)
		{
			$allowedPartnersInBlockedCountries = $globalAccessLimitationsConfiguration['allowedPartnersInBlockedCountries'];
			if ($allowedPartnersInBlockedCountries && in_array($this->id, explode(",", $allowedPartnersInBlockedCountries)))
			{
			    return true;
			}
		
			$blockedCountriesList = $globalAccessLimitationsConfiguration['blockedCountries'];
			if ($blockedCountriesList)
			{
				if(myPartnerUtils::isRequestFromAllowedCountry($blockedCountriesList, $this->id) === false)
				{
					KExternalErrors::dieError(KExternalErrors::IP_COUNTRY_BLOCKED);
				}
			}
		}
		
		return true;
	}
	
	protected function applyAccessControlContext($accessControl)
	{
		$context = new kEntryContextDataResult();
		
		$scope = new accessControlScope();
		$scope->setKs(kCurrentContext::$ks);
		$scope->setContexts(array(ContextType::PLAY));
		
		$disableCache = $accessControl->applyContext($context, $scope, false);
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
	
	public function getNotificationUrl()
	{
		return $this->getUrl2();
	}

	public function getReferenceId() { return $this->getFromCustomData("referenceId", null); }
	public function setReferenceId( $v ) { $this->putInCustomData("referenceId", $v); }

	public function getGoogleOAuth2($appId, $objectIdentifier = null)
	{
		$customDataKey = $appId;
		if ($objectIdentifier)
		{
			$customDataKey .= '_' . $objectIdentifier;
		}
			
		$tokenData = $this->getFromCustomData($customDataKey, 'googleAuth');
		if(is_null($tokenData))
		{
			$appConfig = kConf::get($appId, 'google_auth', null);
			if($appConfig && isset($appConfig[$objectIdentifier]))
			{
				$tokenJsonStr = $appConfig[$objectIdentifier];
				$tokenData = json_decode($tokenJsonStr, true);
			}
		}
		
		return $tokenData;
	}
	
	public function setGoogleOAuth2($appId, $tokenJsonStr, $objectIdentifier = null)
	{
		$tokenData = json_decode($tokenJsonStr, true);
		
		$customDataKey = $appId;
		if ($objectIdentifier)
		{
			$customDataKey .= '_' . $objectIdentifier;
		}
			
		$this->putInCustomData($customDataKey, $tokenData, 'googleAuth');
	}

	public function isInCDNWhiteList($host)
	{
		if (isset($this->cdnWhiteListCache[$host]))
		{
			return $this->cdnWhiteListCache[$host];
		}

		KalturaLog::debug("Checking host [$host] is in partner CDN white list");
		$whiteList = $this->getCdnHostWhiteListArray();
		foreach ($whiteList as $regEx)
		{
			//Avoid passing "/" as pattern as it triggers preg_match(): Unknown modifier '/'
			if(!trim($regEx, "/"))
			{
				continue;
			}
			
			if (preg_match("/".$regEx."/", $host)===1)//Should $regEx be escaped?
			{
				$this->cdnWhiteListCache[$host] = true;
				return true;
			}
		}
		$this->cdnWhiteListCache[$host] = false;
		return false;
	}

	public function getCdnHostWhiteListArray()
	{
		$whiteListStr = $this->getFromCustomData(self::CDN_HOST_WHITE_LIST);
		$whiteListArr = array();
		if (!is_null($whiteListStr))
		{
			$whiteListArr = unserialize($whiteListStr);
		}
		return $whiteListArr;
	}

	public function getCdnHostWhiteList()
	{
		$whiteLiestArr = $this->getCdnHostWhiteListArray();
		$whiteLiestStr = implode(",",$whiteLiestArr);
		return $whiteLiestStr;
	}

	public function setCdnHostWhiteList($whiteListRegEx)
	{
		$whiteListArr = explode(',', rtrim($whiteListRegEx, ','));
		$this->putInCustomData(self::CDN_HOST_WHITE_LIST, serialize($whiteListArr));
	}

	public function getUsageWarnings() { return $this->getFromCustomData(self::CUSTOM_DATA_USAGE_WARNINGS, null, array()); }
	public function setUsageWarnings( $v ) { $this->putInCustomData(self::CUSTOM_DATA_USAGE_WARNINGS, $v); }

	public function getUsageWarning($type, $percent){
		$usageWarnings = $this->getUsageWarnings();
		$key = $type.'_'.$percent;
		if(array_key_exists($key, $usageWarnings)){
			return $usageWarnings[$key];
		}
		return null;
	}
	
	public function resetUsageWarning($type, $percent){
		$usageWarnings = $this->getUsageWarnings();
		unset($usageWarnings[$type.'_'.$percent]);
		$this->setUsageWarnings($usageWarnings);
	}
	
	public function setUsageWarning($type, $percent, $value){
		$usageWarnings = $this->getUsageWarnings();
		$usageWarnings[$type.'_'.$percent] = $value;
		$this->setUsageWarnings($usageWarnings);		
	}
	
	public function getHtmlPurifierBehaviour()
	{
		return $this->getFromCustomData( self::HTML_PURIFIER_BEHAVIOUR, null , HTMLPurifierBehaviourType::IGNORE );	
	}

	public function setHtmlPurifierBehaviour($v)
	{
		return $this->putInCustomData( self::HTML_PURIFIER_BEHAVIOUR, $v );
	}

	public function getHtmlPurifierBaseListUsage()
	{
		return $this->getFromCustomData( self::HTML_PURIFIER_BASE_LIST_USAGE, null , false );
	}

	public function setHtmlPurifierBaseListUsage($v)
	{
		return $this->putInCustomData( self::HTML_PURIFIER_BASE_LIST_USAGE, $v );
	}
	
	public function getDefaultLiveStreamSegmentDuration()
	{
		if (PermissionPeer::isValidForPartner(PermissionName::FEATURE_DYNAMIC_SEGMENT_DURATION, $this->getId()))
		{
			return $this->getFromCustomData("default_live_stream_segment_duration", null, LiveEntry::DEFAULT_SEGMENT_DURATION_MILLISECONDS);
		}
		return null;
	}
	
	public function setDefaultLiveStreamSegmentDuration($v)
	{
		$this->putInCustomData( "default_live_stream_segment_duration", $v );
	}

    public function getDefaultRecordingConversionProfile()
    {
        return $this->getFromCustomData("default_recording_conversion_profile" );
    }

    public function setDefaultRecordingConversionProfile($v)
    {
        $this->putInCustomData( "default_recording_conversion_profile", $v );
    }

	/**
	 * @param      string $name
	 * @param      string $namespace
	 * @return     boolean True if $name has been modified.
	 */
	public function isCustomDataModified($name = null, $namespace = '')
	{
		if(isset($this->oldCustomDataValues[$namespace]) && (is_null($name) || array_key_exists($name, $this->oldCustomDataValues[$namespace])))
		{
			return true;
		}

		return false;
	}


	public function getPartnerUsagePercent()
	{
		if (!$this->partnerUsagePercent)
			return 0;
		return $this->partnerUsagePercent ;
	}

	public function setPartnerUsagePercent($v)
	{
		$this->partnerUsagePercent = $v;
	}

	public function getPublisherEnvironmentType()
	{
		return $this->getFromCustomData( self::PUBLISHER_ENVIRONMENT_TYPE, null , PublisherEnvironmentType::OVP );
	}

	public function setPublisherEnvironmentType($v)
	{
		return $this->putInCustomData( self::PUBLISHER_ENVIRONMENT_TYPE, $v );
	}

	public function getOvpEnvironmentUrl()
	{
		return $this->getFromCustomData( self::OVP_ENVIRONMENT_URL);
	}

	public function setOvpEnvironmentUrl($v)
	{
		return $this->putInCustomData( self::OVP_ENVIRONMENT_URL, $v );
	}

	public function getOttEnvironmentUrl()
	{
		return $this->getFromCustomData( self::OTT_ENVIRONMENT_URL);
	}

	public function setOttEnvironmentUrl($v)
	{
		return $this->putInCustomData( self::OTT_ENVIRONMENT_URL, $v );
	}

	public function getPartnerId()
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function getFreeTrialAccountType()
	{
		$additionalParams = $this->getAdditionalParams();
		if (isset($additionalParams['freeTrialAccountType']))
			return $additionalParams['freeTrialAccountType'];
		return null;
	}

	/**
	 * return all enabled admin secret separated by ','
	 * @return null|string
	 */
	public function getAllAdminSecretsAsString()
	{
		$additionalActiveSecrets = $this->getEnabledAdditionalAdminSecrets();
		if($additionalActiveSecrets)
		{
			$adminSecrets = implode(',', $additionalActiveSecrets);
			return $this->getAdminSecret() . ',' . $adminSecrets;
		}
		return $this->getAdminSecret();
	}

	public function getRTCEnv()
	{
		return $this->getFromCustomData( self::CUSTOMER_DATA_RTC_ENV, null , kConf::get(self::RTC_SERVER_NODE_ENV) );
	}

	public function setRTCEnv($v)
	{
		$this->putInCustomData(self::CUSTOMER_DATA_RTC_ENV, $v);
	}

	public function getAnalyticsUrl()
	{
		$host = $this->getAnalyticsHost();
		$fullUrl = null;
		if($host)
		{
			$fullUrl = infraRequestUtils::getProtocol() . '://' . $host;
		}
		return $fullUrl;
	}

	public function getAnalyticsHost()
	{
		return $this->getFromCustomData(self::ANALYTICS_HOST, null , kConf::get(self::ANALYTICS_HOST, 'local',  null));
	}

	public function setAnalyticsHost($v)
	{
		$this->putInCustomData(self::ANALYTICS_HOST, $v);
	}

	public function getUseTwoFactorAuthentication()
	{
		return $this->getFromCustomData("useTwoFactorAuthentication", null, false);
	}

	public function setUseTwoFactorAuthentication($v)
	{
		$this->putInCustomData("useTwoFactorAuthentication", $v);
	}

	public function getUseSso()
	{
		return $this->getFromCustomData("useSso", null, false);
	}

	public function setUseSso($v)
	{
		$this->putInCustomData("useSso", $v);
	}

	public function getBlockDirectLogin()
	{
		return $this->getFromCustomData("blockDirectLogin", null, false);
	}

	public function setBlockDirectLogin($v)
	{
		$this->putInCustomData("blockDirectLogin", $v);
	}

	public function getAuthenticationType()
	{
		if($this->getUseSso())
		{
			return PartnerAuthenticationType::SSO;
		}
		else if($this->getUseTwoFactorAuthentication())
		{
			return PartnerAuthenticationType::TWO_FACTOR_AUTH;
		}
		return PartnerAuthenticationType::PASSWORD_ONLY;
	}

	public function getAnalyticsPersistentSessionId()
	{
		return (PermissionPeer::isValidForPartner(PermissionName::FEATURE_ANALYTICS_PERSISTENT_SESSION_ID, $this->getId())) ? true : false;
	}

	public function getIgnoreSynonymEsearch()
	{
		return $this->getFromCustomData('ignoreSynonymEsearch', null, false);
	}

	public function setIgnoreSynonymEsearch($v)
	{
		$this->putInCustomData('ignoreSynonymEsearch', $v);
	}

	public function getAvoidIndexingSearchHistory()
	{
		return $this->getFromCustomData('avoidIndexingSearchHistory', null, false);
	}

	public function setAvoidIndexingSearchHistory($v)
	{
		$this->putInCustomData('avoidIndexingSearchHistory', $v);
	}
	
	public function getTwoFactorAuthenticationMode()
	{
		return $this->getFromCustomData(self::TWO_FACTOR_AUTHENTICATION_MODE);
	}
	
	public function setTwoFactorAuthenticationMode($v)
	{
		$this->putInCustomData(self::TWO_FACTOR_AUTHENTICATION_MODE, $v);
	}

	public function getSharedStorageProfileId()
	{
		$partnerDedicatedStorage = StorageProfilePeer::retrieveByPartnerIdAndProtocol($this->getPartnerId(), StorageProfileProtocol::KALTURA_DC);

		$sharedStorageId = $partnerDedicatedStorage ? $partnerDedicatedStorage->getId() : null;
		if($sharedStorageId)
		{
			KalturaLog::debug("Shared storage Id found for partner [{$this->getId()}] is [$sharedStorageId]");
		}

		return $partnerDedicatedStorage ? $partnerDedicatedStorage->getId() : null;
	}

//	public function getSharedStorageProfileId()
//	{
//		$sharedStorageId = null;
//		$allSharedStorageIds = kDataCenterMgr::getSharedStorageProfileIds();
//
//		$sharedIncludePartnerIds = kConf::get('shared_include_partner_ids', 'cloud_storage', array());
//		if (in_array($this->getId(), $sharedIncludePartnerIds) || in_array(self::ALL_PARTNERS_WILD_CHAR, $sharedIncludePartnerIds))
//		{
//			$sharedStorageId = reset($allSharedStorageIds);
//		}
//
//		$sharedPartnerPackages = kConf::get('shared_partner_package_types', 'cloud_storage', array());
//		if (in_array($this->getPartnerPackage(), $sharedPartnerPackages) || in_array(self::ALL_PARTNERS_WILD_CHAR, $sharedPartnerPackages))
//		{
//			$sharedStorageId = reset($allSharedStorageIds);
//		}
//
//		$sharedExcludePartnerIds = kConf::get('shared_exclude_partner_ids', 'cloud_storage', array());
//		if (in_array($this->getId(), $sharedExcludePartnerIds) || in_array(self::ALL_PARTNERS_WILD_CHAR, $sharedExcludePartnerIds))
//		{
//			$sharedStorageId = null;
//		}
//
//		return $sharedStorageId;
//	}
	
	public function setSharedStorageProfileId($v)
	{
		$this->putInCustomData(self::CUSTOM_DATE_SHARED_STORAGE_STORAGE_PROFILE_ID, $v);
	}
	
	public function getTrigramPercentage()
	{
		return $this->getFromCustomData(self::TRIGRAM_PERCENTAGE);
	}
	
	public function setTrigramPercentage($v)
	{
		return $this->putInCustomData(self::TRIGRAM_PERCENTAGE, $v);
	}
	
	public function getMaxWordForNgram()
	{
		return $this->getFromCustomData(self::MAX_WORDS_FOR_NGRAM);
	}
	
	public function setMaxWordForNgram($v)
	{
		return $this->putInCustomData(self::MAX_WORDS_FOR_NGRAM, $v);
	}
	
	public function getPurifyImageContent()
	{
		return $this->getFromCustomData(self::PURIFY_IMAGE_CONTENT, null, true);
	}
	
	public function setPurifyImageContent($v)
	{
		return $this->putInCustomData(self::PURIFY_IMAGE_CONTENT, $v);
	}
	
	public function getHideSecrets()
	{
		return $this->getFromCustomData(self::HIDE_SECRETS, null, false);
	}
	
	public function setHideSecrets($v)
	{
		return $this->putInCustomData(self::HIDE_SECRETS, $v);
	}

	public function getIsSelfServe() { return $this->getFromCustomData(self::IS_SELF_SERVE, null, false); }
	public function setIsSelfServe( $v ) { $this->putInCustomData(self::IS_SELF_SERVE, $v); }

	public function isAllowedLogin()
	{
		return in_array($this->status, array(Partner::PARTNER_STATUS_ACTIVE, Partner::PARTNER_STATUS_READ_ONLY));
	}
	
	public function getEventPlatformAllowedTemplates()
	{
		return $this->getFromCustomData(self::EVENT_PLATFORM_ALLOWED_TEMPLATES, null, '');
	}
	
	public function setEventPlatformAllowedTemplates($v)
	{
		return $this->putInCustomData(self::EVENT_PLATFORM_ALLOWED_TEMPLATES, $v);
	}
	
	public function getRecycleBinRetentionPeriod()
	{
		return $this->getFromCustomData(self::RECYCLE_BIN_RETENTION_PERIOD, null, RecycleBinRetentionPeriod::DAYS_30);
	}
	
	public function setRecycleBinRetentionPeriod($v)
	{
		return $this->putInCustomData(self::RECYCLE_BIN_RETENTION_PERIOD, $v);
	}

	public function getSearchMaxMetadataIndexLength()
	{
		return $this->getFromCustomData(self::CUSTOM_DATE_MAX_METADATA_INDEX_LENGTH);
	}

	public function setSearchMaxMetadataIndexLength($v)
	{
		return $this->putInCustomData(self::CUSTOM_DATE_MAX_METADATA_INDEX_LENGTH, $v);
	}

	public function getEnableGameServicesAnalytics()
	{
		return $this->getFromCustomData("enableGameServicesAnalytics", null, false);
	}

	public function setEnableGameServicesAnalytics($v)
	{
		return $this->putInCustomData("enableGameServicesAnalytics", $v);
	}
	
	public function getCustomAnalyticsDomain()
	{
		return $this->getFromCustomData(self::CUSTOM_ANALYTICS_DOMAIN);
	}
	
	public function setCustomAnalyticsDomain($v)
	{
		return $this->putInCustomData(self::CUSTOM_ANALYTICS_DOMAIN, $v);
	}

	public function getAllowedEmailDomainsForAdmins()
	{
		return $this->getFromCustomData(self::ALLOWED_EMAIL_DOMAINS_FOR_ADMINS);
	}

	public function setAllowedEmailDomainsForAdmins($v)
	{
		return $this->putInCustomData(self::ALLOWED_EMAIL_DOMAINS_FOR_ADMINS, $v);
	}
}
