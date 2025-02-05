<?php
/**
 * @package plugins.entryPermissionLevel
 * @subpackage api.filters
 */
class KalturaPermissionLevelUserEntryFilter extends KalturaUserEntryFilter
{
	/**
	 * @var KalturaPermissionLevelArray
	 */
	public $permissionLevels;
	

	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$this->typeEqual = EntryPermissionLevelPlugin::getApiValue(PermissionLevelUserEntryType::PERMISSION_LEVEL);
		$response = parent::getListResponse($pager, $responseProfile);
		return $response;
	}
	
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		$object = parent::toObject($object_to_fill, $props_to_skip);
		$permissionLevelsBitmask = $this->getPermissionLevelsBitmask();
		if($permissionLevelsBitmask)
		{
			$object->setExtendedStatusBitAnd($permissionLevelsBitmask);
		}
		return $object;
	}

	protected function getPermissionLevelsBitmask()
	{
		if (!$this->permissionLevels || !count($this->permissionLevels))
		{
			return;
		}
		
		$permissionLevelsBitmask = 0;
		foreach ($this->permissionLevels as $permissionLevel)
		{
			/** @var KalturaPermissionLevel $permissionLevel */
			$val = $permissionLevel->permissionLevel;
			if (isset(PermissionLevelUserEntry::$permissionLevelBitmask[intval($val)]))
			{
				$permissionLevelsBitmask += PermissionLevelUserEntry::$permissionLevelBitmask[intval($val)];
			}
		}
		
		return $permissionLevelsBitmask;
	}
}
