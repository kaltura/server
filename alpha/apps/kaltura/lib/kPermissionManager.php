<?php

class kPermissionManager
{
	// -------------------
	// -- Class members --
	// -------------------
		
	const GLOBAL_CACHE_KEY_PREFIX = 'kPermissionManager_'; // Prefix added for all key names stored in the APC cache
	
	private static $map = array(); // Local map of permission items allowed for the current role
	
	const API_ACTIONS_ARRAY_NAME    = 'api_actions';    // name of $map's api actions array
	const API_PARAMETERS_ARRAY_NAME = 'api_parameters'; // name of $map's api parameters array
	const PARTNER_GROUP_ARRAY_NAME  = 'partner_group';  // name of $map's partner group array
			
	private static $initialized = false; // map already initialized or not
	private static $useCache = true;     // use cache or not
	
	private static $ksUserId = null;
	private static $adminSession = false; // is admin session
	private static $ksPartnerId = null;
	private static $requestedPartnerId = null;
	private static $ksString = null;
	private static $roleIds = null;
	private static $operatingPartnerId = null;
	
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
		if (self::$useCache && function_exists('apc_fetch') && function_exists('apc_store'))
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
		$key = 'role_'.$roleId.'_partner_'.$partnerId;
		return $key;
	}
	
	/**
	 * Get value from cache for the given key
	 * @param string $key
	 */
	private static function getFromCache($key)
	{
		if (!self::useCache())
		{
			return null;
		}
		$key = self::GLOBAL_CACHE_KEY_PREFIX.$key; // add prefix to given key
		$value = apc_fetch($key); // try to fetch from cache
		if ($value) {
			KalturaLog::debug("Found an APC cache value for key [$key]");
			$value = unserialize($value);
			return $value;
		}
		else {
			KalturaLog::debug("No APC cache value found for key [$key]");
			return null;
		}
	}
	
	/**
	 * 
	 * Store given value in cache for with the given key as an identifier
	 * @param string $key
	 * @param string $value
	 */
	private static function storeInCache($key, $value)
	{
		if (!self::useCache())
		{
			return false;
		}
		$key = self::GLOBAL_CACHE_KEY_PREFIX.$key; // add prefix to given key
		$value = serialize($value);
		$success = apc_store($key, $value); // try to store in cache

		if ($success)
		{
			KalturaLog::debug("New value stored in APC cache for key [$key]");
			return true;
		}
		else
		{
			KalturaLog::debug("No APC cache value stored for key [$key]");
			return false;
		}
	}
	
	
	/**
	 * Delete a value stored in APC cache with the given key
	 * @param string $key stored key
	 */
	private static function deleteFromCache($key)
	{
		if (!self::useCache())
		{
			return null;
		}
		$key = self::GLOBAL_CACHE_KEY_PREFIX.$key; // add prefix to given key
		
		$success = apc_delete($key);
		if ($success) {
			KalturaLog::debug("Successfully deleted stored APC cache value for key [$key]");
			return true;
		}
		else
		{
			KalturaLog::debug("Cannot delete APC cache value for key [$key]");
			return false;
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
		if (!self::$initialized)
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
		return $map;
	}
	
	
	private static function getPermissions($roleId)
	{
		$map = self::initEmptyMap();
		
		// get role from DB
		$dbRole = null;
		$roleUpdatedAt = null;
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
			$roleUpdatedAt = $dbRole->getUpdatedAt();
		}
		
		// get role from cache
		$roleCacheKey = self::getRoleIdKey($roleId, self::$operatingPartnerId);
		$cacheRole = self::getFromCache($roleCacheKey);
		
		// compare updatedAt between DB and cache
		if ( ($cacheRole && isset($cacheRole['updatedAt']) && $cacheRole['updatedAt'] >= $roleUpdatedAt )
			  || ($cacheRole && !$dbRole) ) // role == null but saved in cache
		{
			// cache is updated - init from cache
			unset($cacheRole['updatedAt']);
			$map = $cacheRole;
			return $map; // initialization from cache finished
		}
		
		// cache is not updated - delete stored value and re-init from DB
		self::deleteFromCache($roleCacheKey);
		$map = self::getPermissionsFromDb($dbRole);
		
		// update cache
		$cacheRole = $map;
		$cacheRole['updatedAt'] = $roleUpdatedAt;
		self::storeInCache($roleCacheKey, $cacheRole);
		
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
			$permissionNames = $dbRole->getPermissionNames(true);
			$permissionNames = array_map('trim', explode(',', $permissionNames));
		}
		else {
			$permissionNames = array();
		}
		
		// add always allowed permissions
		if (self::$operatingPartner) {
			$alwaysAllowed = self::$operatingPartner->getAlwaysAllowedPermissionNames();
			$alwaysAllowed = array_map('trim', explode(',', $alwaysAllowed));
		}
		else {
			$alwaysAllowed = array(PermissionName::ALWAYS_ALLOWED_ACTIONS);
		}
		$permissionNames = array_merge($permissionNames, $alwaysAllowed);
				
		// get mapping of permissions to permission items
		$c = new Criteria();
		$c->addAnd(PermissionToPermissionItemPeer::PERMISSION_NAME, $permissionNames, Criteria::IN);
		$lookups = PermissionToPermissionItemPeer::doSelectJoinAll($c);
		foreach ($lookups as $lookup)
		{
			$item       = $lookup->getPermissionItem();
			$permission = $lookup->getPermission();
			
			if (!$item)	{
				KalturaLog::err('PermissionToPermissionItem id ['.$lookup->getId().'] is defined with PermissionItem id ['.$lookup->getPermissionItemId().'] which does not exist!');
				continue;
			}
			
			if (!$permission) {
				KalturaLog::err('PermissionToPermissionItem id ['.$lookup->getId().'] is defined with Permission name ['.$lookup->getPermissionName().'] which does not exist!');
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
		if (!isset($map[self::API_ACTIONS_ARRAY_NAME][$item->getService()])) {
			$map[self::API_ACTIONS_ARRAY_NAME][$item->getService()] = array();
			$map[self::API_ACTIONS_ARRAY_NAME][$item->getService()][$item->getAction()] = array();		
		}
		else if (!in_array($item->getAction(), $map[self::API_ACTIONS_ARRAY_NAME][$item->getService()])) {
			$map[self::API_ACTIONS_ARRAY_NAME][$item->getService()][$item->getAction()] = array();
		}
	}
	
	
	/**
	 * Add an api parameter permission to the local map
	 * @param array $map map to fill
	 * @param kApiParameterPermissionItem $item
	 */
	private static function addApiParameter(array &$map, kApiParameterPermissionItem $item)
	{
		$itemAction = $item->getAction(); // ApiParameterPermissionItemAction
		if (!isset($map[self::API_PARAMETERS_ARRAY_NAME][$itemAction][$item->getObject()])) {
			$map[self::API_PARAMETERS_ARRAY_NAME][$itemAction][$item->getObject()] = array();
		}
		$map[self::API_PARAMETERS_ARRAY_NAME][$itemAction][$item->getObject()][] = $item->getParameter();
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
			$service = $item->getService();
			$action  = $item->getAction();
			
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
		// verify that kCurrentContext::init has been executed since it must be used to init current context permissions
		if (!kCurrentContext::$ksPartnerUserInitialized) {
			KalturaLog::crit('kCurrentContext::initKsPartnerUser must be executed before initializing kPermissionManager');
			throw new Exception('kCurrentContext has not been initialized!', null);
		}
		
		// can be initialized more than once to support multirequest with different kCurrentContext parameters
		self::$initialized = false;		
		self::$useCache = $useCache ? true : false;

		// copy kCurrentContext parameters (kCurrentContext::init should have been executed before)
		self::$requestedPartnerId = !self::isEmpty(kCurrentContext::$partner_id) ? kCurrentContext::$partner_id : null;
		self::$ksPartnerId = !self::isEmpty(kCurrentContext::$ks_partner_id) ? kCurrentContext::$ks_partner_id : null;
		self::$ksUserId = !self::isEmpty(kCurrentContext::$ks_uid) ? kCurrentContext::$ks_uid : null;
		self::$ksString = kCurrentContext::$ks ? kCurrentContext::$ks : null;
		self::$adminSession = !self::isEmpty(kCurrentContext::$is_admin_session) ? kCurrentContext::$is_admin_session : false;
			
		// clear instance pools
		UserRolePeer::clearInstancePool();
		PermissionPeer::clearInstancePool();
		PermissionItemPeer::clearInstancePool();
		PermissionToPermissionItemPeer::clearInstancePool();
		kuserPeer::clearInstancePool();
		
		// if ks defined - check that it is valid
		self::errorIfKsNotValid();
		
		// init partner, user, and role objects
		self::initPartnerUserObjects();

		// throw an error if KS partner (operating partner) is blocked
		self::errorIfPartnerBlocked();
		
		// init role ids
		self::initRoleIds();

		// init permissions map
		self::initPermissionsMap();
								
		// initialization done
		self::$initialized = true;
		return true;
	}
	
	
	
	private static function initRoleIds()
	{
		$roleIds = null;
		if (!self::$operatingPartner || !self::$ksString)
		{
			// no partner or session -> no role
			$roleIds = null;
		}
		else
		{
			// if user is defined -> get his role IDs
			if (self::$kuser) {
				$roleIds = self::$kuser->getRoleIds();
			}
			
			// if user has no defined roles or no user is defined -> get default role IDs according to session type (admin/not)
			if (!$roleIds)
			{
				if (self::$adminSession) {
					// there is only one partner admin role defined in the system
					$roleIds = self::$operatingPartner->getAdminSessionRoleId();
				}
				else {
					// a partner may have special defined user session roles - get them from partner object
					$roleIds = self::$operatingPartner->getUserSessionRoleId();
				}
			}
			
			if ($roleIds) {
				$roleIds = explode(',', trim($roleIds, ','));
			}
		}
		
		self::$roleIds = $roleIds;		
	}
	
	
	private static function initPartnerUserObjects()
	{
		$ksPartner = null;
		$requestedPartner = null;
		
		// init ks partner = operating partner
		if (!is_null(self::$ksPartnerId)) {
			$ksPartner = PartnerPeer::retrieveByPK(self::$ksPartnerId);
			if (!$ksPartner)
			{
				KalturaLog::crit('Unknown partner id ['.self::$ksPartnerId.']');
				throw new KalturaAPIException ( APIErrors::UNKNOWN_PARTNER_ID ,self::$ksPartnerId);
			}
		}
		
		// init requested partner
		if (!is_null(self::$requestedPartnerId)) {
			$requestedPartner = PartnerPeer::retrieveByPK(self::$requestedPartnerId);
			if (!$requestedPartner)
			{
				KalturaLog::crit('Unknown partner id ['.self::$requestedPartnerId.']');
				throw new KalturaAPIException ( APIErrors::UNKNOWN_PARTNER_ID ,self::$requestedPartnerId);
			}
		}
		
		// init current kuser
		if (self::$ksUserId) { // will never be null because ks::uid is never null
			self::$kuser = kuserPeer::getKuserByPartnerAndUid(self::$ksPartnerId, self::$ksUserId);
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
			}
			throw new KalturaAPIException (APIErrors::INVALID_KS ,self::$ksString ,$res ,ks::getErrorStr($res));
		}
	}
	
	
	private static function isPartnerAccessAllowed($service, $action)
	{		
		if (!self::$operatingPartnerId || !self::$requestedPartnerId) {
			return true;
		}
		
		$accessAllowed = myPartnerUtils::allowPartnerAccessPartner ( self::$operatingPartnerId , self::getPartnerGroup($service, $action) , self::$requestedPartnerId );
		return $accessAllowed;
	}
	
	
	private static function errorIfPartnerBlocked()
	{
		if (!self::$operatingPartner) {
			return;
		}
		
		$partnerStatus = self::$operatingPartner->getStatus();
		
		if($partnerStatus == Partner::PARTNER_STATUS_CONTENT_BLOCK)
		{
			throw new KalturaAPIException (APIErrors::SERVICE_FORBIDDEN_CONTENT_BLOCKED);
		}
		if($partnerStatus == Partner::PARTNER_STATUS_FULL_BLOCK)
		{
			throw new KalturaAPIException (APIErrors::SERVICE_FORBIDDEN_FULLY_BLOCKED);
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
		
		$service = strtolower($service); //TODO: save service with normal case ?
		$action = strtolower($action); //TODO: save actions with normal case ?	
		$partnerAccessPermitted = self::isPartnerAccessAllowed($service, $action);
		$servicePermitted  = $partnerAccessPermitted && isset(self::$map[self::API_ACTIONS_ARRAY_NAME][$service]);
		$actionPermitted   = $servicePermitted && isset(self::$map[self::API_ACTIONS_ARRAY_NAME][$service][$action]);
		return $actionPermitted;
	}
	
	
	private static function getParamPermitted($array_name, $object_name, $param_name)
	{
		self::errorIfNotInitialized();
		
		if (!isset(self::$map[self::API_PARAMETERS_ARRAY_NAME][$array_name][$object_name]))
		{
			return false;
		}
		if ($param_name === kApiParameterPermissionItem::ALL_VALUES_IDENTIFIER) {
			return true;
		}
		if (in_array(kApiParameterPermissionItem::ALL_VALUES_IDENTIFIER, self::$map[self::API_PARAMETERS_ARRAY_NAME][$array_name][$object_name])) {
			return true;
		}
		return in_array($param_name, self::$map[self::API_PARAMETERS_ARRAY_NAME][$array_name][$object_name]);
		
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
	 * @param string $service
	 * @param string $action
	 * @return allowed partner group for the given service and action for the current user
	 */
	public static function getPartnerGroup($service, $action)
	{
		self::errorIfNotInitialized();
		
		$service = strtolower($service); //TODO: save service with normal case ?
		$action = strtolower($action); //TODO: save actions with normal case ?
		
		if (self::$requestedPartnerId && self::$requestedPartnerId != self::$operatingPartnerId)
		{
			return self::$requestedPartnerId;
		}
		
		$partnerGroupSet   = isset(self::$map[self::PARTNER_GROUP_ARRAY_NAME][$service]) &&isset(self::$map[self::PARTNER_GROUP_ARRAY_NAME][$service][$action]);
		
		if (!$partnerGroupSet)
		{
			return self::$operatingPartnerId;
		}
		
		$partnerGroup =  self::$map[self::PARTNER_GROUP_ARRAY_NAME][$service][$action];
		$partnerGroup[] = self::$operatingPartnerId;
		
		if (in_array(myPartnerUtils::ALL_PARTNERS_WILD_CHAR, $partnerGroup))
		{
			return myPartnerUtils::ALL_PARTNERS_WILD_CHAR;
		}
		
		$partnerGroup = array_filter($partnerGroup);
		$partnerGroup = implode(',', $partnerGroup);
		return $partnerGroup;
	}
		
}