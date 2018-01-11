<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class kESearchMatchPhraseQuery extends kESearchBaseFieldQuery
{
	const MATCH_PHRASE_KEY = 'match_phrase';
	const QUERY_KEY = 'query';

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
		$query[self::MATCH_PHRASE_KEY][$this->fieldName][self::QUERY_KEY] = $this->searchTerm;
		if($this->getBoostFactor())
			$query[self::MATCH_PHRASE_KEY][$this->fieldName][self::BOOST_KEY] = $this->getBoostFactor();

		return $query;
	}
}
