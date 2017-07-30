<?php

class kSessionUtils
{
	const REQUIED_TICKET_NOT_ACCESSIBLE = 'N';
	const REQUIED_TICKET_NONE = 0;
	const REQUIED_TICKET_REGULAR = 1;
	const REQUIED_TICKET_ADMIN = 2;
	
	/**
	 * Will start a ks (always a regular one with view and edit privileges
	 * verification will be done according to the version
	 */
	public static function startKSessionFromLks ( $partner_id , $lks , $puser_id , $version , &$ks_str  , &$ks,	$desired_expiry_in_seconds=86400 )
	{
		$ks_max_expiry_in_seconds = ""; // see if we want to use the generic setting of the partner
		
		$result = myPartnerUtils::isValidLks ( $partner_id , $lks , $puser_id , $version , $ks_max_expiry_in_seconds );
		if ( $result >= 0 )
		{
			if ( $ks_max_expiry_in_seconds && $ks_max_expiry_in_seconds < $desired_expiry_in_seconds )
				$desired_expiry_in_seconds = 	$ks_max_expiry_in_seconds;

			$ks = new ks();
			$ks->valid_until = kApiCache::getTime() + $desired_expiry_in_seconds ; // store in milliseconds to make comparison easier at validation time
			$ks->type = ks::TYPE_KS;
			$ks->partner_id = $partner_id;
			$ks->partner_pattern = $partner_id;
			$ks->error = 0;
			$ks->rand = microtime(true);
			$ks->user = $puser_id;
			$ks->privileges = "view:*,edit:*"; // give privileges for view & edit
			$ks_str = $ks->toSecureString();
			return 0;
		}
		else
		{
			return $result;
		}
	}
	
	public static function createKSession($partner_id, $partner_secret, $puser_id, $expiry, $type, $privileges, $additional_data = null, $master_partner_id = null)
	{
		$ks = new ks();
		$ks->valid_until = kApiCache::getTime() + $expiry; // store in milliseconds to make comparison easier at validation time
		$ks->type = $type;
		$ks->partner_id = $partner_id;
		$ks->master_partner_id = $master_partner_id;
		$ks->partner_pattern = $partner_id;
		$ks->error = 0;
		$ks->rand = microtime(true);
		$ks->user = $puser_id;
		$ks->privileges = $privileges;
		$ks->additional_data = $additional_data;
		
		return $ks;
	}
		
	/*
	* will validate the partner_id, secret & key and return a kaltura-session string (KS)
	* the ks will be a 2-way hashed string that expires after a given period of time and holds data about the partner
	* if the partner is a "strong" partner, we may want to return the ks to allow him maipulate other partners (sub partners)
	* this will be done by storing the partner_id_list / partner_id_pattern in the ks.
	* The session can be given per puser - then the puser_id should not be null, OR
	*  it can be global and puser_id = null.
	* In the first case, it will be considered invalid for user that are not the ones that started the session
	*/
	public static function startKSession ( $partner_id , $partner_secret , $puser_id , &$ks_str  ,
		$desired_expiry_in_seconds=86400 , $admin = false , $partner_key = "" , $privileges = "", $master_partner_id = null, $additional_data = null)
	{
		$ks_max_expiry_in_seconds = ""; // see if we want to use the generic setting of the partner
		ks::validatePrivileges($privileges,  $partner_id);
		$result =  myPartnerUtils::isValidSecret ( $partner_id , $partner_secret , $partner_key , $ks_max_expiry_in_seconds , $admin );
		if ( $result >= 0 )
		{
			if ( $ks_max_expiry_in_seconds && $ks_max_expiry_in_seconds < $desired_expiry_in_seconds )
				$desired_expiry_in_seconds = 	$ks_max_expiry_in_seconds;

			//	echo "startKSession: from DB: $ks_max_expiry_in_seconds | desired: $desired_expiry_in_seconds " ;

			$ks_type = ks::TYPE_KS;
			if($admin)
				$ks_type = $admin ; // if the admin > 1 - use it rather than automatially setting it to be 2
				
			$ks = self::createKSession($partner_id, $partner_secret, $puser_id, $desired_expiry_in_seconds, $ks_type, $privileges, $additional_data, $master_partner_id);
			$ks_str = $ks->toSecureString();
			return 0;
		}
		else
		{
			return $result;
		}

	}

