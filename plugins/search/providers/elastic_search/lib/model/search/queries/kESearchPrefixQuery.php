<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class kESearchPrefixQuery extends kESearchBaseFieldQuery
{
	const PREFIX_KEY = 'prefix';
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
		$query[self::PREFIX_KEY][$this->fieldName][self::VALUE_KEY] = $this->searchTerm;
		if($this->getBoostFactor())
			$query[self::PREFIX_KEY][$this->fieldName][self::BOOST_KEY] = $this->getBoostFactor();
		
		return $query;
	}
}
