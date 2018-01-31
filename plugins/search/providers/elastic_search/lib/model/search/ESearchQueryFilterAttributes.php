<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class ESearchQueryFilterAttributes
{
	public static $ignoreDisplayInSearchFields = array(
		ESearchEntryFieldName::PARENT_ENTRY_ID,
		ESearchEntryFieldName::ID,
	);

	private $ignoreDisplayInSearchValues;

	function __construct()
	{
		$this->ignoreDisplayInSearchValues = array();
		foreach	(self::$ignoreDisplayInSearchFields as $key)
			$this->ignoreDisplayInSearchValues[$key] = array();
	}

	public function getDisplayInSearchFilter()
	{
		$displayInSearchQuery = new kESearchTermQuery(ESearchEntryFieldName::DISPLAY_IN_SEARCH, EntryDisplayInSearchType::SYSTEM);
		$specialCaseQueries = array();
		foreach	($this->ignoreDisplayInSearchValues as $key => $value)
		{
			if($value)
			{
				$specialCaseQueries[] = new kESearchTermQuery($key, $value);
			}
		}

		$displayInSearchBoolQuery = new kESearchBoolQuery();
		if($specialCaseQueries)
		{
			$innerSearchBoolQuery = new kESearchBoolQuery();
			$innerSearchBoolQuery->addQueriesToShould($specialCaseQueries);
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
		$this->ignoreDisplayInSearchValues[$key][] = $value;
	}
}