	public static function createKSessionNoValidations ( $partner_id , $puser_id , &$ks_str  ,
		$desired_expiry_in_seconds=86400 , $admin = false , $partner_key = "" , $privileges = "")
	{
		
		$ks_max_expiry_in_seconds =  myPartnerUtils::getExpiry ( $partner_id );
		if ($ks_max_expiry_in_seconds && ($ks_max_expiry_in_seconds < $desired_expiry_in_seconds))
			$desired_expiry_in_seconds = 	$ks_max_expiry_in_seconds;
		
		$ks = new ks();
		$ks->valid_until = kApiCache::getTime() + $desired_expiry_in_seconds ; // store in milliseconds to make comparison easier at validation time
//			$ks->type = $admin ? ks::TYPE_KAS : ks::TYPE_KS;
		if ( $admin == false )
			$ks->type = ks::TYPE_KS;
		else
			$ks->type = $admin ; // if the admin > 1 - use it rather than automatially setting it to be 2
		
		$ks->partner_id = $partner_id;
		$ks->partner_pattern = $partner_id;
		$ks->error = 0;
		$ks->rand = microtime(true);
		$ks->user = $puser_id;
		$ks->privileges = $privileges;
		$ks_str = $ks->toSecureString();
		return 0;
	}

	/**
	 * @param string $ks_str
	 * @return ks
	 */
	public static function crackKs ( $ks_str )
	{
		$ks = ks::fromSecureString( $ks_str );
		return $ks;
	}
	
	/**
	* will validate the partner_id, secret & key and return a kaltura-admin-session string (KAS)
	* this key will be good for the admin part of the API, such as reports/lists of data/batch deletion
	*/
	public static function startKAdminSession ( $partner_id , $partner_secret , $puser_id , &$ks_str  ,
		$desired_expiry_in_seconds=86400 , $partner_key = "" , $privileges = "")
	{
		return self::startKSession ( $partner_id , $partner_secret , $puser_id , $ks_str  ,	$desired_expiry_in_seconds , true ,  $partner_key , $privileges );
	}

	/*
	 * Will combine all validation methods regardless the ticket type
	 * if the ks exists - use it - it's already cracked but may not be a valid one (it was created before the partner id was known)
	 * the $required_ticket_type can be a number or a list of numbers separated by ',' - this means any of the types is valid
	 * the ks->type can be a number greater than 0.
	 * if the ks->type & required_ticket_type > 0 - it means the ks->type has the relevant bit of the required_ticket_type -
	 * 		consider it a match !
	 * if the required_ticket_type is a list - there should be at least one match for the validation to succeed
	 */
	public static function validateKSession2 ( $required_ticket_type_str , $partner_id , $puser_id , $ks_str ,&$ks)
	{
		$res = 0;
		$required_ticket_type_arr = explode ( ',' , $required_ticket_type_str );
		foreach ( $required_ticket_type_arr as $required_ticket_type )
		{
			$res = ks::INVALID_TYPE; // assume the type is not valid.

			// TODO - remove !!!!!
			$ks_type = $ks->type + 1; // 0->1 and 1->2
 
			// TODO - fix bug ! should work with bitwise operators
			if ( ( $ks_type & $required_ticket_type ) == $required_ticket_type )
			{
				if ($ks_type == self::REQUIED_TICKET_REGULAR )
				{
					$res = $ks->isValid( $partner_id , $puser_id  , ks::TYPE_KS );
				}
				elseif ( $ks_type > self::REQUIED_TICKET_REGULAR )
				{
					// for types greater than 1 (REQUIED_TICKET_REGULAR) - it is assumed the kas was used.
					$res = $ks->isValid( $partner_id , $puser_id  , ks::TYPE_KAS );
				}
			}
			if ( $res > 0 ) return $res;
		}
		return $res;
	}
	
	public static function validateKSessionNoTicket($partner_id, $puser_id, $ks_str, &$ks)
	{
		if ( !$ks_str )
		{
			return false;
		}
		$ks = ks::fromSecureString( $ks_str );
		return $ks->isValid( $partner_id, $puser_id, false );
	}
	
	/**
		validate the time and data of the ks
		If the puser_id was set in the KS, it is expected to be equal to the puser_id here
	*/
	public static function validateKSession ( $partner_id , $puser_id , $ks_str ,&$ks)
	{
		if ( !$ks_str )
		{
			return false;
		}
		$ks = ks::fromSecureString( $ks_str );
		return $ks->isValid( $partner_id , $puser_id  , ks::TYPE_KS );
	}

