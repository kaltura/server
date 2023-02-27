<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class ESearchEntryQueryFilterAttributes extends ESearchBaseQueryFilterAttributes
{
	public function getDisplayInSearchFilter()
	{
		$displayInSearchBoolQuery = new kESearchBoolQuery();
		
		$mustNotDisplayInSearchTypes = array(EntryDisplayInSearchType::RECYCLED, EntryDisplayInSearchType::SYSTEM);
		foreach ($mustNotDisplayInSearchTypes as $type)
		{
			$displayInSearchQuery = new kESearchTermQuery(ESearchEntryFieldName::DISPLAY_IN_SEARCH, $type);
			$mustNotDisplayInSearchBoolQuery = new kESearchBoolQuery();
			$mustNotDisplayInSearchBoolQuery->addToMustNot($displayInSearchQuery);
			$displayInSearchBoolQuery->addToShould($mustNotDisplayInSearchBoolQuery);
		}
		
		$ignoreDisplayInSearchQueries = array();
		foreach	($this->ignoreDisplayInSearchValues as $key => $value)
		{
			if ($value)
			{
				$ignoreDisplayInSearchQueries[] = new kESearchTermsQuery($key, $value);
			}
		}
		if (count($ignoreDisplayInSearchQueries))
		{
			$displayInSearchBoolQuery->addQueriesToShould($ignoreDisplayInSearchQueries);
		}

		return $displayInSearchBoolQuery;
	}
}