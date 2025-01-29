<?php
/**
 * @package plugins.viewHistory
 * @subpackage api.filters
 */
class KalturaViewHistoryUserEntryFilter extends KalturaUserEntryFilter
{
	/**
	 * @var KalturaUserEntryExtendedStatus
	 */
	public $extendedStatusEqual;

	/**
	 * @dynamicType KalturaUserEntryExtendedStatus
	 * @var string
	 */
	public $extendedStatusIn;

	/**
	 * @dynamicType KalturaUserEntryExtendedStatus
	 * @var string
	 */
	public $extendedStatusNotIn;

	static private $map_between_objects = array
	(
		"extendedStatusEqual" => "_eq_extended_status",
		"extendedStatusIn" => "_in_extended_status",
		"extendedStatusNotIn" => "_notin_extended_status",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$this->typeEqual = ViewHistoryPlugin::getApiValue(ViewHistoryUserEntryType::VIEW_HISTORY);
		$response = parent::getListResponse($pager, $responseProfile);
		
		return $response;
	}
	
	public function toObject ($object_to_fill = null, $props_to_skip = array())
	{
		if (kCurrentContext::getCurrentSessionType() == SessionType::USER)
		{
			$this->userIdEqual = kCurrentContext::getCurrentKsKuser()->getPuserId();
			$this->userIdIn = null;
			$this->userIdNotIn = null;
		}
		elseif (!$this->userIdEqual && !$this->userIdIn && !$this->userIdNotIn)
		{
			$this->userIdEqual = kCurrentContext::getCurrentKsKuser() ? kCurrentContext::getCurrentKsKuser()->getPuserId() : null;
		}
		
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
