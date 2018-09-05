<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class kESearchContextCompletionSuggestQuery extends kESearchCompletionSuggestQuery
{

	const CONTEXTS_KEY = 'contexts';

	/**
	 * @var array
	 */
	protected $contexts = array();

	public function addContext(kESearchSuggestContext $context)
	{
		$this->contexts[$context->getName()][] = $context->getContext();
	}

	/**
	 * @return array
	 */
	public function getContexts()
	{
		return $this->contexts;
	}

	public function getFinalQuery()
	{
		$query = parent::getFinalQuery();
		if ($this->getSuggestName()) 
		{
			$query[self::SUGGEST_KEY][$this->getSuggestName()][self::COMPLETION_KEY][self::CONTEXTS_KEY] = $this->getContexts();
		}
		return $query;
	}

}
