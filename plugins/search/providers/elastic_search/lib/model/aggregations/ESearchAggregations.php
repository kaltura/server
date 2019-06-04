<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.aggregations
 */
class ESearchAggregations extends BaseObject
{
	const BUCKET = 'bucket';
	const DOC_COUNT = 'doc_count';
	const TERMS = 'terms';
	const SIZE = 'size';
	const AGGS = 'aggs';
	const FIELD = 'field';
	const NESTED = 'nested';
	const PATH = 'path';
	const KEY = 'key';
	const BUCKETS = 'buckets';
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