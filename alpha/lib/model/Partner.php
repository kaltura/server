<?php

/**
 * Subclass for representing a row from the 'partner' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Partner extends BasePartner
{
	const PARTNER_GROUP_TYPE_PUBLISHER = 1;
	const PARTNER_GROUP_TYPE_VAR = 2;
	const PARTNER_GROUP_TYPE_GROUP = 3;
	
	const BATCH_PARTNER_ID = -1;
	const ADMIN_CONSOLE_PARTNER_ID = -2;
	
	const PARTNER_THAT_DOWS_NOT_EXIST = -1000;
	
	const VALIDATE_WRONG_LOGIN = -1;
	const VALIDATE_WRONG_PASSWORD = -2;
	const VALIDATE_TOO_MANY_INVALID_LOGINS = -3;
	const VALIDATE_PARTNER_BLOCKED = -4;
	const VALIDATE_LKS_DISABLED = -10;
	
	const PARTNER_STATUS_ACTIVE = 1;
	const PARTNER_STATUS_CONTENT_BLOCK = 2;
	const PARTNER_STATUS_FULL_BLOCK = 3;
	
	const CONTENT_BLOCK_SERVICE_CONFIG_ID = 'services_limited_partner.ct';
	const FULL_BLOCK_SERVICE_CONFIG_ID = 'services_block.ct';
	
	const MAX_ALLOWD_INVALID_LOGIN_COUNT = 10;
	
	const MAX_ACCESS_CONTROLS = 24;
	
	const MAX_NUMBER_OF_CATEGORIES = 200;
	
	const CATEGORIES_LOCK_TIMEOUT = 300; // in seconds
	
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
		if ( empty ( $lang ) ) $lang = "en";
		
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

	public function getDefThumbOffset()	{		return $this->getFromCustomData( "defThumbOffset" , false  );	}
	public function setDefThumbOffset( $v )	{		return $this->putInCustomData( "defThumbOffset", $v );	}
	
	public function getHost()	{		return $this->getFromCustomData( "host" , null, false  );	}
	public function setHost( $v )	{		return $this->putInCustomData( "host", $v );	}
		
	public function getCdnHost()	{		return $this->getFromCustomData( "cdnHost" , null, false  );	}
	public function setCdnHost( $v )	{		return $this->putInCustomData( "cdnHost", $v );	}	
		
	public function getForceCdnHost()	{		return $this->getFromCustomData( "forceCdnHost" , null, false  );	}
	public function setForceCdnHost( $v )	{		return $this->putInCustomData( "forceCdnHost", $v );	}	
		
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
	
	public function getLicensedJWPlayer() { return $this->getFromCustomData("licensedJWPlayer", null, 0); }
	public function setLicensedJWPlayer( $v ) { $this->putInCustomData("licensedJWPlayer", (int)$v); } 

	public function getAddEntryMaxFiles() { return $this->getFromCustomData("addEntryMaxFiles", null, myFileUploadService::MAX_FILES); }
	public function setAddEntryMaxFiles( $v ) { $this->putInCustomData("addEntryMaxFiles", (int)$v); }

	private function getCategoriesLockTime() { return $this->getFromCustomData("categoriesLockTime", null, 0); }
	private function setCategoriesLockTime( $v ) { $this->putInCustomData("categoriesLockTime", (int)$v); }

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

	/** added DelivryBlockCountries param for having per-partner ability to block serving of files to specific country **/
	public function getDelivryBlockCountries() { return $this->getFromCustomData("delivryBlockCountries", null); }
	public function setDelivryBlockCountries( $v ) { $this->putInCustomData("delivryBlockCountries", $v); }
	
	/** added deliveryRestrictions param for having per-partner ability to block serving of files to specific cdns and protocols **/
	public function getDeliveryRestrictions() { return $this->getFromCustomData("deliveryRestrictions", null); }
	public function setDeliveryRestrictions( $v ) { $this->putInCustomData("deliveryRestrictions", $v); }
			
	public function lockCategories()
	{
		$this->setCategoriesLockTime(time());
		$this->save();
	}
	
	public function unlockCategories()
	{
		$this->setCategoriesLockTime(0);
		$this->save();
	}
	
	public function isCategoriesLocked()
	{
		if ($this->getCategoriesLockTime() + self::CATEGORIES_LOCK_TIMEOUT > time())
		{
			return true;
		}
		else
		{
			$this->unlockCategories();
			return false;
		}
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
	
	public function getPriority($isBulk)
	{
		$priorityGroup = PriorityGroupPeer::retrieveByPK($this->getPriorityGroupId());
		
		if(!$priorityGroup)
		{
			if($isBulk)
				return PriorityGroup::DEFAULT_BULK_PRIORITY;
				
			return PriorityGroup::DEFAULT_PRIORITY;
		}
		
		if($isBulk)
			return $priorityGroup->getBulkPriority();
			
		return $priorityGroup->getPriority();
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
	
	public function setAdminLoginUsersQuota($quota)
	{
		$this->putInCustomData('admin_login_users_quota', $quota);
	}
	
	public function getAdminLoginUsersQuota()
	{
		return $this->getFromCustomData('admin_login_users_quota');
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
			$role = UserRolePeer::getByStrId(UserRoleId::PARTNER_ADMIN_ROLE);
			$id = $role->getId();
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
			$role = UserRolePeer::getByStrId(UserRoleId::BASE_USER_SESSION_ROLE);
			$id = $role->getId();
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
		
		// update the owner kuser deatils if required
		if ($this->tempAdminEmail || $this->tempAdminName)
		{
			$ownerKuserId = $this->getAccountOwnerKuserId();
			if ($ownerKuserId) {
				$ownerKuser = kuserPeer::retrieveByPK($ownerKuserId);
				if ($this->tempAdminName) {
					$ownerKuser->setFullName($this->tempAdminName);
				}
				if ($this->tempAdminEmail) {
					$ownerKuser->setEmail($this->tempAdminEmail);
				}
				$ownerKuser->save();
			}	
		}
				
		$this->setEnabledPlugins = array();
		$this->setEnabledServices = array();
		
		parent::postSave($con);
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
		if ($doChecks)
		{
			$kuser = kuserPeer::retrieveByPK($kuserId);
			if (!$kuser || $kuser->getPartnerId() != $this->getId()) {
				throw new kUserException('', kUserException::USER_NOT_FOUND);
			}
			$kuserRoles = explode(',', $kuser->getRoleIds());
			if (!in_array($this->getAdminSessionRoleId(), $kuserRoles)) {
				throw new kPermissionException('', kPermissionException::ACCOUNT_OWNER_NEEDS_PARTNER_ADMIN_ROLE);
			}
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
		return $ownerKuser->getPuserId();
	}
	
	/**
	 * Change the kuser set as the account owner to the one with puserId = $adminUserId
	 * @param string $adminUserId puserId of the new kuser
	 * @throws KalturaErrors::USER_NOT_FOUND
	 */
	public function setAdminUserId($adminUserId)
	{
		$adminKuser = kuserPeer::getKuserByPartnerAndUid($this->getId(), $adminUserId);
		if (!$adminKuser) {
			throw new KalturaAPIException(KalturaErrors::USER_NOT_FOUND);
		}
		$this->setAccountOwnerKuserId($adminKuser->getId());
	}

	/**
	 * Temporary admin name - saves new admin name until partner is saved
	 * @var string
	 */
	private $tempAdminName  = null;
	
	/**
	 * Temporary admin email - saves new admin email until partner is saved
	 * @var string
	 */
	private $tempAdminEmail = null;
	
	
	public function setAdminName($name)
	{
		$this->tempAdminName = $name;
	}
	
	public function setAdminEmail($email)
	{
		$this->tempAdminEmail = $email;	
	}
	
	
	public function getAdminName()
	{
		if ($this->tempAdminName) {
			return $this->tempAdminName;
		}
		
		$ownerKuserId = $this->getAccountOwnerKuserId();
		if (!$ownerKuserId) {
			return parent::getAdminName();
		}
		$ownerKuser = kuserPeer::retrieveByPK($ownerKuserId);
		if (!$ownerKuser) {
			KalturaLog::err('Cannot retrieve kuser with id ['.$ownerKuserId.'] set as account owner for partner ['.$this->getId().']');
			return null;
		}
		return $ownerKuser->getFullName();		
	}
	
	public function getAdminEmail()
	{
		if ($this->tempAdminEmail) {
			return $this->tempAdminEmail;
		}
		
		$ownerKuserId = $this->getAccountOwnerKuserId();
		if (!$ownerKuserId) {
			return parent::getAdminEmail();
		}
		$ownerKuser = kuserPeer::retrieveByPK($ownerKuserId);
		if (!$ownerKuser) {
			KalturaLog::err('Cannot retrieve kuser with id ['.$ownerKuserId.'] set as account owner for partner ['.$this->getId().']');
			return null;
		}
		return $ownerKuser->getEmail();	
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
	
}
