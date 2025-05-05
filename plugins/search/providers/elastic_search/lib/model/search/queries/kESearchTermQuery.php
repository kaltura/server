<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class kESearchTermQuery extends kESearchBaseFieldQuery
{
	const TERM_KEY = 'term';
	const TERMS_KEY = 'terms';
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
		if (count($this->searchTerm) > 1)
		{
			$query[self::TERMS_KEY][$this->fieldName] = $this->searchTerm;
		}
		else
		{
			$query[self::TERM_KEY][$this->fieldName][self::VALUE_KEY] = $this->searchTerm;
			if ($this->getBoostFactor())
			{
				$query[self::TERM_KEY][$this->fieldName][self::BOOST_KEY] = $this->getBoostFactor();
			}
		}
		
		return $query;
	}
}
