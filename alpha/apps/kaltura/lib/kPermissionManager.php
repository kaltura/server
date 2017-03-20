<?php

class kPermissionManager implements kObjectCreatedEventConsumer, kObjectChangedEventConsumer
{
	// -------------------
	// -- Class members --
	// -------------------
		
	const GLOBAL_CACHE_KEY_PREFIX = 'kPermissionManager_'; // Prefix added for all key names stored in the cache
	
	private static $map = array(); // Local map of permission items allowed for the current role
	
	const API_ACTIONS_ARRAY_NAME    = 'api_actions';      // name of $map's api actions array
	const API_PARAMETERS_ARRAY_NAME = 'api_parameters';   // name of $map's api parameters array
	const PARTNER_GROUP_ARRAY_NAME  = 'partner_group';    // name of $map's partner group array
	const PERMISSION_NAMES_ARRAY    = 'permission_names'; // name of $map's permission names array
			
	private static $lastInitializedContext = null; // last initialized security context (ks + partner id)
	private static $cacheWatcher = null;
	private static $useCache = true;     // use cache or not
	
	private static $ksUserId = null;
	private static $adminSession = false; // is admin session
	private static $ksPartnerId = null;
	private static $requestedPartnerId = null;
	private static $ksString = null;
	private static $roleIds = null;
	private static $operatingPartnerId = null;
	
	private static $cacheStores = array();
	
	/**
	 * @var Partner
	 */
	private static $operatingPartner = null;
	
	/**
	 * @var kuser
	 */
	private static $kuser = null;
		
	
	// ----------------------------
	// -- Cache handling methods --
	// ----------------------------
	
	
	private static function useCache()
	{
		if (self::$useCache)
		{
			return true;
		}
		return false;
	}
	
	/**
	 * @param int $roleId
	 * @return cache key name for the given role id
	 */
	private static function getRoleIdKey($roleId, $partnerId)
	{
		if (is_null($roleId)) {
			$roleId = 'null';
		}
		if (is_null($partnerId)) {
			$partnerId = 'null';
		}
		$key = 'role_'.$roleId.'_partner_'.$partnerId.'_internal_'.intval(kIpAddressUtils::isInternalIp());
		return $key;
	}
	
	private static function getCacheKeyPrefix()
	{
		$result = self::GLOBAL_CACHE_KEY_PREFIX;
		if (kConf::hasParam('permission_cache_version'))
			$result .= kConf::get('permission_cache_version');
		return $result;
	}
	
	/**
	 * Get value from cache for the given key
	 * @param string $key
	 */
	private static function getFromCache($key, $roleCacheDirtyAt)
	{
		if (!self::useCache())
		{
			return null;
		}
		
		self::$cacheStores = array();
		
		$cacheLayers = kCacheManager::getCacheSectionNames(kCacheManager::CACHE_TYPE_PERMISSION_MANAGER);
		
		foreach ($cacheLayers as $cacheLayer)
		{
			$cacheStore = kCacheManager::getCache($cacheLayer);
			if (!$cacheStore)
				continue;
				
			$cacheRole = $cacheStore->get(self::getCacheKeyPrefix() . $key); // try to fetch from cache
			if ( !$cacheRole || !isset($cacheRole['updatedAt']) || ( $cacheRole['updatedAt'] < $roleCacheDirtyAt ) )
			{
				self::$cacheStores[] = $cacheStore;
				continue;
			}

			$map = $cacheStore->get(self::getCacheKeyPrefix() . $cacheRole['mapHash']); // try to fetch from cache
			if ( !$map )
			{
				self::$cacheStores[] = $cacheStore;
				continue;
			}
				
			KalturaLog::debug("Found a cache value for key [$key] map hash [".$cacheRole['mapHash']."] in layer [$cacheLayer]");
			self::storeInCache($key, $cacheRole, $map);		// store in lower cache layers
			self::$cacheStores[] = $cacheStore;

			return $map;
		}

		KalturaLog::debug("No cache value found for key [$key]");
		return null;
	}
	
	/**
	 *
	 * Store given value in cache for with the given key as an identifier
	 * @param string $key
	 * @param string $value
	 */
	private static function storeInCache($key, $cacheRole, $map)
	{
		if (!self::useCache())
		{
			return;
		}
		
		foreach (self::$cacheStores as $cacheStore)
		{
			if (!$cacheStore->set(
				self::getCacheKeyPrefix() . $key,
				$cacheRole,
				kConf::get('apc_cache_ttl')))
				continue;

			$success = $cacheStore->set(
				self::getCacheKeyPrefix() . $cacheRole['mapHash'],
				$map,
				kConf::get('apc_cache_ttl')); // try to store in cache
					
			if ($success)
			{
				KalturaLog::debug("New value stored in cache for key [$key] map hash [".$cacheRole['mapHash']."]");
			}
			else
			{
				KalturaLog::debug("No cache value stored for key [$key] map hash [".$cacheRole['mapHash']."]");
			}
		}
	}
	
	
	
