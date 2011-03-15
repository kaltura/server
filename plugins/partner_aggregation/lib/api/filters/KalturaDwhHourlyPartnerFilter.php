<?php
/**
 * @package plugins.partnerAggregation
 * @subpackage api.filters
 */
class KalturaDwhHourlyPartnerFilter extends KalturaDwhHourlyPartnerBaseFilter
{
	/**
	 * @see KalturaFilter::toObject()
	 * @return DwhHourlyPartnerFilter
	 */
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(!$object_to_fill)
			$object_to_fill = new DwhHourlyPartnerFilter();
			
		if($this->aggregatedTimeLessThanOrEqual)
		{
			$object_to_fill->set('_lte_date_id', date('Ymd'), $this->aggregatedTimeLessThanOrEqual);
			$object_to_fill->set('_lte_hour_id', date('G'), $this->aggregatedTimeLessThanOrEqual);
		}
		if($this->aggregatedTimeGreaterThanOrEqual)
		{
			$object_to_fill->set('_gte_date_id', date('Ymd'), $this->aggregatedTimeGreaterThanOrEqual);
			$object_to_fill->set('_gte_hour_id', date('G'), $this->aggregatedTimeGreaterThanOrEqual);
		}
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
