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

	/**
	 * @var int
	 */
	public $permissionLevelsBitmask;

	static private $map_between_objects = array
	(
		'permissionLevelsBitmask' => '_bitor_extended_status',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$this->typeEqual = EntryPermissionLevelPlugin::getApiValue(PermissionLevelUserEntryType::PERMISSION_LEVEL);

		$this->setPermissionLevelsBitMask();

		$response = parent::getListResponse($pager, $responseProfile);
		return $response;
	}

	protected function setPermissionLevelsBitMask()
	{
		if (!$this->permissionLevels)
		{
			return;
		}

		$permissionBitMask = [
			KalturaUserEntryPermissionLevel::SPEAKER => 1,
			KalturaUserEntryPermissionLevel::ROOM_MODERATOR => 2,
			KalturaUserEntryPermissionLevel::ATTENDEE => 4,
			KalturaUserEntryPermissionLevel::ADMIN => 8,
			KalturaUserEntryPermissionLevel::PREVIEW_ONLY => 16,
			KalturaUserEntryPermissionLevel::CHAT_MODERATOR => 32,
			KalturaUserEntryPermissionLevel::PANELIST => 64,
		];

		$extendedHistory = 0;
		foreach ($this->permissionLevels as $permissionLevel)
		{
			/** @var KalturaPermissionLevel $permissionLevel */
			$val = $permissionLevel->permissionLevel;
			$extendedHistory += $permissionBitMask[intval($val)];
		}

		if ($extendedHistory)
		{
			$this->permissionLevelsBitmask = $extendedHistory;
		}
	}
}
