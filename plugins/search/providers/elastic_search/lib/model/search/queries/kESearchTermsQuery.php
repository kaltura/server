<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class kESearchTermsQuery extends kESearchBaseFieldQuery
{
	const TERMS_KEY = 'terms';

	/**
	 * @var array
	 */
	protected $searchTerms;

	public function __construct($fieldName, $searchTerms)
	{
		$this->fieldName = $fieldName;
		$this->searchTerms = $searchTerms;
	}

	public function getFinalQuery()
	{
		$query = array();
		$query[self::TERMS_KEY][$this->fieldName] = $this->searchTerms;
		return $query;
	}
}
