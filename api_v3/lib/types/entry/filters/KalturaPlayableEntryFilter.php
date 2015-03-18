<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaPlayableEntryFilter extends KalturaPlayableEntryBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaBaseEntryFilter::prepareEntriesCriteriaFilter()
	 * 
	 * Convert duration in seconds to msecs (because the duration field is mapped to length_in_msec)
	 */
	protected function prepareEntriesCriteriaFilter(KalturaFilterPager $pager)
	{
		if ($this->durationGreaterThan !== null)
			$this->durationGreaterThan = $this->durationGreaterThan * 1000;

		//When translating from seconds to msec need to subtract 500 msec since entries greater than 5500 msec are considered as entries with 6 sec
		if ($this->durationGreaterThanOrEqual !== null)
			$this->durationGreaterThanOrEqual = $this->durationGreaterThanOrEqual * 1000 - 500;
			
		if ($this->durationLessThan !== null)
			$this->durationLessThan = $this->durationLessThan * 1000;
			
		//When translating from seconds to msec need to add 499 msec since entries less than 5499 msec are considered as entries with 5 sec
		if ($this->durationLessThanOrEqual !== null)
			$this->durationLessThanOrEqual = $this->durationLessThanOrEqual * 1000 + 499;
			
		return parent::prepareEntriesCriteriaFilter($pager);
	}
	
	public function __construct()
	{
		$this->typeIn = KalturaEntryType::MEDIA_CLIP . ',' . KalturaEntryType::MIX . ',' . KalturaEntryType::LIVE_STREAM;
	}
}