	public static function validateKAdminSession ( $partner_id , $puser_id , $kas_str ,&$ks)
	{
		if ( !$kas_str )
		{
			return false;
		}

		$kas = ks::fromSecureString( $kas_str );
		return $kas->isValid( $partner_id , $puser_id  , ks::TYPE_KAS );
	}

	public static function killKSession ( $ks )
	{
		try
		{
			$ksObj = ks::fromSecureString($ks);
			if($ksObj)
				$ksObj->kill();
		}
		catch(Exception $e){}
	}
}

class ks extends kSessionBase
{
	const USER_WILDCARD = "*";
	const PRIVILEGE_WILDCARD = "*";

	static $ERROR_MAP = null;
			
	const PATTERN_WILDCARD = "*";
	
	public $error;
	
	/**
	 * @var kuser
	 */
	protected $kuser = null;

	public static function getErrorStr ( $code )
	{
		if ( self::$ERROR_MAP == null )
		{
			self::$ERROR_MAP  = array ( 
				self::INVALID_STR => "INVALID_STR", 
				self::INVALID_PARTNER => "INVALID_PARTNER", 
				self::INVALID_USER => "INVALID_USER", 
				self::INVALID_TYPE => "INVALID_TYPE", 
				self::EXPIRED => "EXPIRED", 
				self::LOGOUT => "LOGOUT", 
				Partner::VALIDATE_LKS_DISABLED => "LKS_DISABLED", 
				self::EXCEEDED_ACTIONS_LIMIT => 'EXCEEDED_ACTIONS_LIMIT', 
				self::EXCEEDED_RESTRICTED_IP => 'EXCEEDED_RESTRICTED_IP', 
				self::EXCEEDED_RESTRICTED_URI => 'EXCEEDED_RESTRICTED_URI', 
			);
		}
		
		$str =  @self::$ERROR_MAP[$code];
		if ( ! $str ) $str = "?";
		return $str;
	}
	
	public function getOriginalString()
	{
		return $this->original_str;
	}
	
	/**
	 * @param string $encoded_str
	 * @return ks
	 */
	public static function fromSecureString ( $encoded_str )
	{
		if(empty($encoded_str))
			return null;

		$ks = new ks();
		if (!$ks->parseKS($encoded_str))
		{
			throw new Exception ( self::getErrorStr ( self::INVALID_STR ) );
		}

		return $ks;
	}

	public function getUniqueString()
	{
		return $this->partner_id . $this->rand;
	}
	
	public function toSecureString()
	{
		list($ksVersion, $secret) = $this->getKSVersionAndSecret($this->partner_id);
		return kSessionBase::generateSession(
			$ksVersion,
			$secret,
			$this->user,
			$this->type,
			$this->partner_id,
			$this->valid_until - time(),
			$this->privileges,
			$this->master_partner_id,
			$this->additional_data);
	}
	
	public function isValid( $partner_id , $puser_id , $type = false)
	{		
		$result = $this->tryToValidateKS();
		if ($result != self::UNKNOWN && $result != self::OK)
		{
			return $result;
		}
		
		if ( ! $this->matchPartner ( $partner_id ) ) return self::INVALID_PARTNER;
		if ( ! $this->matchUser ( $puser_id ) ) return self::INVALID_USER;
		if ($type !== false) { // do not check ks type
			if ( ! $this->type == $type  ) return self::INVALID_TYPE;
		}
		
		if($result == self::UNKNOWN)
		{
			$criteria = new Criteria();
			
			$ksCriterion = $criteria->getNewCriterion(invalidSessionPeer::TYPE, invalidSession::INVALID_SESSION_TYPE_KS);
			$ksCriterion->addAnd($criteria->getNewCriterion(invalidSessionPeer::KS, $this->getHash()));
			
			$sessionId = $this->getSessionIdHash();
			if($sessionId) {
				$invalidSession = $criteria->getNewCriterion(invalidSessionPeer::KS, $sessionId);
				$invalidSession->addAnd($criteria->getNewCriterion(invalidSessionPeer::TYPE, invalidSession::INVALID_SESSION_TYPE_SESSION_ID));
				$ksCriterion->addOr($invalidSession);
			}
			
			$criteria->add($ksCriterion);
			$dbKs = invalidSessionPeer::doSelectOne($criteria);
			if ($dbKs)
			{
				$currentActionLimit = $dbKs->getActionsLimit();
				if(is_null($currentActionLimit))
					return self::LOGOUT;
				elseif($currentActionLimit <= 0)
					return self::EXCEEDED_ACTIONS_LIMIT;

				$dbKs->setActionsLimit($currentActionLimit - 1);
				$dbKs->save();
			}
			else
			{
				$limit = $this->isSetLimitAction();
				if ($limit)
					invalidSessionPeer::actionsLimitKs($this, $limit - 1);
			}
		}
		
		// creates the kuser
		if($partner_id != Partner::BATCH_PARTNER_ID &&
			PermissionPeer::isValidForPartner(PermissionName::FEATURE_END_USER_REPORTS, $partner_id))
		{
			$this->kuser = kuserPeer::createKuserForPartner($partner_id, $puser_id);
			if(!$puser_id && $this->kuser->getScreenName() != 'Unknown')
			{
				$this->kuser->setScreenName('Unknown');
				$this->kuser->save();
			}
		}
		
		return self::OK;
	}
	
