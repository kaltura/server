<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.aggregations
 */
class ESearchAggregations extends BaseObject
{
	/**
	 * @var array
	 */
	protected $aggregations;

	/**
	 * @return array
	 */
	public function getAggregations()
	{
		return $this->aggregations;
	}

	/**
	 * @param array $aggregations
	 */
	public function setAggregations($aggregations)
	{
		$this->aggregations = $aggregations;
	}

}