	// ----------------------------
	// -- Initialization methods --
	// ----------------------------
	
	
	/**
	 * Throws an error if init function hasn't been executed yet
	 * @throws Exception
	 */
	private static function errorIfNotInitialized()
	{
		if (is_null(self::$lastInitializedContext))
		{
			throw new Exception('Permission manager has not yet been initialized');
		}
	}
	
	
	/**
	 * Init an empty cache map array for holding "organized" permission items
	 */
	private static function initEmptyMap()
	{
		$map = array();
		$map[self::API_ACTIONS_ARRAY_NAME]    = array();
		$map[self::API_PARAMETERS_ARRAY_NAME] = array();
		$map[self::PARTNER_GROUP_ARRAY_NAME]  = array();
		$map[self::API_PARAMETERS_ARRAY_NAME][ApiParameterPermissionItemAction::READ]   = array();
		$map[self::API_PARAMETERS_ARRAY_NAME][ApiParameterPermissionItemAction::UPDATE] = array();
		$map[self::API_PARAMETERS_ARRAY_NAME][ApiParameterPermissionItemAction::INSERT] = array();
		$map[self::PERMISSION_NAMES_ARRAY] = array();
		return $map;
	}
	
	
	private static function getPermissions($roleId)
	{
		$map = self::initEmptyMap();
		
		// get cache dirty time
		$roleCacheDirtyAt = 0;
		if (self::$operatingPartner) {
			$roleCacheDirtyAt = self::$operatingPartner->getRoleCacheDirtyAt();
		}
		
		// get role from cache
		$roleCacheKey = self::getRoleIdKey($roleId, self::$operatingPartnerId);
		$cacheRole = self::getFromCache($roleCacheKey, $roleCacheDirtyAt);
		
		// compare updatedAt between partner dirty flag and cache
		if ( $cacheRole )
		{
			return $cacheRole; // initialization from cache finished
		}
		
		// cache is not updated - delete stored value and re-init from DB
		
		$dbRole = null;
		if (!is_null($roleId))
		{
			UserRolePeer::setUseCriteriaFilter(false);
			$dbRole = UserRolePeer::retrieveByPK($roleId);
			UserRolePeer::setUseCriteriaFilter(true);
			
			if (!$dbRole)
			{
				KalturaLog::alert('User role ID ['.$roleId.'] set for user ID ['.self::$ksUserId.'] of partner ['.self::$operatingPartnerId.'] was not found in the DB');
				throw new kPermissionException('User role ID ['.$roleId.'] set for user ID ['.self::$ksUserId.'] of partner ['.self::$operatingPartnerId.'] was not found in the DB', kPermissionException::ROLE_NOT_FOUND);
			}
		}
		
		$map = self::getPermissionsFromDb($dbRole);
		
		// update cache
		$cacheRole = array(
			'updatedAt' => time(),
			'mapHash' => md5(serialize($map)));
		self::storeInCache($roleCacheKey, $cacheRole, $map);
		
		return $map;
	}
		
		
	/**
	 * Init permission items map from DB for the given role
	 * @param UserRole $dbRole
	 */
	private static function getPermissionsFromDb($dbRole)
	{
		$map = self::initEmptyMap();
		
		// get all permission object names from role record
		if ($dbRole)
		{
			$tmpPermissionNames = $dbRole->getPermissionNames(true);
			$tmpPermissionNames = array_map('trim', explode(',', $tmpPermissionNames));
		}
		else {
			$tmpPermissionNames = array();
		}
		
		// add always allowed permissions
		if (self::$operatingPartner) {
			$alwaysAllowed = self::$operatingPartner->getAlwaysAllowedPermissionNames();
			$alwaysAllowed = array_map('trim', explode(',', $alwaysAllowed));
		}
		else {
			$alwaysAllowed = array(PermissionName::ALWAYS_ALLOWED_ACTIONS);
		}
		$tmpPermissionNames = array_merge($tmpPermissionNames, $alwaysAllowed);
		
		// if the request sent from the internal server set additional permission allowing access without KS
		// from internal servers
		if (kIpAddressUtils::isInternalIp())
		{
			KalturaLog::debug('IP in range, adding ALWAYS_ALLOWED_FROM_INTERNAL_IP_ACTIONS permission');
			$alwaysAllowedInternal = array(PermissionName::ALWAYS_ALLOWED_FROM_INTERNAL_IP_ACTIONS);
			$tmpPermissionNames = array_merge($tmpPermissionNames, $alwaysAllowedInternal);
		}
		
		$permissionNames = array();
		foreach ($tmpPermissionNames as $name)
		{
			$permissionNames[$name] = $name;
		}
		$map[self::PERMISSION_NAMES_ARRAY] = $permissionNames;
		
		// get mapping of permissions to permission items
		$c = new Criteria();
		$c->addAnd(PermissionPeer::NAME, $permissionNames, Criteria::IN);
		$c->addAnd(PermissionPeer::PARTNER_ID, array(strval(PartnerPeer::GLOBAL_PARTNER), strval(self::$operatingPartnerId)), Criteria::IN);
		$c->addAnd(PermissionItemPeer::PARTNER_ID, array(strval(PartnerPeer::GLOBAL_PARTNER), strval(self::$operatingPartnerId)), Criteria::IN);
		$lookups = PermissionToPermissionItemPeer::doSelectJoinAll($c);
		foreach ($lookups as $lookup)
		{
			$item       = $lookup->getPermissionItem();
			$permission = $lookup->getPermission();
			
			if (!$item)	{
				KalturaLog::err('PermissionToPermissionItem id ['.$lookup->getId().'] is defined with PermissionItem id ['.$lookup->getPermissionItemId().'] which does not exists!');
				continue;
			}
			
			if (!$permission) {
				KalturaLog::err('PermissionToPermissionItem id ['.$lookup->getId().'] is defined with Permission name ['.$lookup->getPermissionName().'] which does not exists!');
				continue;
			}
				
			// organize permission items in local arrays
			$type = $item->getType();
			if ($type == PermissionItemType::API_ACTION_ITEM)
			{
				self::addApiAction($map, $item);
			}
			else if ($type == PermissionItemType::API_PARAMETER_ITEM)
			{
				self::addApiParameter($map, $item);
			}
		}
		
		// set partner group permission
		$c = new Criteria();
		$c->addAnd(PermissionPeer::PARTNER_ID, self::$operatingPartnerId, Criteria::EQUAL);
		$c->addAnd(PermissionPeer::TYPE, PermissionType::PARTNER_GROUP, Criteria::EQUAL);
		$partnerGroupPermissions = PermissionPeer::doSelect($c);
		foreach ($partnerGroupPermissions as $pgPerm)
		{
			self::addPartnerGroupAction($map, $pgPerm);
		}
		
		return $map;
	}
	
	
	