	/**
	 * @return kuser
	 */
	public function getKuser()
	{
		if(!$this->kuser)
			$this->kuser = kuserPeer::getKuserByPartnerAndUid($this->partner_id, $this->user);
			
		return $this->kuser;
	}
	
	/**
	 * @return int
	 */
	public function getKuserId()
	{
		$this->getKuser();
		
		if($this->kuser)
			return $this->kuser->getId();
			
		return null;
	}
	
	public function isValidForPartner($partner_id)
	{
		$result = $this->isValidBase();
		if ($result != self::OK)
		{
			return $result;
		}
		
		if ( ! $this->matchPartner ( $partner_id ) ) return self::INVALID_PARTNER;
		return self::OK;
	}

	// TODO - find a way to verify the privileges -
	// the privileges is a string with a separators and the required_privs is infact a substring
	public function verifyPrivileges ( $required_priv_name , $required_priv_value = null )
	{
		// need the general privilege not a specific value
		if ( empty ( $required_priv_value ) )
			return strpos ( $this->privileges,  $required_priv_name ) !== FALSE ;

		// either the original privileges were general - with a value of a wildcard
		if ( ( $this->privileges == self::PRIVILEGE_WILDCARD ) ||
			 ( strpos ( $this->privileges,  $required_priv_name . ":" . self::PRIVILEGE_WILDCARD ) !== false ) ||
			 ( strpos ( $this->privileges,  $required_priv_name . ":" . $required_priv_value ) !== false ) )
			 {
			 	return true;
			 }
		else if (in_array(self::PRIVILEGE_WILDCARD, $this->parsedPrivileges) ||
		(isset ($this->parsedPrivileges[$required_priv_name]) && in_array($required_priv_value, $this->parsedPrivileges[$required_priv_name])))
		{
			return true;
		}
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
		if ( $required_priv_name == ks::PRIVILEGE_EDIT &&
			$this->verifyPlaylistPrivileges(ks::PRIVILEGE_EDIT_ENTRY_OF_PLAYLIST, $required_priv_value, $partnerId))
		{
			return true;
		}
		
	    if ( $required_priv_name == ks::PRIVILEGE_VIEW &&
			$this->verifyPlaylistPrivileges(ks::PRIVILEGE_VIEW_ENTRY_OF_PLAYLIST, $required_priv_value, $partnerId))
		{
			return true;
		}

		if ( $required_priv_name == ks::PRIVILEGE_VIEW &&
			$this->verifyRedirectEntryId(ks::PRIVILEGE_VIEW, $required_priv_value))
		{
			return true;
		}

		return false;
	}

	public function verifyRedirectEntryId($privilegeName, $entryId)
	{
		$allPrivileges = explode(',', $this->privileges);
		foreach($allPrivileges as $privilege)
		{
			$exPrivilege = explode(':', $privilege);
			if ($exPrivilege[0] == $privilegeName && isset($exPrivilege[1]))
			{
				$privilegeObjectId = $exPrivilege[1];
				$entry = entryPeer::retrieveByPK($privilegeObjectId);
				if($entry && $entry->getRedirectEntryId() == $entryId)
					return true;
			}
		}
		return false;
	}
	
	public function verifyPlaylistPrivileges($required_priv_name, $entryId, $partnerId)
	{
		// break all privileges to their pairs - this is to support same "multi-priv" method expected for
		// edit privilege (edit:XXX,edit:YYY,...)
		$allPrivileges = explode(',', $this->privileges);
		// foreach pair - check privileges on playlist
		foreach($allPrivileges as $priv)
		{
			// extract playlist ID from pair
			$exPrivileges = explode(':', $priv);
			if($exPrivileges[0] == $required_priv_name)
			{
				// if found in playlist - return true
				if(myPlaylistUtils::isEntryReferredByPlaylist($entryId, $exPrivileges[1], $partnerId))
				{
					return true;
				}
				
			}
			
		}
		return false;
	}

