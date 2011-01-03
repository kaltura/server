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
			$ks->valid_until = time() + $desired_expiry_in_seconds ; // store in milliseconds to make comparison easier at validation time
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
		$result =  myPartnerUtils::isValidSecret ( $partner_id , $partner_secret , $partner_key , $ks_max_expiry_in_seconds , $admin );
		if ( $result >= 0 )
		{
			if ( $ks_max_expiry_in_seconds && $ks_max_expiry_in_seconds < $desired_expiry_in_seconds )
				$desired_expiry_in_seconds = 	$ks_max_expiry_in_seconds;

			//	echo "startKSession: from DB: $ks_max_expiry_in_seconds | desired: $desired_expiry_in_seconds " ;

			$ks = new ks();
			$ks->valid_until = time() + $desired_expiry_in_seconds ; // store in milliseconds to make comparison easier at validation time
//			$ks->type = $admin ? ks::TYPE_KAS : ks::TYPE_KS;
			if ( $admin == false )
				$ks->type = ks::TYPE_KS;
			else
				$ks->type = $admin ; // if the admin > 1 - use it rather than automatially setting it to be 2

			$ks->partner_id = $partner_id;
			$ks->master_partner_id = $master_partner_id;
			$ks->partner_pattern = $partner_id;
			$ks->error = 0;
			$ks->rand = microtime(true);
			$ks->user = $puser_id;
			$ks->privileges = $privileges;
			$ks->additional_data = $additional_data;
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
	// 2009-10-20 - don't limit the expiry of the ks !
/*		
		// TODO - verify the partner allows such sessions (basically allows external widgets)
		$ks_max_expiry_in_seconds =  myPartnerUtils::getExpiry ( $partner_id );

		if ( $ks_max_expiry_in_seconds < $desired_expiry_in_seconds )
			$desired_expiry_in_seconds = 	$ks_max_expiry_in_seconds;
*/
		$ks = new ks();
		$ks->valid_until = time() + $desired_expiry_in_seconds ; // store in milliseconds to make comparison easier at validation time
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

class ks
{
	const USER_WILDCARD = "*";
	const PRIVILEGE_WILDCARD = "*";

	static $ERROR_MAP = null; 
		
	const INVALID_STR = -1;
	const INVALID_PARTNER = -2;
	const INVALID_USER = -3;
	const INVALID_TYPE = -4;
	const EXPIRED = -5;
	const LOGOUT = -6;
	const OK = 1;

	const INVALID_LKS = -7;
	
	const TYPE_KS = 0; // change to be 1
	const TYPE_KAS =1; // change to be 2

	const PATTERN_WILDCARD = "*";

	const SEPARATOR = ";";
	
	const PRIVILEGE_EDIT = "edit";
	const PRIVILEGE_VIEW = "sview";
	const PRIVILEGE_DOWNLOAD = "download";
	const PRIVILEGE_EDIT_ENTRY_OF_PLAYLIST = "editplaylist";
	const PRIVILEGE_VIEW_ENTRY_OF_PLAYLIST = "sviewplaylist";

	public $partner_id = null;
	public $master_partner_id = null;
	public $valid_until = null;
	public $partner_pattern = null;
	public $type;
	public $error;
	public $rand;
	public $user;
	public $privileges;
	public $additional_data = null;

	private $original_str = "";

	private $valid_string=false;

	public static function getErrorStr ( $code )
	{
		if ( self::$ERROR_MAP == null )
		{
			self::$ERROR_MAP  = array ( self::INVALID_STR => "INVALID_STR" , self::INVALID_PARTNER => "INVALID_PARTNER" , self::INVALID_USER => "INVALID_USER" ,
				self::INVALID_TYPE => "INVALID_TYPE" , self::EXPIRED => "EXPIRED" , self::LOGOUT => "LOGOUT" , Partner::VALIDATE_LKS_DISABLED => "LKS_DISABLED");
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
		if ( empty ( $encoded_str ) ) return null;

		$str = base64_decode( $encoded_str , true ) ; // encode this string

		$ks = new ks();

		$real_str = $str;
		@list ( $hash , $real_str) = @explode ( "|" , $str , 2 );

//		echo "[$str]<br>[$hash]<br>[$real_str]<br>[" . self::hash ( $real_str ) . "]<br>";

		$ks->original_str = $encoded_str;

		$parts = explode(self::SEPARATOR, $real_str);
		list(
			$ks->partner_id, 
			$ks->partner_pattern, 
			$ks->valid_until, 
			$ks->type, 
			$ks->rand, 
		) = $parts;
		
		if(isset($parts[5]))
			$ks->user = $parts[5];
			
		if(isset($parts[6]))
			$ks->privileges = $parts[6];
			
		if(isset($parts[7]))
			$ks->master_partner_id = $parts[7];
		
		if(isset($parts[8]))
			$ks->additional_data = $parts[8];
			
		$salt = $ks->getSalt();
		 
		if (self::hash ( $salt , $real_str ) != $hash )
		{
			throw new Exception ( self::getErrorStr ( self::INVALID_STR ) );
			//$ks->valid_string = false;
			//return $ks;
		}
			

		$ks->valid_string = true;
		return $ks;
	}

	public function isAdmin()
	{
		return $this->type >= self::TYPE_KAS;
	}
	
	public function getUniqueString()
	{
		return $this->partner_id . $this->rand;
	}

	public function toSecureString ()
	{
		$fields = array ( $this->partner_id , $this->partner_pattern , $this->valid_until , $this->type , $this->rand , $this->user , $this->privileges , $this->master_partner_id , $this->additional_data);
		$str = implode ( self::SEPARATOR , $fields );

		$salt = $this->getSalt();
		$hashed_str = self::hash ( $salt , $str ) . "|" . $str ;
		$decoded_str = base64_encode( $hashed_str );

		return $decoded_str;
	}

	public function isValid( $partner_id , $puser_id , $type )
	{
		if ( ! $this->valid_string ) return self::INVALID_STR;
		if ( ! $this->matchPartner ( $partner_id ) ) return self::INVALID_PARTNER;
		if ( ! $this->matchUser ( $puser_id ) ) return self::INVALID_USER;
		if ( ! $this->type == $type  ) return self::INVALID_TYPE;
		if ( $this->expired ( ) ) return self::EXPIRED ;
	
		if($this->original_str)
		{
			$invalid = invalidSessionPeer::isInvalid($this->original_str, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
			if($invalid)
				return self::LOGOUT;
		}
		
		return self::OK;
	}
	
	public function isValidForPartner($partner_id)
	{
		if ( ! $this->valid_string ) return self::INVALID_STR;
		if ( ! $this->matchPartner ( $partner_id ) ) return self::INVALID_PARTNER;
		if ( $this->expired ( ) ) return self::EXPIRED ;

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
		return
			( $this->privileges == self::PRIVILEGE_WILDCARD ) ||
			( strpos ( $this->privileges,  $required_priv_name . ":" . self::PRIVILEGE_WILDCARD ) !== FALSE ) ||
			( strpos ( $this->privileges,  $required_priv_name . ":" . $required_priv_value ) !== FALSE );
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
				if(myPlaylistUtils::isEntryInPlaylist($entryId, $exPrivileges[1], $partnerId))
				{
					return true;
				}
				
			}
			
		}
		return false;
	}

	private function expired ( )
	{
		return ( time() >= $this->valid_until );
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

	private function getSalt ( )
	{
		$p = PartnerPeer::retrieveByPK( $this->partner_id )	;
		if ( ! $p )  return ""; // VERY big problem
		return $p->getAdminSecret();
	}
	
	private static function hash ( $salt , $str )
	{
		return sha1($salt.$str);
	}
	
	public function kill()
	{
		invalidSessionPeer::invalidateKs($this);		
	}
}

?>