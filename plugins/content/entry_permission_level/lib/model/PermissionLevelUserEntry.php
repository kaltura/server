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

	const PERMISSION_BIT_MASK = [
			UserEntryPermissionLevel::SPEAKER => 1,
			UserEntryPermissionLevel::ROOM_MODERATOR => 2,
			UserEntryPermissionLevel::ATTENDEE => 4,
			UserEntryPermissionLevel::ADMIN => 8,
			UserEntryPermissionLevel::PREVIEW_ONLY => 16,
			UserEntryPermissionLevel::CHAT_MODERATOR => 32,
			UserEntryPermissionLevel::PANELIST => 64,
		];

	public function __construct()
	{
		$this->setType(EntryPermissionLevelPlugin::getPermissionLevelUserEntryTypeCoreValue(PermissionLevelUserEntryType::PERMISSION_LEVEL));
		parent::__construct();
	}
	
	public function getPermissionLevels()
	{
		$permissionLevels = $this->decodePermissionLevels(($this->getExtendedStatus()));

		return array_map( function($permissionLevel)
		{
			$permissionLevelObject = new PermissionLevel();
			$permissionLevelObject->setPermissionLevel($permissionLevel);
			return $permissionLevelObject;
		}, $permissionLevels);
	}

	protected function decodePermissionLevels($encodedPermissionLevels)
	{
		$permissionLevels = array();
		foreach (self::PERMISSION_BIT_MASK as $permission => $permissionBitMask)
		{
			if ($encodedPermissionLevels & $permissionBitMask)
			{
				$permissionLevels[] = $permission;
			}
		}

		return $permissionLevels;
	}
	
	public function setPermissionLevels($permissionLevels)
	{
		if (!count($permissionLevels))
		{
			return;
		}

		$encodedPermissions = $this->encodePermissionLevels($permissionLevels);
		$this->setExtendedStatus($encodedPermissions);
	}

	protected function encodePermissionLevels($permissionLevels)
	{
		$encodedPermissionLevels = 0;
		foreach ($permissionLevels as $permissionLevel)
		{
			/** @var PermissionLevel $permissionLevel */
			$bitMask = self::PERMISSION_BIT_MASK[$permissionLevel->getPermissionLevel()];
			$encodedPermissionLevels = $encodedPermissionLevels | $bitMask;
		}

		return $encodedPermissionLevels;
	}

	public function getPermissionOrder()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_PERMISSION_ORDER);
	}

	public function setPermissionOrder($permissionOrder)
	{
		$this->putInCustomData(self::CUSTOM_DATA_PERMISSION_ORDER, $permissionOrder);
	}
}
