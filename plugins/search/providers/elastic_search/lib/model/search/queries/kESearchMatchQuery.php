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
	const ANALYZER = 'analyzer';

	/**
	 * @var string
	 */
	protected $searchTerm;
	
	/**
	 * @var string
	 */
	protected $minimumShouldMatch;

	/**
	 * @var string
	 */
	protected $analyzer;

	public function __construct($fieldName, $searchTerm)
	{
		$this->fieldName = $fieldName;
		$this->searchTerm = $searchTerm;
		$this->analyzer = null;
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

	/**
	 * @return string
	 */
	public function getAnalyzer()
	{
		return $this->analyzer;
	}

	/**
	 * @param string $analyzer
	 */
	public function setAnalyzer($analyzer)
	{
		$this->analyzer = $analyzer;
	}

	public function getFinalQuery()
	{
		$query = array();
		$query[self::MATCH_KEY][$this->fieldName][self::QUERY_KEY] = $this->searchTerm;
		if($this->getBoostFactor())
			$query[self::MATCH_KEY][$this->fieldName][self::BOOST_KEY] = $this->getBoostFactor();
		if($this->minimumShouldMatch)
			$query[self::MATCH_KEY][$this->fieldName][self::MINIMUM_SHOULD_MATCH_KEY] = $this->getMinimumShouldMatch();
		if ($this->getAnalyzer())
			$query[self::MATCH_KEY][$this->fieldName][self::ANALYZER] = $this->getAnalyzer();

		return $query;
	}
}