	// ---------------------------------------
	// -- Permission array handling methods --
	// ---------------------------------------
	
	/**
	 * Add an api action permission to the local map
	 * @param array $map map to fill
	 * @param kApiActionPermissionItem $item
	 */
	private static function addApiAction(array &$map, kApiActionPermissionItem $item)
	{
		$service = strtolower($item->getService());
		$action = strtolower($item->getAction());
		if (!isset($map[self::API_ACTIONS_ARRAY_NAME][$service])) {
			$map[self::API_ACTIONS_ARRAY_NAME][$service] = array();
			$map[self::API_ACTIONS_ARRAY_NAME][$service][$action] = array();
		}
		else if (!in_array($action, $map[self::API_ACTIONS_ARRAY_NAME][$service], true)) {
			$map[self::API_ACTIONS_ARRAY_NAME][$service][$action] = array();
		}
	}
	
	
	/**
	 * Add an api parameter permission to the local map
	 * @param array $map map to fill
	 * @param kApiParameterPermissionItem $item
	 */
	private static function addApiParameter(array &$map, kApiParameterPermissionItem $item)
	{
		$itemAction = strtolower($item->getAction());
		$itemObject = strtolower($item->getObject());
		if (!isset($map[self::API_PARAMETERS_ARRAY_NAME][$itemAction][$itemObject])) {
			$map[self::API_PARAMETERS_ARRAY_NAME][$itemAction][$itemObject] = array();
		}
		$map[self::API_PARAMETERS_ARRAY_NAME][$itemAction][$itemObject][strtolower($item->getParameter())] = true;
	}
	
