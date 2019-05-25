<?php
/**
 * Created by IntelliJ IDEA.
 * User: moshe.maor
 * Date: 5/26/2019
 * Time: 5:31 PM
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