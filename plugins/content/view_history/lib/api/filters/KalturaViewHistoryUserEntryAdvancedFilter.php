<?php
/**
 * @package plugins.viewHistory
 * @subpackage api.filters
 */
class KalturaViewHistoryUserEntryAdvancedFilter extends KalturaSearchItem
{
	/**
	 * @var string
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
	 * @var string
	 */
	public $updatedAtGreaterThanOrEqual;
	
	/**
	 * @var string
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
			$object_to_fill = new kViewHistoryUserEntryAdvancedFilter();
		
		$object_to_fill->filter = $this->getBaseFilter();
			
		return parent::toObject($object_to_fill, $props_to_skip);
	}
	
	public function getBaseFilter ()
	{
		$userEntryFilter = new KalturaViewHistoryUserEntryFilter();
		foreach ($this as $key=>$value)
		{
			$userEntryFilter->$key = $value;
		}
		
		$userEntryFilter->typeEqual = ViewHistoryPlugin::getApiValue(ViewHistoryUserEntryType::VIEW_HISTORY);
		$userEntryFilter->orderBy = KalturaUserEntryOrderBy::UPDATED_AT_DESC;
		
		return $userEntryFilter->toObject();
	}
}