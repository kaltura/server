<?php


/**
 * Skeleton subclass for representing a row from the 'permission' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class Permission extends BasePermission
{
	
	/**
	 * Add a permission item to the current permission
	 * @param int $permissionItemId
	 * @throws kPermissionException::PERMISSION_ITEM_NOT_FOUND
	 */
	public function addPermissionItem($permissionItemId, $save = true)
	{
		// check if permission item exists
		$permissionItem = PermissionItemPeer::retrieveByPK($permissionItemId);
		if (!$permissionItem) {
			throw new kPermissionException('', kPermissionException::PERMISSION_ITEM_NOT_FOUND);
		}
		
		// check if item is already associated to the permission
		$permissionToItem = PermissionToPermissionItemPeer::getByPermissionNameAndItemId($this->getName(), $permissionItemId);
		if ($permissionToItem) {
			KalturaLog::notice('Permission with name ['.$this->getName().'] already contains permission item with id ['.$permissionItemId.']');
			return true;
		}
		
		// add item to permission
		$permissionToItem = new PermissionToPermissionItem();
		$permissionToItem->setPermissionItem($permissionItem);
		$this->addPermissionToPermissionItem($permissionToItem);
		if ($save) {
			$this->save();
		}
		return true;
	}
	
	/**
	 * @return array Array of permission item ids associated with the current permission
	 */
	public function getPermissionItemIds()
	{
		$ids = array();
		$lookups = $this->getPermissionToPermissionItems();
		if (!$lookups) {
			return null;
		}		
		foreach ($lookups as $lookup) {
			$ids[] = $lookup->getPermissionItemId();
		}
		return $ids;
	}
	
	/**
	 * @return array Array of permission item objects associated with the current permission
	 */
	public function getPermissionItems()
	{
		$ids = $this->getPermissionItems();
				
		$c = new Criteria();
		$c->add(PermissionItemPeer::ID, $ids, Criteria::IN);
		$items = PermissionItemPeer::doSelect($c);
		
		return $items;
	}	

	/**
	 * Remove the given permission item from the current permission
	 * @param int $permissionItemId
	 */
	public function removePermissionItem($permissionItemId)
	{		
		// check if item is already associated to the permission
		$permissionToItem = PermissionToPermissionItemPeer::getByPermissionNameAndItemId($this->getName(), $permissionItemId);
		if (!$permissionToItem) {
			KalturaLog::notice('Permission with name ['.$this->getName().'] does not contain permission item with id ['.$permissionItemId.']');
			return true;
		}
		
		// delete association between item and permission
		$permissionToItem->delete();
	}
	

	/**
	 * Set the permission items of the current permission.
	 * @param string $idsString A comma seperated string of permission item IDs
	 */
	public function setPermissionItems($idsString)
	{
		$this->deleteAllPermissionItems();
		$ids = explode(',', trim($idsString));
		
		foreach ($ids as $id)
		{
			if (!is_null($id) && $id != '') {
				$this->addPermissionItem($id, false);
			}
		}
		
		$this->save();
	}
	
	
	/**
	 * Delete all permission items related from current pemission.
	 */
	private function deleteAllPermissionItems()
	{
		$c = new Criteria();
		$c->add(PermissionToPermissionItemPeer::PERMISSION_NAME, $this->getName(), Criteria::EQUAL);
		PermissionToPermissionItemPeer::doDelete($c);
	}
	
	/**
	 * Copy current permission to the given partner.
	 * @param int $partnerId
	 */
	public function copyToPartner($partnerId)
	{
		$permission = new Permission();
		$permission->setName($this->getName());
		$permission->setFriendlyName($this->getFriendlyName());
		$permission->setDescription($this->getDescription());
		$permission->setStatus($this->getStatus());
		$permission->setTags($this->getTags());
		$permission->setType($this->getType());
		$permission->setCustomData($this->getCustomData());
		$permission->setPartnerId($partnerId); // set new partner id
		return $permission;
	}
	
	
	public function getPartnerGroup()
	{
		$this->getFromCustomData('partner_group');
	}
	
	public function setPartnerGroup($group)
	{
		$this->putInCustomData('partner_group', $group);
	}
	
} // Permission
