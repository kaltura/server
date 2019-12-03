<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.filters
 */

class ESearchAdapterFactory
{
	/**
	 * @param KalturaRelatedFilter $filter
	 * @return ESearchQueryFromFilter
	 */
	public static function getAdapter(KalturaRelatedFilter $filter)
	{
		if($filter instanceof KalturaBaseEntryBaseFilter)
		{
			return new ESearchEntryQueryFromFilter();
		}

		if($filter instanceof KalturaCaptionAssetBaseFilter)
		{
			return new ESearchCaptionQueryFromFilter();
		}

		return null;
	}
}
