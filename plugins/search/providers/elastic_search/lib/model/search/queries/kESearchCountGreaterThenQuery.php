<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class kESearchCountGreaterThenQuery extends kESearchBaseFieldQuery
{
	const SCRIPT_KEY = 'script';
	const SOURCE_KEY = 'source';

	/**
	 * @var string
	 */
	protected $threshold;
	
	public function __construct($fieldName, $searchTerm)
	{
		$this->fieldName = $fieldName;
		$this->threshold = $searchTerm;
	}
	
	public function getFinalQuery()
	{
		$query = array();
		$query[self::SCRIPT_KEY][self::SCRIPT_KEY][self::SOURCE_KEY] = "doc['$this->fieldName'].length > $this->threshold";
		
		return $query;
	}
}
