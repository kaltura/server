<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */

class ESearchMetadataAggregationItem extends ESearchAggregationItem
{
	const KEY = 'metadata';

	public function getAggregationCommand()
	{
		return array ('nested' =>
							array('path' => "metadata"),
						'aggs'=>array('NestedBucket' =>
							array('terms' =>
								array ('field' => 'metadata.value_text.raw' ,
										'size' =>$this->getSize()))));
	}

	public  function getAggregationKey()
	{
		return self::KEY;
	}
}