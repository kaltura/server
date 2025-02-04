<?php
/**
 * @package plugins.entryPermissionLevel
 * @subpackage model
 */
class PermissionLevelUserEntry extends UserEntry
{
	const PERMISSION_LEVEL_OM_CLASS = 'PermissionLevelUserEntry';
	
	const CUSTOM_DATA_PERMISSION_LEVELS = 'permission_levels';

	const CUSTOM_DATA_PERMISSION_ORDER = 'permission_order';
	
	public static $permissionLevelBitmask = array(
			UserEntryPermissionLevel::SPEAKER => 1,
			UserEntryPermissionLevel::ROOM_MODERATOR => 2,
			UserEntryPermissionLevel::ATTENDEE => 4,
			UserEntryPermissionLevel::ADMIN => 8,
			UserEntryPermissionLevel::PREVIEW_ONLY => 16,
			UserEntryPermissionLevel::CHAT_MODERATOR => 32,
			UserEntryPermissionLevel::PANELIST => 64,
		);

	public function __construct()
	{
		$this->setType(EntryPermissionLevelPlugin::getPermissionLevelUserEntryTypeCoreValue(PermissionLevelUserEntryType::PERMISSION_LEVEL));
		parent::__construct();
	}
	
	public function getPermissionLevels()
	{
		$serialized = $this->getFromCustomData(self::CUSTOM_DATA_PERMISSION_LEVELS);
		if (!$serialized)
		{
			return null;
		}
		return unserialize($serialized);
	}
	
	public function setPermissionLevels($permissionLevels)
	{
		if(!count($permissionLevels))
			return;
		
		$this->syncExtendedStatus($permissionLevels);
		$serialized = serialize($permissionLevels);
		return $this->putInCustomData(self::CUSTOM_DATA_PERMISSION_LEVELS, $serialized);
	}

	public function getPermissionOrder()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_PERMISSION_ORDER);
	}

	public function setPermissionOrder($permissionOrder)
	{
		$this->putInCustomData(self::CUSTOM_DATA_PERMISSION_ORDER, $permissionOrder);
	}
	
	public function syncExtendedStatus($permissionLevels)
	{
		if (!$permissionLevels || !count($permissionLevels))
		{
			return;
		}
		
		$permissionLevelsBitmask = 0;
		foreach ($permissionLevels as $permissionLevel)
		{
			/** @var PermissionLevel $permissionLevel */
			$val = $permissionLevel->getPermissionLevel();
			$permissionLevelsBitmask += self::$permissionLevelBitmask[intval($val)];
		}
		
		$this->setExtendedStatus($permissionLevelsBitmask);
	}
}
