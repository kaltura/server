<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */

class ESearchCuepointsAggregationItem extends ESearchAggregationItem
{
	/**
	 * var ESearchCuePointsAggregationFieldName
	 */
	protected $fieldName;

	const KEY = 'cue_points';
	public function getAggregationCommand()
	{
		return array (ESearchAggregations::NESTED =>
			array(ESearchAggregations::PATH => "cue_points"),
					ESearchAggregations::AGGS=>array(self::NESTED_BUCKET =>
						parent::getAggregationCommand()
					));
	}

	public  function getAggregationKey()
	{
		return self::KEY;
	}
}