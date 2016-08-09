<?php


/**
 * Skeleton subclass for performing query and update operations on the 'permission' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class PermissionPeer extends BasePermissionPeer
{
	protected static $allowedPermissions = array();
	
	public static function checkValidPermissionsForRole($permissionsStr, $partnerId)
	{
		if ($permissionsStr == UserRole::ALL_PARTNER_PERMISSIONS_WILDCARD) {
			return true;
		}
		
		$permissions = array_map('trim', explode(',', $permissionsStr));
		
		foreach ($permissions as $permission)
		{
			if (!$permission)
				continue;
			
			$c = new Criteria();
			$c->addAnd(PermissionPeer::NAME, $permission, Criteria::EQUAL);
			$c->addAnd(PermissionPeer::PARTNER_ID, array(strval($partnerId), strval(PartnerPeer::GLOBAL_PARTNER)), Criteria::IN);
			// a user role can only contain NORMAL permission types - TODO should be changed to perPartner / perUser permissions
			$c->addAnd(PermissionPeer::TYPE, PermissionType::NORMAL, Criteria::EQUAL);
			$c->addAnd(PermissionPeer::STATUS, PermissionStatus::ACTIVE, Criteria::EQUAL);
			
			PermissionPeer::setUseCriteriaFilter(false);
			$hasPermission = PermissionPeer::doSelectOne($c);
			PermissionPeer::setUseCriteriaFilter(true);
			
			if (!$hasPermission || $hasPermission->getStatus() == PermissionStatus::DELETED) {
				throw new kPermissionException('Permission ['.$permission.'] was not found for partner ['.$partnerId.']', kPermissionException::PERMISSION_NOT_FOUND);
			}
		}
	}
			
	public static function addToPartner($permission, $partnerId, $checkGlobalPartner = true)
	{
		$permission->setPartnerId($partnerId);
		$c = new Criteria();
		if ($checkGlobalPartner) {
			$c->addAnd(PermissionPeer::PARTNER_ID, array(strval($partnerId), strval(PartnerPeer::GLOBAL_PARTNER)), Criteria::IN);
			$c->addDescendingOrderByColumn(PermissionPeer::PARTNER_ID);
		}
		else {
			$c->addAnd(PermissionPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
		}
		$c->addAnd(PermissionPeer::NAME, $permission->getName(), Criteria::EQUAL);
		$existingPermission = PermissionPeer::doSelectOne($c);
		if (!$existingPermission) {
			$permission->save();
			KalturaLog::log('Adding permission ['.$permission->getName().'] to partner ['.$partnerId.'].');
			return $permission;
		}
		else {
			throw new kPermissionException('Permission ['.$permission->getName().'] already exists for partner ['.$partnerId.']', kPermissionException::PERMISSION_ALREADY_EXISTS);
		}
	}
	
	public static function setDefaultCriteriaFilter ()
	{
		if(is_null(self::$s_criteria_filter))
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = new Criteria();
		$c->add(self::STATUS, PermissionStatus::DELETED,Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter($c);
	}
	
	
	public static function removePermissionFromPartner($permissionName, $partnerId)
	{
		$c = new Criteria();
		$c->addAnd(PermissionPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
		$c->addAnd(PermissionPeer::NAME, $permissionName, Criteria::EQUAL);
		$existingPermission = PermissionPeer::doSelectOne($c);
		if (!$existingPermission) {
			throw new kPermissionException('Permission ['.$permissionName.'] does not exist for partner ['.$partnerId.']', kPermissionException::PERMISSION_NOT_FOUND);
		}
		KalturaLog::log('Removing permission ['.$permissionName.'] from partner ['.$partnerId.'].');
		$existingPermission->setStatus(PermissionStatus::DELETED);
	}
	
	
	public static function enableForPartner($permissionName, $permissionType, $partnerId = null, $friendlyName = null, $description = null)
	{
		if(is_null($partnerId))
			$partnerId = (kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id);
			
		$permission = new Permission();
		$permission->setName($permissionName);
		$permission->setFriendlyName($friendlyName ? $friendlyName : $permissionName);
		$permission->setDescription($description);
		$permission->setType($permissionType);
		$permission->setStatus(PermissionStatus::ACTIVE);
		
		try {
			// try to add permission
			self::addToPartner($permission, $partnerId, false);
			return true;
		}
		catch (kPermissionException $e) {
			$code = $e->getCode();
			if ($code == kPermissionException::PERMISSION_ALREADY_EXISTS) {
				// permission already exists - set status to active
				$permission = self::getByNameAndPartner($permissionName, array($partnerId));
				if(!$permission)
					throw new kCoreException("Permission [$permissionName] not found for partner [$partnerId]", kCoreException::INTERNAL_SERVER_ERROR);
					
				$permission->setStatus(PermissionStatus::ACTIVE);
				$permission->save();
				return true;
			}
			throw $e;
		}
		throw new kCoreException('Unknown error occured', kCoreException::INTERNAL_SERVER_ERROR);
	}

	
	public static function disableForPartner($permissionName, $partnerId)
	{
		$permission = self::getByNameAndPartner($permissionName, array($partnerId));
		if (!$permission) {
			return true; // permission not found - already disabled
		}
		if ($permission->getStatus() != PermissionStatus::ACTIVE) {
			return true; // non active status - already disabled
		}
		$permission->setStatus(PermissionStatus::BLOCKED);
		$permission->save();
	}
	
	
	public static function isValidForPartner($permissionName, $partnerId, $checkDependency = true)
	{
		if(!isset(self::$allowedPermissions[$partnerId]))
		{
			self::$allowedPermissions[$partnerId] = array();
		}
		elseif(isset(self::$allowedPermissions[$partnerId][$permissionName]))
		{
			return self::$allowedPermissions[$partnerId][$permissionName];
		}
			
		$permission = self::getByNameAndPartner($permissionName, array($partnerId, PartnerPeer::GLOBAL_PARTNER));
		return self::validatePermission($permissionName, $partnerId, $checkDependency, $permission);
	}


	private static function validatePermission($permissionName ,$partnerId, $checkDependency, $permission)
	{
		if (!$permission) {
			self::$allowedPermissions[$partnerId][$permissionName] = false;
			return false;
		}
		if ($permission->getStatus() != PermissionStatus::ACTIVE) {
			self::$allowedPermissions[$partnerId][$permissionName] = false;
			return false;
		}
		
		// check if permissions depends on another permission which is not valid for partner
		if ($checkDependency)
		{
			$dependsOn = trim($permission->getDependsOnPermissionNames());
			$dependsOn = explode(',', $dependsOn);
			$valid = true;
			if ($dependsOn) {
				foreach($dependsOn as $dependPermission) {
					$dependPermission = trim($dependPermission);
					if (!$dependPermission) {
						continue;
					}
					$valid = $valid && self::isValidForPartner($dependPermission, $partnerId);
				}
			}
			if (!$valid) {
				self::$allowedPermissions[$partnerId][$permissionName] = false;
				return false;
			}
		}
		self::$allowedPermissions[$partnerId][$permissionName] = $permission;

		return $permission;
	}
	
	
	public static function getByNameAndPartner($permissionName, $partnerIdsArray)
	{
		$c = new Criteria();
		
		if(!is_array($partnerIdsArray))
			$partnerIdsArray = array($partnerIdsArray);
			
		if(!in_array('*', $partnerIdsArray, true))
		{
			$partnerIdsArray = array_map('strval', $partnerIdsArray);
			$c->addAnd(PermissionPeer::PARTNER_ID, $partnerIdsArray, Criteria::IN);
		}
		$c->addAnd(PermissionPeer::NAME, $permissionName, Criteria::EQUAL);
		$c->addAscendingOrderByColumn(PermissionPeer::STATUS); // needed in case the permission appears more than once
		PermissionPeer::setUseCriteriaFilter(false);
		$permission = PermissionPeer::doSelectOne($c);
		PermissionPeer::setUseCriteriaFilter(true);
		return $permission;
	}

	public static function getByNamesAndPartner(array $permissionNamesArray, array $partnerIdsArray)
	{
		$c = new Criteria();

		$c->addAnd(PermissionPeer::PARTNER_ID, $partnerIdsArray, Criteria::IN);
		$c->addAnd(PermissionPeer::NAME, $permissionNamesArray, Criteria::IN);
		$c->addAnd(PermissionPeer::STATUS, PermissionStatus::ACTIVE, Criteria::EQUAL);
		$c->addGroupByColumn(PermissionPeer::NAME);

		PermissionPeer::setUseCriteriaFilter(false);
		$permissions = PermissionPeer::doSelect($c);
		PermissionPeer::setUseCriteriaFilter(true);

		return $permissions;
	}

	public static function preFetchPermissions($permissionsNamesArray)
	{
		$preFetchPermissions = PermissionPeer::getByNamesAndPartner($permissionsNamesArray , array(kCurrentContext::$ks_partner_id, PartnerPeer::GLOBAL_PARTNER));

		foreach ($preFetchPermissions as $permission)
		{
			PermissionPeer::validatePermission($permission->getName(), kCurrentContext::$ks_partner_id, true ,$permission);
		}
	}
	
	public static function isAllowedPlugin($pluginName, $partnerId)
	{
		$permissionName = self::getPermissionNameFromPluginName($pluginName);
		return self::isValidForPartner($permissionName, $partnerId);
	}
	
	public static function enablePlugin($pluginName, $partnerId)
	{
		$permissionName = self::getPermissionNameFromPluginName($pluginName);
		$friendlyName = $pluginName .' plugin permission';
		$description = 'Permission to use '.$pluginName.' plugin';
		return self::enableForPartner($permissionName, PermissionType::PLUGIN, $partnerId, $friendlyName, $description);
	}
	
	public static function disablePlugin($pluginName, $partnerId)
	{
		$permissionName = self::getPermissionNameFromPluginName($pluginName);
		return self::disableForPartner($permissionName, $partnerId);
	}
	
	public static function getPermissionNameFromPluginName($pluginName)
	{
		return strtoupper($pluginName).'_PLUGIN_PERMISSION';
	}
	
	public static function getAllValidForPartner($partnerId, $checkDependency = true)
	{
		$c = new Criteria();
		$c->addAnd(PermissionPeer::PARTNER_ID, array(strval($partnerId), strval(PartnerPeer::GLOBAL_PARTNER)), Criteria::IN);
		$c->addAnd(PermissionPeer::STATUS, PermissionStatus::ACTIVE, Criteria::EQUAL);
		PermissionPeer::setUseCriteriaFilter(false);
		$allPermissions = PermissionPeer::doSelect($c);
		PermissionPeer::setUseCriteriaFilter(true);
		
		if ($checkDependency) {
			$allPermissions = self::filterDependencies($allPermissions, $partnerId);
		}
				
		return $allPermissions;
	}
	
	
	public static function filterDependenciesByNames($permissionNames, $partnerId)
	{
		$c = new Criteria();
		$c->addAnd(PermissionPeer::NAME, explode(',', $permissionNames), Criteria::IN);
		$c->addAnd(PermissionPeer::PARTNER_ID, array(strval($partnerId), strval(PartnerPeer::GLOBAL_PARTNER)), Criteria::IN);
		PermissionPeer::setUseCriteriaFilter(false);
		$permissionObjects = PermissionPeer::doSelect($c);
		PermissionPeer::setUseCriteriaFilter(true);
		$permissionObjects = PermissionPeer::filterDependencies($permissionObjects, $partnerId);
		$permissionNames = array();
		foreach ($permissionObjects as $object)
		{
			$permissionNames[] = $object->getName();
		}
		$permissionNames = implode(',', $permissionNames);
		return $permissionNames;
	}
	
	public static function filterDependencies($permissions, $partnerId)
	{
		$c = new Criteria();
		$c->addAnd(PermissionPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
		$c->addAnd(PermissionPeer::TYPE, array(PermissionType::PLUGIN, PermissionType::SPECIAL_FEATURE), Criteria::IN);
		$c->addAnd(PermissionPeer::STATUS, PermissionStatus::ACTIVE, Criteria::EQUAL);
		$c->addSelectColumn(PermissionPeer::NAME);
		$stmt = PermissionPeer::doSelectStmt($c);
		$additionalPartnerPermissionNames = $stmt->fetchAll(PDO::FETCH_COLUMN);
	
		
		$checkDependency = true;
		while ($checkDependency)
		{
			$checkDependency = false;
			$permissionNames = array();
			foreach ($permissions as $permission)
			{
				// create an array of permission names to assist the check
				$permissionNames[$permission->getId()] = $permission->getName();
			}
			foreach ($permissions as $key => $permission)
			{
				$dependsOn = trim($permission->getDependsOnPermissionNames());
				$dependsOn = explode(',', $dependsOn);
				if ($dependsOn)
				{
					foreach($dependsOn as $dependPermission)
					{
						$dependPermission = trim($dependPermission);
						if (!$dependPermission) {
							// invalid text
							continue;
						}
						if (!in_array($dependPermission, $permissionNames, true) && !in_array($dependPermission, $additionalPartnerPermissionNames, true)) {
							// current permission depends on a non existing permission
							unset($permissions[$key]);
							$checkDependency = true; // need to recheck because we have delete a permission
							break;
						}
					}
				}
			}
		}
		return $permissions;
	}

	/**
	 * Retrieve partner level permissions
	 *
	 * @param      int $partnerId
	 * @param      PropelPDO $con the connection to use
	 * @return     array<Permission>
	 */
	public static function retrievePartnerLevelPermissions($partnerId, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(PermissionPeer::PARTNER_ID, $partnerId);
		$criteria->add(PermissionPeer::STATUS, PermissionStatus::ACTIVE);
		$criteria->add(PermissionPeer::TYPE, array(PermissionType::PLUGIN, PermissionType::SPECIAL_FEATURE), Criteria::IN);

		return PermissionPeer::doSelect($criteria, $con);
	}
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("permission:partnerId=%s", self::PARTNER_ID));		
	}
} // PermissionPeer

