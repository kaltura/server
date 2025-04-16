<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class kESearchBoolQuery extends kESearchCompoundQuery
{

	const BOOL_KEY = 'bool';
	const SHOULD_KEY = 'should';
	const MUST_KEY = 'must';
	const MUST_NOT_KEY = 'must_not';
	const FILTER_KEY = 'filter';
	const MINIMUM_SHOULD_MATCH_KEY = 'minimum_should_match';

	/**
	 * @var array
	 */
	private $boolQuery;

	/**
	 * @var array
	 */
	private $filterQueries;

	/**
	 * @var array
	 */
	private $mustQueries;

	/**
	 * @var array
	 */
	private $shouldQueries;

	/**
	 * @var array
	 */
	private $mustNotQueries;

	/**
	 * @var int
	 */
	private $minimumShouldMatch;

	public function __construct()
	{
		$this->boolQuery = array();
		$this->filterQueries = array();
		$this->mustQueries = array();
		$this->shouldQueries = array();
		$this->mustNotQueries = array();
		$this->minimumShouldMatch = 1;
	}

	/**
	 * @return int
	 */
	public function getMinimumShouldMatch()
	{
		return $this->minimumShouldMatch;
	}

	/**
	 * @param int $minimumShouldMatch
	 */
	public function setMinimumShouldMatch($minimumShouldMatch)
	{
		$this->minimumShouldMatch = $minimumShouldMatch;
	}

	/**
	 * @return array
	 */
	public function getShouldQueries()
	{
		return $this->shouldQueries;
	}

	public function addToFilter($query)
	{
		$this->filterQueries[] = $query;
	}

	public function addQueriesToFilter($queries)
	{
		$this->filterQueries = array_merge($this->filterQueries, $queries);
	}

	public function addToMust($query)
	{
		$this->mustQueries[] = $query;
	}

	public function addToShould($query)
	{
		$this->shouldQueries[] = $query;
	}

	public function addQueriesToShould($queries)
	{
		$this->shouldQueries = array_merge($this->shouldQueries, $queries);
	}

	public function addToMustNot($query)
	{
		$this->mustNotQueries[] = $query;
	}

	public function getFinalQuery()
	{
		if($this->shouldAddToFinalQuery($this->filterQueries))
			$this->addToFinalQuery(self::FILTER_KEY, $this->filterQueries);

		if($this->shouldAddToFinalQuery($this->mustQueries))
			$this->addToFinalQuery(self::MUST_KEY, $this->mustQueries);

		if($this->shouldAddToFinalQuery($this->shouldQueries))
		{
			$this->addToFinalQuery(self::SHOULD_KEY, $this->shouldQueries);
			$this->boolQuery[self::BOOL_KEY][self::MINIMUM_SHOULD_MATCH_KEY] = $this->getMinimumShouldMatch();
		}

		if($this->shouldAddToFinalQuery($this->mustNotQueries))
			$this->addToFinalQuery(self::MUST_NOT_KEY,$this->mustNotQueries);

		return $this->boolQuery;
	}

	public function addByOperatorType($boolOperator, $query)
	{
		switch ($boolOperator) 
		{
			case self::MUST_KEY:
				$this->addToMust($query);
				break;
			case self::FILTER_KEY:
				$this->addToFilter($query);
				break;
			case self::MUST_NOT_KEY:
				$this->addToMustNot($query);
				break;
			case self::SHOULD_KEY:
				$this->addToShould($query);
				break;
			default:
				KalturaLog::log("Undefined bool operator in kESearchBoolQuery[".$boolOperator."]");
		}
	}

	private function shouldAddToFinalQuery($queriesPath)
	{
		if(count($queriesPath))
			return true;

		return false;
	}

	private function addToFinalQuery($queryKey, $queriesPath)
	{
		foreach ($queriesPath as $query)
		{
			$this->boolQuery[self::BOOL_KEY][$queryKey][] = $query->getFinalQuery();
		}
	}

}