	public function isSetLimitAction()
	{
		// break all privileges to their pairs - this is to support same "multi-priv" method expected for
		// edit privilege (edit:XXX,edit:YYY,...)
		$allPrivileges = explode(',', $this->privileges);
		// foreach pair - check privileges on playlist
		foreach($allPrivileges as $priv)
		{
			// extract playlist ID from pair
			$exPrivileges = explode(':', $priv);
			if ($exPrivileges[0] == self::PRIVILEGE_ACTIONS_LIMIT)
				if ((is_numeric($exPrivileges[1])) && ($exPrivileges[1] > 0)){
					return $exPrivileges[1];
				}else{
					throw new kCoreException(kCoreException::INTERNAL_SERVER_ERROR, APIErrors::INVALID_ACTIONS_LIMIT);
				}
		}
		
		return false;
	}
		
	public function getEnableEntitlement()
	{
		// break all privileges to their pairs - this is to support same "multi-priv" method expected for
		// edit privilege (edit:XX,edit:YYY,...)
		$allPrivileges = explode(',', $this->privileges);
		// foreach pair - check privileges on playlist
		foreach($allPrivileges as $priv)
		{
			if ($priv == self::PRIVILEGE_ENABLE_ENTITLEMENT)
				return true;
		}
		
		return false;
	}

	public function getDisableEntitlement()
	{
		// break all privileges to their pairs - this is to support same "multi-priv" method expected for
		// edit privilege (edit:XX,edit:YYY,...)
		$allPrivileges = explode(',', $this->privileges);
		// foreach pair - check privileges on playlist
		foreach($allPrivileges as $priv)
		{
			if ($priv == self::PRIVILEGE_DISABLE_ENTITLEMENT)
				return true;
		}
		
		return false;
	}
	
	public function getEnableCategoryModeration()
	{
		// break all privileges to their pairs - this is to support same "multi-priv" method expected for
		// edit privilege (edit:XX,edit:YYY,...)
		$allPrivileges = explode(',', $this->privileges);
		// foreach pair - check privileges on playlist
		foreach($allPrivileges as $priv)
		{
			if ($priv == self::PRIVILEGE_ENABLE_CATEGORY_MODERATION)
				return true;
		}
		
		return false;
	}
	
	public function getDisableEntitlementForEntry()
	{
		// break all privileges to their pairs - this is to support same "multi-priv" method expected for
		// edit privilege (edit:XX,edit:YYY,...)
		$allPrivileges = explode(',', $this->privileges);
		
		$entries = array();
		
		// foreach pair - check privileges on playlist
		foreach($allPrivileges as $priv)
		{
			$exPrivileges = explode(':', $priv);
			if ($exPrivileges[0] == self::PRIVILEGE_DISABLE_ENTITLEMENT_FOR_ENTRY)
			{
				$entries[] =  $exPrivileges[1];
				
				$entry = entryPeer::retrieveByPKNoFilter($exPrivileges[1], null, false);
				if($entry && $entry->getParentEntryId())
				{
					$entries[] = $entry->getParentEntryId();
				}
			}
		}
		
		return $entries;
	}
	
	public function getPrivilegeByName($privilegeName)
	{
		// edit privilege (edit:XX,edit:YYY,...)
		$allPrivileges = explode(',', $this->privileges);
		// foreach pair - check privileges on playlist
		foreach($allPrivileges as $privilege)
		{
			if ($privilege == $privilegeName)
				return true;
		}
		
		return false;
	}
	
	public function getPrivacyContext()
	{
		// break all privileges to their pairs - this is to support same "multi-priv" method expected for
		// edit privilege (edit:XX,edit:YYY,...)
		$allPrivileges = explode(',', $this->privileges);
		// foreach pair - check privileges on playlist
		
		foreach($allPrivileges as $priv)
		{
			$exPrivileges = explode(':', $priv, 2);
			//validate setRole
			if (count($exPrivileges) == 2 && $exPrivileges[0] == self::PRIVILEGE_PRIVACY_CONTEXT)
				return $exPrivileges[1];
		}
		
		return null;
	}
	
	public function getLimitEntry()
	{
		return $this->getPrivilegeValue(self::PRIVILEGE_LIMIT_ENTRY, null);
	}

