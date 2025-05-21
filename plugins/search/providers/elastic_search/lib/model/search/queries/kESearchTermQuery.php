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
		$term = self::TERM_KEY;
		if (is_array($this->searchTerm))
		{
			if (count($this->searchTerm) > 1)
			{
				$term = self::TERMS_KEY;
				$query[$term][$this->fieldName] = $this->searchTerm;
			}
			elseif (count($this->searchTerm) === 1)
			{
				$query[$term][$this->fieldName][self::VALUE_KEY] = $this->searchTerm[0];
			}
		}
		else
		{
			$query[$term][$this->fieldName][self::VALUE_KEY] = $this->searchTerm;
		}

		if ($this->getBoostFactor())
		{
			$query[$term][$this->fieldName][self::BOOST_KEY] = $this->getBoostFactor();
		}

		return $query;
	}
}
