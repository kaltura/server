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
 * @package    lib.model
 */
class UserRole extends BaseUserRole
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
		$newRole->setPermissionNames($this->getPermissionNames());
		$newRole->setCustomData($this->getCustomData());
		$newRole->setPartnerId($partnerId); // set new partner id
		$newRole->setTags($this->getTags());
		return $newRole;
	}
	
	
	public function setPermissionNames($permissionNames)
	{
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
	public function getPermissionNames($filterDependencies = false)
	{
		// get from DB
		$permissionNames = parent::getPermissionNames();
		$permissionNames = array_map('trim', explode(',', $permissionNames));
		$permissionNames = implode(',', $permissionNames);
		
		// translate * to permission names of all permissions valid for partner
		if ($permissionNames === self::ALL_PARTNER_PERMISSIONS_WILDCARD)
		{
			$permissionNames = '';
			$currentPartnerId = kCurrentContext::$ks_partner_id;
			if (is_null($currentPartnerId) || $currentPartnerId === '') {
				$currentPartnerId = kCurrentContext::$partner_id;
			}
			$permissions = PermissionPeer::getAllValidForPartner($currentPartnerId, $filterDependencies);
			foreach ($permissions as $permission)
			{
				$permissionNames .= $permission->getName().',';	
			}
			trim($permissionNames, ',');
			
		}
		else if ($filterDependencies)
		{
			$permissionNames = PermissionPeer::filterDependenciesByNames($permissionNames, $this->getPartnerId());
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
				
		$this->setStatus(KalturaUserRoleStatus::DELETED);
		return $this;
	}
	
} // UserRole