	public function getRole()
	{
		// break all privileges to their pairs - this is to support same "multi-priv" method expected for
		// edit privilege (edit:XX,edit:YYY,...)
		$allPrivileges = explode(',', $this->privileges);
		// foreach pair - check privileges on playlist
		foreach($allPrivileges as $priv)
		{
			// extract playlist ID from pair
			$exPrivileges = explode(':', $priv);
			if ($exPrivileges[0] == self::PRIVILEGE_SET_ROLE)
			{
				if (isset($exPrivileges[1]) && (is_numeric($exPrivileges[1])) && ($exPrivileges[1] < 0))
				{
					throw new kCoreException(kCoreException::INTERNAL_SERVER_ERROR, APIErrors::INVALID_SET_ROLE);
				}
				
				return isset($exPrivileges[1]) ? $exPrivileges[1] : false;
			}
		}
		
		return false;
	}
	
	public static function validatePrivileges ( $privileges, $partnerId )
	{
		// break all privileges to their pairs - this is to support same "multi-priv" method expected for
		// edit privilege (edit:XXX,edit:YYY,...)
		$allPrivileges = explode(',', $privileges);
		// foreach pair - check privileges on playlist
		foreach($allPrivileges as $priv)
		{
			// extract playlist ID from pair
			$exPrivileges = explode(':', $priv);
			//validate setRole
			if ($exPrivileges[0] == self::PRIVILEGE_SET_ROLE){
				$c = new Criteria();
				$c->addAnd(is_numeric($exPrivileges[1]) ? UserRolePeer::ID : UserRolePeer::SYSTEM_NAME, $exPrivileges[1], Criteria::EQUAL);
				$partnerIdsArray = array_map('strval', array($partnerId, PartnerPeer::GLOBAL_PARTNER));
				$c->addAnd(UserRolePeer::PARTNER_ID, $partnerIdsArray, Criteria::IN);
				$roleId = UserRolePeer::doSelectOne($c);
				
				if ($roleId){
					$roleIds = $roleId->getId();
				}else{
					throw new kCoreException(kCoreException::INTERNAL_SERVER_ERROR, APIErrors::UNKNOWN_ROLE_ID ,$exPrivileges[1]);
				}
			}
		}
	}

	public function hasPrivilege($privilegeName)
	{
		if (!is_array($this->parsedPrivileges))
			return false;

		return isset($this->parsedPrivileges[$privilegeName]);
	}

	public function getPrivilegeValues($privilegeName, $default = array())
	{
		if ($this->hasPrivilege($privilegeName))
			return $this->parsedPrivileges[$privilegeName];
		else
			return $default;
	}

	public function getPrivilegeValue($privilegeName, $default = null)
	{
		$values = $this->getPrivilegeValues($privilegeName);
		if (isset($values[0]))
			return $values[0];
		else
			return $default;
	}
	
	private function matchPartner ( $partner_id )
	{
		if ( $this->partner_id == $partner_id ) return true;
		// removed for security reasons - a partner cannot decide to work on other partners
//		if ( $this->partner_pattern == self::PATTERN_WILDCARD ) // TODO - change to some regular expression to match the partner_id
//			return true;
		return false;
	}

	private function matchUser ( $puser_id )
	{
//		echo __METHOD__ . " [{$this->user}] [{$puser_id}]<br>";

		if ( $this->user == null ) return true; // the ticket is a generic one - fits any user
		if ( $this->user == self::USER_WILDCARD  ) return true;// the ticket is a generic one - fits any user

		return $this->user == $puser_id;
	}

	protected function getKSVersionAndSecret($partnerId)
	{
		$result = parent::getKSVersionAndSecret($partnerId);
		if ($result)
			return $result;
		
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if (!$partner)
			return array(1, null); // VERY big problem

		$ksVersion = $partner->getKSVersion();

		$cacheKey = self::getSecretsCacheKey($partnerId);
		$cacheSections = kCacheManager::getCacheSectionNames(kCacheManager::CACHE_TYPE_PARTNER_SECRETS);
		foreach ($cacheSections as $cacheSection)
		{
			$cacheStore = kCacheManager::getCache($cacheSection);
			if (!$cacheStore)
				continue;
			
			$cacheStore->set($cacheKey, array($partner->getAdminSecret(), $partner->getSecret(), $ksVersion));
		}
		
		return array($ksVersion, $partner->getAdminSecret());
	}
	
	protected function logError($msg)
	{
		KalturaLog::err($msg);
	}
		
	public function kill()
	{
		invalidSessionPeer::invalidateKs($this);
	}
}