	/**
	 * Add a partner group permission to the local map for the given action
	 * @param array $map map to fill
	 * @param Permission $permission partner group permission object
	 */
	private static function addPartnerGroupAction(array &$map, Permission $permission)
	{
		$partnerGroup = $permission->getPartnerGroup();
		if (!$permission->getPartnerGroup())
		{
			KalturaLog::notice('No partner group defined for permission id ['.$permission->getId().'] with type partner group ['.$permission->getType().']');
			return;
		}
		$partnerGroup = explode(',', trim($partnerGroup, ','));
		
		$permissionItems = $permission->getPermissionItems();
		
		foreach ($permissionItems as $item)
		{
			if ($item->getType() != PermissionItemType::API_ACTION_ITEM)
			{
				KalturaLog::notice('Permission item id ['.$item->getId().'] is not of type PermissionItemType::API_ACTION_ITEM but still defined in partner group permission id ['.$permission->getId().']');
				continue;
			}
			$service = strtolower($item->getService());
			$action  = strtolower($item->getAction());
			
			if (!isset($map[self::PARTNER_GROUP_ARRAY_NAME][$service]))
			{
				$map[self::PARTNER_GROUP_ARRAY_NAME][$service] = array();
				$map[self::PARTNER_GROUP_ARRAY_NAME][$service][$action] = array();
			}
			else if (!isset($map[self::PARTNER_GROUP_ARRAY_NAME][$service][$action]))
			{
				$map[self::PARTNER_GROUP_ARRAY_NAME][$service][$action] = array();
			}
			
			$map[self::PARTNER_GROUP_ARRAY_NAME][$service][$action] = array_merge($map[self::PARTNER_GROUP_ARRAY_NAME][$service][$action], $partnerGroup);
		}
	}
	
	private static function isEmpty($value)
	{
		if (is_null($value) || $value === '') {
			return true;
		}
		return false;
	}
	
	
	// --------------------
	// -- Public methods --
	// --------------------
	
	/**
	 * Init with allowed permissions for the user in the given KS or kCurrentContext if not KS given
	 * kCurrentContext::init should have been executed before!
	 * @param string $ks KS to extract user and partner IDs from instead of kCurrentContext
	 * @param boolean $useCache use cache or not
	 * @throws TODO: add all exceptions
	 */
	public static function init($useCache = null)
	{
		$securityContext = array(kCurrentContext::$partner_id, kCurrentContext::$ks);
		if ($securityContext === self::$lastInitializedContext) {
			self::$cacheWatcher->apply();
			return;
		}
		
		// verify that kCurrentContext::init has been executed since it must be used to init current context permissions
		if (!kCurrentContext::$ksPartnerUserInitialized) {
			KalturaLog::crit('kCurrentContext::initKsPartnerUser must be executed before initializing kPermissionManager');
			throw new Exception('kCurrentContext has not been initialized!', null);
		}
		
		// can be initialized more than once to support multirequest with different kCurrentContext parameters
		self::$lastInitializedContext = null;
		self::$cacheWatcher = new kApiCacheWatcher();
		self::$useCache = $useCache ? true : false;

		// copy kCurrentContext parameters (kCurrentContext::init should have been executed before)
		self::$requestedPartnerId = !self::isEmpty(kCurrentContext::$partner_id) ? kCurrentContext::$partner_id : null;
		self::$ksPartnerId = !self::isEmpty(kCurrentContext::$ks_partner_id) ? kCurrentContext::$ks_partner_id : null;
		if (self::$ksPartnerId == Partner::ADMIN_CONSOLE_PARTNER_ID && 
			kConf::hasParam('admin_console_partner_allowed_ips'))
		{
			$ipAllowed = false;
			$ipRanges = explode(',', kConf::get('admin_console_partner_allowed_ips'));
			foreach ($ipRanges as $curRange)
			{
				if (kIpAddressUtils::isIpInRange($_SERVER['REMOTE_ADDR'], $curRange))
				{
					$ipAllowed = true;
					break;
				}
			} 
			if (!$ipAllowed)
				throw new kCoreException("Admin console partner used from an unallowed address", kCoreException::PARTNER_BLOCKED);
		}
		self::$ksUserId = !self::isEmpty(kCurrentContext::$ks_uid) ? kCurrentContext::$ks_uid : null;
		if (self::$ksPartnerId != Partner::BATCH_PARTNER_ID)
			self::$kuser = !self::isEmpty(kCurrentContext::getCurrentKsKuser()) ? kCurrentContext::getCurrentKsKuser() : null;
		self::$ksString = kCurrentContext::$ks ? kCurrentContext::$ks : null;
		self::$adminSession = !self::isEmpty(kCurrentContext::$is_admin_session) ? kCurrentContext::$is_admin_session : false;
		
		// if ks defined - check that it is valid
		self::errorIfKsNotValid();
		
		// init partner, user, and role objects
		self::initPartnerUserObjects();

		// throw an error if KS partner (operating partner) is blocked
		self::errorIfPartnerBlocked();
		
		//throw an error if KS user is blocked
		self::errorIfUserBlocked();

		// init role ids
		self::initRoleIds();

		// init permissions map
		self::initPermissionsMap();
								
		// initialization done
		self::$lastInitializedContext = $securityContext;
		self::$cacheWatcher->stop();
		
		return true;
	}
	
