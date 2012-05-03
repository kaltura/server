<?php

require_once(dirname(__FILE__) . '/../../../../../infra/cache/kCacheManager.php');

// NOTE: this code runs before the API dispatcher - should not use Propel / autoloader
class kSessionBase
{
	const SEPARATOR = ";";

	const TYPE_KS =  0; // change to be 1
	const TYPE_KAS = 1; // change to be 2

	const PRIVILEGE_EDIT = "edit";
	const PRIVILEGE_VIEW = "sview";
	const PRIVILEGE_LIST = "list"; // used to bypass the user filter in entry list
	const PRIVILEGE_DOWNLOAD = "download";
	const PRIVILEGE_EDIT_ENTRY_OF_PLAYLIST = "editplaylist";
	const PRIVILEGE_VIEW_ENTRY_OF_PLAYLIST = "sviewplaylist";
	const PRIVILEGE_ACTIONS_LIMIT = "actionslimit";
	const PRIVILEGE_SET_ROLE = "setrole";
	const PRIVILEGE_IP_RESTRICTION = "iprestrict";
	const PRIVILEGE_ENABLE_ENTITLEMENT = "enableentitlement";
	const PRIVILEGE_DISABLE_ENTITLEMENT = "disableentitlement";
	const PRIVILEGE_PRIVACY_CONTEXT = "privacycontext";

	const ADMIN_SECRET_CACHE_PREFIX = 'partner_admin_secret_';

	const INVALID_SESSION_KEY_PREFIX = 'invalid_session_';
	const INVALID_SESSIONS_SYNCED_KEY = 'invalid_sessions_synched';
	
	protected $hash = null;
	protected $real_str = null;
	protected $original_str = "";

	public $partner_id = null;
	public $partner_pattern = null;
	public $valid_until = null;
	public $type = null;
	public $rand = null;
	public $user = null;
	public $privileges = null;
	public $master_partner_id = null;
	public $additional_data = null;
	
	public static function getKSObject($encoded_str)
	{
		if (empty($encoded_str))
			return null;

		$ks = new kSessionBase();		
		if (!$ks->parseKS($encoded_str))
			return null;

		return $ks;
	}
	
	public function parseKS($encoded_str)
	{
		$str = base64_decode($encoded_str, true);
		if (strpos($str, "|") === false)
			return false;
			
		list($hash , $real_str) = explode( "|" , $str , 2 );

		$parts = explode(self::SEPARATOR, $real_str);
		if (count($parts) < 3)
			return false;
		
		list(
			$this->partner_id,
			$this->partner_pattern,
			$this->valid_until,
		) = $parts;

		if(isset($parts[3]))
			$this->type = $parts[3];

		if(isset($parts[4]))
			$this->rand = $parts[4];
		
		if(isset($parts[5]))
			$this->user = $parts[5];
			
		if(isset($parts[6]))
			$this->privileges = $parts[6];
			
		if(isset($parts[7]))
			$this->master_partner_id = $parts[7];
		
		if(isset($parts[8]))
			$this->additional_data = $parts[8];

		$this->hash = $hash;
		$this->real_str = $real_str;
		$this->original_str = $encoded_str;
		
		return true;
	}

	public function isAdmin()
	{
		return $this->type >= self::TYPE_KAS;
	}
	
	public function isWidgetSession()
	{
		return ($this->type == self::TYPE_KS) && ($this->user == 0) && ($this->privileges == "view:*");
	}
	
	static public function getAdminSecretFromCache($partnerId)
	{
		if (!function_exists('apc_fetch'))
			return null;			// no APC - can't get the partner secret here (DB not initialized)
		
		$adminSecret = apc_fetch(self::ADMIN_SECRET_CACHE_PREFIX . $partnerId);
		if (!$adminSecret)
			return null;			// admin secret not found in APC
		
		return $adminSecret;
	}
	
	protected function isKSInvalidated()
	{
		if (strpos($this->privileges, self::PRIVILEGE_ACTIONS_LIMIT) !== false)
			return null;			// cannot validate action limited KS at this level
		
		$memcache = kCacheManager::getCache(kCacheManager::MC_GLOBAL_KEYS);
		if (!$memcache)
			return null;			// failed to connect to memcache or memcache not enabled

		$ksKey = self::INVALID_SESSION_KEY_PREFIX . $this->hash;
		$keysToGet = array(self::INVALID_SESSIONS_SYNCED_KEY, $ksKey);
		$cacheResult = $memcache->multiGet($keysToGet);
		if ($cacheResult === false)
			return null;			// failed to get the keys

		if (!array_key_exists(self::INVALID_SESSIONS_SYNCED_KEY, $cacheResult) ||
			!$cacheResult[self::INVALID_SESSIONS_SYNCED_KEY])
			return null;			// invalid sessions not synched to memcache
		
		if (array_key_exists($ksKey, $cacheResult))
			return true;			// the session is invalid
		
		return false;
	}
	
	public function tryToValidateKS()
	{
		if (!$this->real_str || !$this->hash)
			return false;						// KS parsing failed
		
		if ($this->valid_until <= time())
			return false;						// KS is expired
			
		$adminSecret = self::getAdminSecretFromCache($this->partner_id);
		if (!$adminSecret)
			return false;						// admin secret not found in APC, can't validate the KS
			
		if (sha1($adminSecret . $this->real_str) != $this->hash)
			return false;						// wrong KS signature

		if ($this->partner_id == -1 ||			// Batch KS are never invalidated
			$this->isWidgetSession())			// Since anyone can create a widget session, no need to check for invalidation
			return true;

		$isInvalidated = $this->isKSInvalidated();
		if ($isInvalidated === null || $isInvalidated)
			return false;						// KS is invalidated, or failed to check
	
		return true;
	}
	
	public static function generateSession($adminSecretForSigning, $userId, $type, $partnerId, $expiry, $privileges)
	{
		$rand = microtime(true);
		$expiry = time() + $expiry;
		$fields = array(
			$partnerId,
			$partnerId,
			$expiry,
			$type,
			$rand,
			$userId,
			$privileges,
			'',
			'',
		);
		$info = implode ( ";" , $fields );

		$signature = sha1( $adminSecretForSigning . $info );
		$strToHash =  $signature . "|" . $info ;
		$encoded_str = base64_encode( $strToHash );

		return $encoded_str;
	}
}
