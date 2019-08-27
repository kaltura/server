<?php
/**
 * @package plugins.watchLater
 * @subpackage api.filters
 */

class KalturaWatchLaterUserEntryAdvancedFilter extends KalturaSearchItem
{
	/**
	 * @var int
	 */
	public $idEqual;

	/**
	 * @var string
	 */
	public $idIn;

	/**
	 * @var string
	 */
	public $userIdEqual;

	/**
	 * @var string
	 */
	public $userIdIn;

	/**
	 * @var time
	 */
	public $updatedAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $updatedAtLessThanOrEqual;

	/**
	 * @var KalturaUserEntryExtendedStatus
	 */
	public $extendedStatusEqual;

	/**
	 * @dynamicType KalturaUserEntryExtendedStatus
	 * @var string
	 */
	public $extendedStatusIn;


	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
		{
			$object_to_fill = new kWatchLaterUserEntryAdvancedFilter();
		}

		$object_to_fill->filter = $this->getBaseFilter();
		return parent::toObject($object_to_fill, $props_to_skip);
	}

	public function getBaseFilter ()
	{
		$userEntryFilter = new KalturaWatchLaterUserEntryFilter();
		foreach ($this as $key=>$value)
		{
			$userEntryFilter->$key = $value;
		}

		$userEntryFilter->typeEqual = WatchLaterPlugin::getApiValue(WatchLaterUserEntryType::WATCH_LATER);
		$userEntryFilter->orderBy = KalturaUserEntryOrderBy::UPDATED_AT_DESC;

		return $userEntryFilter->toObject();
	}
}