	public static function getRoleIds(Partner $operatingPartner = null, kuser $kuser = null)
	{
		$roleIds = null;
		$ksString = kCurrentContext::$ks;
		$isAdminSession = !self::isEmpty(kCurrentContext::$is_admin_session) ? kCurrentContext::$is_admin_session : false;

		if (!$ksString ||
			(!$operatingPartner && kCurrentContext::$ks_partner_id != Partner::BATCH_PARTNER_ID))
		{
			$roleId = UserRolePeer::getIdByStrId (UserRoleId::NO_SESSION_ROLE);
			if($roleId)
				return array($roleId);
				
			return null;
		}

		$ks = ks::fromSecureString($ksString);
		$ksSetRoleId = $ks->getRole();

		if ($ksSetRoleId)
		{
			if ($ksSetRoleId == 'null')
			{
				return null;
			}
			$ksPartnerId = !self::isEmpty(kCurrentContext::$ks_partner_id) ? kCurrentContext::$ks_partner_id : null;
			//check if role exists
			$c = new Criteria();
			$c->addAnd(is_numeric($ksSetRoleId) ? UserRolePeer::ID : UserRolePeer::SYSTEM_NAME
				, $ksSetRoleId, Criteria::EQUAL);
			$partnerIds = array_map('strval', array($ksPartnerId, PartnerPeer::GLOBAL_PARTNER));
			$c->addAnd(UserRolePeer::PARTNER_ID, $partnerIds, Criteria::IN);
			$roleId = UserRolePeer::doSelectOne($c);

			if ($roleId){
				$roleIds = $roleId->getId();
			}else{
				KalturaLog::debug("Role id [$ksSetRoleId] does not exists");
				throw new kCoreException("Unknown role Id [$ksSetRoleId]", kCoreException::ID_NOT_FOUND);
			}
		}

		// if user is defined -> get his role IDs
		if (!$roleIds && $kuser) {
			$roleIds = $kuser->getRoleIds();
		}

		// if user has no defined roles or no user is defined -> get default role IDs according to session type (admin/not)
		if (!$roleIds)
		{
			if (!$operatingPartner)
			{
				// use system default roles
				if ($ks->isWidgetSession()) {
					$strId = UserRoleId::WIDGET_SESSION_ROLE;
				}
				elseif ($isAdminSession) {
					$strId = UserRoleId::PARTNER_ADMIN_ROLE;
				}
				else {
					$strId = UserRoleId::BASE_USER_SESSION_ROLE;
				}

				$roleIds = UserRolePeer::getIdByStrId ($strId);
			}
			else
			{
				if ($ks->isWidgetSession()){
					//there is only one partner widget role defined in the system
					$roleIds = $operatingPartner->getWidgetSessionRoleId();
				}
				elseif ($isAdminSession) {
					// there is only one partner admin role defined in the system
					$roleIds = $operatingPartner->getAdminSessionRoleId();
				}
				else {
					// a partner may have special defined user session roles - get them from partner object
					$roleIds = $operatingPartner->getUserSessionRoleId();
				}
			}
		}

		if ($roleIds) {
			$roleIds = explode(',', trim($roleIds, ','));
		}

		return $roleIds;
	}
	
	private static function initRoleIds()
	{
		self::$roleIds = self::getRoleIds(self::$operatingPartner, self::$kuser);
	}
	
	
	private static function initPartnerUserObjects()
	{
		if (self::$ksPartnerId == Partner::BATCH_PARTNER_ID) {
			self::$operatingPartner = null;
			self::$operatingPartnerId = self::$ksPartnerId;
			return;
		}
		
		$ksPartner = null;
		$requestedPartner = null;
		
		// init ks partner = operating partner
		if (!is_null(self::$ksPartnerId)) {
			$ksPartner = PartnerPeer::retrieveByPK(self::$ksPartnerId);
			if (!$ksPartner)
			{
				KalturaLog::crit('Unknown partner id ['.self::$ksPartnerId.']');
				throw new kCoreException("Unknown partner Id [" . self::$ksPartnerId ."]", kCoreException::ID_NOT_FOUND);
			}
		}
		
		// init requested partner
		if (!is_null(self::$requestedPartnerId)) {
			$requestedPartner = PartnerPeer::retrieveActiveByPK(self::$requestedPartnerId);
			if (!$requestedPartner)
			{
				KalturaLog::crit('Unknown partner id ['.self::$requestedPartnerId.']');
				throw new kCoreException("Unknown partner Id [" . self::$requestedPartnerId ."]", kCoreException::PARTNER_BLOCKED);
			}
		}
		
		// init current kuser
		if (self::$ksUserId && !self::$kuser) { // will never be null because ks::uid is never null
			kuserPeer::setUseCriteriaFilter(false);
			self::$kuser = kuserPeer::getActiveKuserByPartnerAndUid(self::$ksPartnerId, self::$ksUserId);
			kuserPeer::setUseCriteriaFilter(true);
			if (!self::$kuser)
			{
				self::$kuser = null;
				// error not thrown to support adding users 'on-demand'
				// current session will get default role according to session type (user/admin)
			}
		}
		
		// choose operating partner!
		if ($ksPartner) {
			self::$operatingPartner = $ksPartner;
			self::$operatingPartnerId = $ksPartner->getId();
		}
		else if (!self::$ksString && $requestedPartner) {
			self::$operatingPartner = $requestedPartner;
			self::$operatingPartnerId = $requestedPartner->getId();
			self::$kuser = null;
		}
	}
	
	
	
