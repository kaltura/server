<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class ESearchQueryFilterAttributes
{
	private $ignoreDisplayInSearchValues;

	function __construct()
	{
		$this->ignoreDisplayInSearchValues = array();
	}

	public function getEntryDisplayInSearchFilter()
	{
		$displayInSearchQuery = new kESearchTermQuery(ESearchEntryFieldName::DISPLAY_IN_SEARCH, EntryDisplayInSearchType::SYSTEM);
		$ignoreDisplayInSearchQueries = array();
		foreach	($this->ignoreDisplayInSearchValues as $key => $value)
		{
			if($value)
			{
				$ignoreDisplayInSearchQueries[] = new kESearchTermsQuery($key, $value);
			}
		}

		$displayInSearchBoolQuery = new kESearchBoolQuery();
		if($ignoreDisplayInSearchQueries)
		{
			$innerSearchBoolQuery = new kESearchBoolQuery();
			$innerSearchBoolQuery->addQueriesToShould($ignoreDisplayInSearchQueries);
			$innerDisplayInSearchBoolQuery = new kESearchBoolQuery();
			$innerDisplayInSearchBoolQuery->addToMustNot($displayInSearchQuery);
			$innerSearchBoolQuery->addToShould($innerDisplayInSearchBoolQuery);
			$displayInSearchBoolQuery->addToFilter($innerSearchBoolQuery);
		}
		else
		{
			$displayInSearchBoolQuery->addToMustNot($displayInSearchQuery);
		}

		return $displayInSearchBoolQuery;
	}

	public function addValueToIgnoreDisplayInSearch($key, $value)
	{
		if(!array_key_exists($key, $this->ignoreDisplayInSearchValues))
			$this->ignoreDisplayInSearchValues[$key] = array();

		$this->ignoreDisplayInSearchValues[$key][] = $value;
	}
}