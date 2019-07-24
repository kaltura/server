<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */

class ESearchMetadataAggregationItem extends ESearchAggregationItem
{
	const KEY = 'metadata';
	const SUB_AGG = 'subagg';

	public function getAggregationCommand()
	{

		return array (ESearchAggregations::NESTED =>
			array(ESearchAggregations::PATH => "metadata"),
					ESearchAggregations::AGGS=>
						array(self::NESTED_BUCKET =>
						array(
							ESearchAggregations::TERMS =>
							array (ESearchAggregations::FIELD => 'metadata.xpath', ESearchAggregations::SIZE =>$this->getSize()),
			 				ESearchAggregations::AGGS =>
								array (self::SUB_AGG =>
									array(ESearchAggregations::TERMS =>
										array (ESearchAggregations::FIELD => 'metadata.value_text.raw', ESearchAggregations::SIZE =>$this->getSize()))))));
	}

	public  function getAggregationKey()
	{
		return self::KEY;
	}
}