	private static function initPermissionsMap()
	{
		// init an empty map
		self::$map = self::initEmptyMap();
		
		if (!self::$roleIds)
		{
			self::$map = self::getPermissions(null);
		}
		else
		{
			foreach (self::$roleIds as $roleId)
			{
				// init actions and parameters arrays from cache
				$roleMap = self::getPermissions($roleId);
				
				// merge current role map to the global map
				self::$map = array_merge_recursive(self::$map, $roleMap);
			}
		}
	}
	
	// ----------------------------------------------------------------------------
	
	
	
	private static function errorIfKsNotValid()
	{
		// if no ks in current context - no need to check anything
		if (!self::$ksString) {
			return;
		}
		
		$ksObj = null;
		$res = kSessionUtils::validateKSessionNoTicket(self::$ksPartnerId, self::$ksUserId, self::$ksString, $ksObj);

		if ( 0 >= $res )
		{
			switch($res)
			{
				case ks::INVALID_STR:
					KalturaLog::err('Invalid KS ['.self::$ksString.']');
					break;
									
				case ks::INVALID_PARTNER:
					KalturaLog::err('Wrong partner ['.self::$ksPartnerId.'] actual partner ['.$ksObj->partner_id.']');
					break;
									
				case ks::INVALID_USER:
					KalturaLog::err('Wrong user ['.self::$ksUserId.'] actual user ['.$ksObj->user.']');
					break;
																		
				case ks::EXPIRED:
					KalturaLog::err('KS Expired [' . date('Y-m-d H:i:s', $ksObj->valid_until) . ']');
					break;
									
				case ks::LOGOUT:
					KalturaLog::err('KS already logged out');
					break;
				
				case ks::EXCEEDED_ACTIONS_LIMIT:
					KalturaLog::err('KS exceeded number of actions limit');
					break;
					
				case ks::EXCEEDED_RESTRICTED_IP:
					KalturaLog::err('IP does not match KS restriction');
					break;
			}
			
			throw new kCoreException("Invalid KS", kCoreException::INVALID_KS, ks::getErrorStr($res));
		}
	}
	
	
	private static function isPartnerAccessAllowed($service, $action)
	{
		if (is_null(self::$operatingPartnerId) || is_null(self::$requestedPartnerId)) {
			return true;
		}
		
		$partnerGroup = self::getPartnerGroup($service, $action);
		$accessAllowed = myPartnerUtils::allowPartnerAccessPartner ( self::$operatingPartnerId , $partnerGroup , self::$requestedPartnerId );
		if(!$accessAllowed)
			KalturaLog::debug("Operating partner [" . self::$operatingPartnerId . "] not allowed using requested partner [" . self::$requestedPartnerId . "] with partner group [$partnerGroup]");
			
		return $accessAllowed;
	}

	private static function errorIfUserBlocked()
	{
		if (!kCurrentContext::$ks_kuser)
			return;
		$status = kCurrentContext::$ks_kuser->getStatus();
		if ($status == KuserStatus::BLOCKED)
			throw new kCoreException("User blocked", kCoreException::USER_BLOCKED);
	}

	private static function errorIfPartnerBlocked()
	{
		if (!self::$operatingPartner) {
			return;
		}
		
		$partnerStatus = self::$operatingPartner->getStatus();
		
		if($partnerStatus == Partner::PARTNER_STATUS_CONTENT_BLOCK)
		{
		    throw new kCoreException("Partner blocked", kCoreException::PARTNER_BLOCKED);
		}
		if($partnerStatus == Partner::PARTNER_STATUS_FULL_BLOCK)
		{
		    throw new kCoreException("Partner fully blocked", kCoreException::PARTNER_BLOCKED);
		}
	}
	
