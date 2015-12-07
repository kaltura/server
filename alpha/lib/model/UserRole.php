<?php


/**
 * Skeleton subclass for representing a row from the 'user_role' table.
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
class UserRole extends BaseUserRole implements IRelatedObject
{
	const ALL_PARTNER_PERMISSIONS_WILDCARD = '*';
	
	/**
	 * Copy current role to the given partner.
	 * @param int $partnerId
	 */
	public function copyToPartner($partnerId)
	{
		$newRole = new UserRole();
		$newRole->setName($this->getName());
		$newRole->setDescription($this->getDescription());
		$newRole->setStatus($this->getStatus());
		$newRole->setPermissionNames(parent::getPermissionNames());
		$newRole->setCustomData($this->getCustomData());
		$newRole->setPartnerId($partnerId); // set new partner id
		$newRole->setTags($this->getTags());
		return $newRole;
	}
	
	
	public function setPermissionNames($permissionNames)
	{
		if(!$this->isNew() && parent::getPermissionNames() == '*')
			return;
			
		$permissionNames = array_map('trim', explode(',', $permissionNames));
		$permissionNames = implode(',', $permissionNames);
		parent::setPermissionNames($permissionNames);
	}
	
	
	/**
	 * Get the [permission_names] column value.
	 * If set to self::ALL_PARTNER_PERMISSIONS_WILDCARD (*), return all permisisons relevant for the partner.
	 * @var bool $filterDependencies true if should filter permissions which are set for partner but not valid due to dependencies on other permissions which are missing for the partner
	 * @return     string
	 */
	public function getPermissionNames($filterDependencies = false, $skipTranslateWildcard = false)
	{
		// get from DB
		$permissionNames = parent::getPermissionNames();
		$permissionNames = array_map('trim', explode(',', $permissionNames));
		
		$currentPartnerId = kCurrentContext::$ks_partner_id;
		if (is_null($currentPartnerId) || $currentPartnerId === '') {
			$currentPartnerId = kCurrentContext::$partner_id;
		}
		
		// translate * to permission names of all permissions valid for partner
		if (in_array(self::ALL_PARTNER_PERMISSIONS_WILDCARD, $permissionNames) && !$skipTranslateWildcard)
		{
			$permissionNames = array();
			$permissions = PermissionPeer::getAllValidForPartner($currentPartnerId, $filterDependencies);
			foreach ($permissions as $permission)
			{
				$permissionNames[$permission->getName()] = $permission->getName();
			}			
		}
		$permissionNames = implode(',', $permissionNames);
		if ($filterDependencies)
		{
			$permissionNames = PermissionPeer::filterDependenciesByNames($permissionNames, $currentPartnerId);
		}
		return $permissionNames;
	}
	
	
	public function setAsDeleted()
	{
		// check if role is being used by some user
		$lookups = $this->getKuserToUserRolesJoinkuser();
		foreach ($lookups as $lookup) {
			if ($lookup->getKuser()->getStatus() != KuserStatus::DELETED) {
				throw new kPermissionException('Cannot delete role id ['.$this->getId().'] used by user id ['.$lookup->getKuser()->getPuserId().']', kPermissionException::ROLE_IS_BEING_USED);
			}
		}
				
		$this->setStatus(UserRoleStatus::DELETED);
		return $this;
	}
	
	public function getCacheInvalidationKeys()
	{
		return array("userRole:id=".strtolower($this->getId()));
	}
} // UserRole
