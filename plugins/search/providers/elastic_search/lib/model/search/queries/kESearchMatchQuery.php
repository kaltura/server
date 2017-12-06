<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class kESearchMatchQuery extends kESearchBaseFieldQuery
{
	const MATCH_KEY = 'match';
	const QUERY_KEY = 'query';
	const MINIMUM_SHOULD_MATCH_KEY = 'minimum_should_match';

	/**
	 * @var string
	 */
	protected $searchTerm;
	
	/**
	 * @var string
	 */
	protected $minimumShouldMatch;

	public function __construct($fieldName, $searchTerm)
	{
		$this->fieldName = $fieldName;
		$this->searchTerm = $searchTerm;
	}

	/**
	 * @return string
	 */
	public function getMinimumShouldMatch()
	{
		return $this->minimumShouldMatch;
	}

	/**
	 * @param string $minimumShouldMatch
	 */
	public function setMinimumShouldMatch($minimumShouldMatch)
	{
		$this->minimumShouldMatch = $minimumShouldMatch;
	}

	public function getFinalQuery()
	{
		$query = array();
		$query[self::MATCH_KEY][$this->fieldName][self::QUERY_KEY] = $this->searchTerm;
		if($this->getBoostFactor())
			$query[self::MATCH_KEY][$this->fieldName][self::BOOST_KEY] = $this->getBoostFactor();
		if($this->minimumShouldMatch)
			$query[self::MATCH_KEY][$this->fieldName][self::MINIMUM_SHOULD_MATCH_KEY] = $this->getMinimumShouldMatch();

		return $query;
	}
}