	/**
	 * Checks if the given service & action is permitted for the current user
	 * @param string $service Service name
	 * @param string $action Action name
	 * @return true if given service->action is accisible by the user or false otherwise
	 */
	public static function isActionPermitted($service, $action)
	{
		self::errorIfNotInitialized();
		
		$service = strtolower($service);
		$action = strtolower($action);
		
		$partnerAccessPermitted = self::isPartnerAccessAllowed($service, $action);
		if(!$partnerAccessPermitted)
		{
			KalturaLog::err("Partner is not allowed");
			return false;
		}
		
		$servicePermitted  = isset(self::$map[self::API_ACTIONS_ARRAY_NAME][$service]);
		if(!$servicePermitted)
		{
			KalturaLog::err("Service is not permitted");
			return false;
		}
		
		$actionPermitted   = isset(self::$map[self::API_ACTIONS_ARRAY_NAME][$service][$action]);
		if(!$actionPermitted)
			KalturaLog::err("Action is not permitted");
		
		return $actionPermitted;
	}
	
	
	private static function getParamPermitted($array_name, $objectName, $paramName)
	{
		self::errorIfNotInitialized();
		
		$objectName = strtolower($objectName);
		$paramName = strtolower($paramName);
		if ( !isset(self::$map[self::API_PARAMETERS_ARRAY_NAME][$array_name][$objectName]) && !isset(self::$map[self::API_PARAMETERS_ARRAY_NAME][ApiParameterPermissionItemAction::USAGE][$objectName]) )
		{
			return false;
		}
		if ($paramName === kApiParameterPermissionItem::ALL_VALUES_IDENTIFIER) {
			return true;
		}
		if (isset(self::$map[self::API_PARAMETERS_ARRAY_NAME][$array_name][$objectName][kApiParameterPermissionItem::ALL_VALUES_IDENTIFIER])) {
			return true;
		}
		return isset(self::$map[self::API_PARAMETERS_ARRAY_NAME][$array_name][$objectName][$paramName]) || isset(self::$map[self::API_PARAMETERS_ARRAY_NAME][ApiParameterPermissionItemAction::USAGE][$objectName][$paramName]);
		
	}
	
	/**
	 * Returns an array of parameter that belong to the object of type $object_name and are readable for the current user.
	 * @param string $object_name
	 * @return array parameter names
	 */
	public static function getReadPermitted($object_name, $param_name)
	{
		return self::getParamPermitted(ApiParameterPermissionItemAction::READ, $object_name, $param_name);
	}
	
	/**
	 * Returns an array of parameter that belong to the object of type $object_name and are insertable for the current user.
	 * @param string $object_name
	 * @return array parameter names
	 */
	public static function getInsertPermitted($object_name, $param_name)
	{
		return self::getParamPermitted(ApiParameterPermissionItemAction::INSERT, $object_name, $param_name);
	}
	
	/**
	 * Returns an array of parameter that belong to the object of type $object_name and are updatable for the current user.
	 * @param string $object_name
	 * @return array parameter names
	 */
	public static function getUpdatePermitted($object_name, $param_name)
	{
		return self::getParamPermitted(ApiParameterPermissionItemAction::UPDATE, $object_name, $param_name);
	}
	
	/**
	 * Returns an array of parameter that belong to the object of type $object_name and are useable for the current user.
	 * @param string $object_name
	 * @return array parameter names
	 */
	public static function getUsagePermitted($object_name, $param_name)
	{
		return self::getParamPermitted(ApiParameterPermissionItemAction::USAGE, $object_name, $param_name);
	}
	
	/**
	 * @param string $service
	 * @param string $action
	 * @return allowed partner group for the given service and action for the current user
	 */
	public static function getPartnerGroup($service, $action)
	{
		self::errorIfNotInitialized();
		
		$service = strtolower($service); //TODO: save service with normal case ?
		$action = strtolower($action); //TODO: save actions with normal case ?
		
		$partnerGroupSet   = isset(self::$map[self::PARTNER_GROUP_ARRAY_NAME][$service]) &&isset(self::$map[self::PARTNER_GROUP_ARRAY_NAME][$service][$action]);
		
		if (!$partnerGroupSet)
		{
			return self::$operatingPartnerId;
		}
		
		$partnerGroup =  self::$map[self::PARTNER_GROUP_ARRAY_NAME][$service][$action];
		$partnerGroup[] = self::$operatingPartnerId;
		
		if (in_array(myPartnerUtils::ALL_PARTNERS_WILD_CHAR, $partnerGroup, true))
		{
			if (self::$requestedPartnerId && self::$requestedPartnerId != self::$operatingPartnerId)
				return self::$requestedPartnerId;
				
			return myPartnerUtils::ALL_PARTNERS_WILD_CHAR;
		}
		
		$partnerGroup = array_filter($partnerGroup);
		if (self::$requestedPartnerId && self::$requestedPartnerId != self::$operatingPartnerId && in_array(self::$requestedPartnerId, $partnerGroup))
			return self::$requestedPartnerId;
		
		$partnerGroup = implode(',', $partnerGroup);
		return $partnerGroup;
	}
	
