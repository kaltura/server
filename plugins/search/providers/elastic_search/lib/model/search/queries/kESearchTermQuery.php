<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class kESearchTermQuery extends kESearchBaseFieldQuery
{
	const TERM_KEY = 'term';
	const VALUE_KEY = 'value';

	/**
	 * @var string
	 */
	protected $searchTerm;
	
	public function __construct($fieldName, $searchTerm)
	{
		$this->fieldName = $fieldName;
		$this->searchTerm = $searchTerm;
	}
	
	public function getFinalQuery()
	{
		$query = array();
		$query[self::TERM_KEY][$this->fieldName][self::VALUE_KEY] = $this->searchTerm;
		if($this->getBoostFactor())
			$query[self::TERM_KEY][$this->fieldName][self::BOOST_KEY] = $this->getBoostFactor();
		
		return $query;
	}
}
