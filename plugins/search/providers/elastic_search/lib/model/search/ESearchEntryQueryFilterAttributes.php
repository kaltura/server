<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class ESearchEntryQueryFilterAttributes extends ESearchBaseQueryFilterAttributes
{
	public function getDisplayInSearchFilter()
	{
		$displayInSearchStatuses = array(EntryDisplayInSearchType::RECYCLED, EntryDisplayInSearchType::SYSTEM);
		$displayInSearchQuery = new kESearchTermsQuery(ESearchEntryFieldName::DISPLAY_IN_SEARCH, $displayInSearchStatuses);
		$mustNotDisplayInSearchBoolQuery = new kESearchBoolQuery();
		$mustNotDisplayInSearchBoolQuery->addToMustNot($displayInSearchQuery);
		
		$queryContainsDisplayInSearch = false;
		$ignoreDisplayInSearchQueries = array();
		foreach	($this->ignoreDisplayInSearchValues as $key => $value)
		{
			if ($value)
			{
				$ignoreDisplayInSearchQueries[] = new kESearchTermsQuery($key, $value);
				if ($key == ESearchEntryFieldName::DISPLAY_IN_SEARCH)
				{
					$queryContainsDisplayInSearch = true;
				}
			}
		}
		
		if (!count($ignoreDisplayInSearchQueries))
		{
			return $mustNotDisplayInSearchBoolQuery;
		}
		
		$displayInSearchBoolQuery = new kESearchBoolQuery();
		$displayInSearchBoolQuery->addQueriesToShould($ignoreDisplayInSearchQueries);
		if (!$queryContainsDisplayInSearch)
		{
			$displayInSearchBoolQuery->addToShould($mustNotDisplayInSearchBoolQuery);
		}
		return $displayInSearchBoolQuery;
	}
}