	/**
	 * @return array current role ids
	 */
	public static function getCurrentRoleIds()
	{
		return self::$roleIds;
	}
	
	/**
	 * @return return current permission names
	 */
	public static function getCurrentPermissions()
	{
		return self::$map[self::PERMISSION_NAMES_ARRAY];
	}
	
	/**
	 * @return boolean
	 */
	public static function isPermitted($permissionName)
	{
		return isset(self::$map[self::PERMISSION_NAMES_ARRAY][$permissionName]);
	}
		
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if($object instanceof Permission && $object->getPartnerId() != PartnerPeer::GLOBAL_PARTNER)
			return true;
		
		if ($object instanceof UserRole && $object->getPartnerId() != PartnerPeer::GLOBAL_PARTNER &&
			     (in_array(UserRolePeer::PERMISSION_NAMES, $modifiedColumns) || in_array(UserRolePeer::STATUS, $modifiedColumns))    )
			return true;
			
		if ($object instanceof PermissionToPermissionItem)
			return true;
			
		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		if($object instanceof Permission && $object->getPartnerId() != PartnerPeer::GLOBAL_PARTNER)
		{
			self::markPartnerRoleCacheDirty($object->getPartnerId());
			return true;
		}
		
		if ($object instanceof UserRole && $object->getPartnerId() != PartnerPeer::GLOBAL_PARTNER &&
			     (in_array(UserRolePeer::PERMISSION_NAMES, $modifiedColumns) || in_array(UserRolePeer::STATUS, $modifiedColumns))    )
		{
			self::markPartnerRoleCacheDirty($object->getPartnerId());
			return true;
		}
		
		if ($object instanceof PermissionToPermissionItem)
		{
			$permission = $object->getPermission();
			if ($permission && $permission->getPartnerId() != PartnerPeer::GLOBAL_PARTNER)
			{
				self::markPartnerRoleCacheDirty($permission->getPartnerId());
				return true;
			}
		}
		
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::shouldConsumeCreatedEvent()
	 */
	public function shouldConsumeCreatedEvent(BaseObject $object)
	{
		if($object instanceof Permission)
			return true;
		
		if ($object instanceof PermissionToPermissionItem)
			return true;
		
		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::objectCreated()
	 */
	public function objectCreated(BaseObject $object)
	{
		if($object instanceof Permission)
		{
			// changes in permissions for partner, may require new cache generation
			if ($object->getPartnerId() != PartnerPeer::GLOBAL_PARTNER)
			{
				self::markPartnerRoleCacheDirty($object->getPartnerId());
				return true;
			}
		}
		
		if ($object instanceof PermissionToPermissionItem)
		{
			$permission = $object->getPermission();
			if ($permission && $permission->getPartnerId() != PartnerPeer::GLOBAL_PARTNER)
			{
				self::markPartnerRoleCacheDirty($permission->getPartnerId());
				return true;
			}
		}
		
		return true;
	}
	
	private static function markPartnerRoleCacheDirty($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if (!$partner) {
			KalturaLog::err("Cannot find partner with id [$partnerId]");
			return;
		}
		$partner->setRoleCacheDirtyAt(time());
		$partner->save();
		PartnerPeer::removePartnerFromCache($partnerId);
	}
	
	/**
	 *
	 * add ps2 permission for given partner
	 * @param Partner $partner
	 */
	public static function setPs2Permission(Partner $partner)
 	{
 		$ps2Permission = new Permission();
 		$ps2Permission->setName(PermissionName::FEATURE_PS2_PERMISSIONS_VALIDATION);
 		$ps2Permission->setPartnerId($partner->getId());
 		$ps2Permission->setStatus(PermissionStatus::ACTIVE);
 		$ps2Permission->setType(PermissionType::SPECIAL_FEATURE);
 		$ps2Permission->save();
 	}
 	
/**
	 *
	 * add ps2 permission for given partner
	 * @param Partner $partner
	 */
	public static function sePermissionForPartner(Partner $partner, $permission)
 	{
 		$ps2Permission = new Permission();
 		$ps2Permission->setName($permission);
 		$ps2Permission->setPartnerId($partner->getId());
 		$ps2Permission->setStatus(PermissionStatus::ACTIVE);
 		$ps2Permission->setType(PermissionType::SPECIAL_FEATURE);
 		$ps2Permission->save();
 	}
	
}
