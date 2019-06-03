<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */

class ESearchCuepointsAggregationItem extends ESearchAggregationItem
{
	/***
	 * var ESearchCuePointsAggregationFieldName
	 */
	protected $fieldName;

	const KEY = 'cue_points';
	public function getAggregationCommand()
	{
		return array ('nested' =>
			array('path' => "cue_points"),
					'aggs'=>array('NestedBucket' =>
						array('terms' =>
							array ('field' => $this->getFieldName(),
								'size' =>$this->getSize()))));
	}

	public  function getAggregationKey()
	{
		return self::KEY;
	}
}