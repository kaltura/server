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
 * @package Core
 * @subpackage model
 */
class Permission extends BasePermission
{
	
	private $permissionItemIds = null;
	private $permissionItemIdsChanged = false;
	
	
	public function __construct()
	{
		$this->permissionItemIds = null;
		$this->permissionItemIdsChanged = false;	
	}
	
	public function preSave(PropelPDO $con = null)
	{
		if ($this->permissionItemIdsChanged)
		{
			PermissionItemPeer::checkValidForParther(implode(',',$this->permissionItemIds), $this->getPartnerId());
		}
		
		return parent::preSave($con);
	}
	
	
	public function postSave(PropelPDO $con = null) 
	{
		if ($this->permissionItemIdsChanged)
		{

			$currentPermissions = PermissionToPermissionItemPeer::retrieveByPermissionId($this->getId());
			$currentPermissionsItemIds = array_map(function ($element) { return $element->getPermissionItemId(); }, $currentPermissions);
			
			// Remove old permissions
			$permissionsItemsToRemove = array_diff($currentPermissionsItemIds, $this->permissionItemIds);
			$this->deletePermissionItems($permissionsItemsToRemove);
			
			// Add new permissions
			$permissionsItemsToAdd = array_diff($this->permissionItemIds, $currentPermissionsItemIds);
			foreach ($permissionsItemsToAdd as $itemId)
			{
				if (!is_null($itemId) && $itemId !== '')
				{
					$permissionToPermissionItem = new PermissionToPermissionItem();
					$permissionToPermissionItem->setPermissionItemId($itemId);
					$permissionToPermissionItem->setPermissionId($this->getId());
					$permissionToPermissionItem->save();
				}
			}
		}
		
		$this->permissionItemIds = null;
		$this->permissionItemIdsChanged = false;
		return parent::postSave($con);
		
	}
		
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
		
		$itemIds = $this->getPermissionItemIds(); // init $this->permissionItemIds
		
		// check if item is already associated with the permission
		if ($itemIds && in_array($permissionItemId, $itemIds)) {
			KalturaLog::notice('Permission with name ['.$this->getName().'] already contains permission item with id ['.$permissionItemId.']');
			return true;
		}
		
		$this->permissionItemIds[$permissionItemId] = $permissionItemId;
		$this->permissionItemIdsChanged = true;

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
		if (is_null($this->permissionItemIds))
		{
			$c = new Criteria();
			$c->addAnd(PermissionToPermissionItemPeer::PERMISSION_ID, $this->getId(), Criteria::EQUAL);
			$results = PermissionToPermissionItemPeer::doSelect($c);
			if (!$results) {
				return null;
			}
			$ids = array();
			foreach ($results as $result)
				$ids[] = $result->getPermissionItemId();
			$this->permissionItemIds = $ids;
		}		
		return $this->permissionItemIds;
	}
	
	/**
	 * @return array Array of permission item objects associated with the current permission
	 */
	public function getPermissionItems()
	{
		$ids = $this->getPermissionItemIds();
				
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
		$itemIds = $this->getPermissionItemIds();
		if (!in_array($permissionItemId, $itemIds)) {
			KalturaLog::notice('Permission with name ['.$this->getName().'] does not contain permission item with id ['.$permissionItemId.']');
			return true;
		}
		
		unset($this->permissionItemIds[$permissionItemId]);
		$this->permissionItemIdsChanged = true;
	}
	

	/**
	 * Set the permission items of the current permission.
	 * @param string $idsString A comma seperated string of permission item IDs
	 */
	public function setPermissionItems($idsString)
	{
		$this->permissionItemIds = explode(',', trim($idsString));
		$this->permissionItemIdsChanged = true;
	}
	
	
	/**
	 * Delete all permission items related from current pemission.
	 */
	private function deletePermissionItems(array $permissionsItemsToRemove)
	{
		if(!count($permissionsItemsToRemove))
			return;
		
		$c = new Criteria();
		$c->add(PermissionToPermissionItemPeer::PERMISSION_ID, $this->getId(), Criteria::EQUAL);
		$c->add(PermissionToPermissionItemPeer::PERMISSION_ITEM_ID, $permissionsItemsToRemove, Criteria::IN);
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
		return $this->getFromCustomData('partner_group');
	}
	
	public function setPartnerGroup($group)
	{
		$this->putInCustomData('partner_group', $group);
	}
	
	public function getCacheInvalidationKeys()
	{
		return array("permission:partnerId=".$this->getPartnerId());
	}
} // Permission
