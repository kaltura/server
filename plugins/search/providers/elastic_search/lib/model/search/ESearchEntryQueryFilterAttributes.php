<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class ESearchEntryQueryFilterAttributes extends ESearchBaseQueryFilterAttributes
{
	public function getDisplayInSearchFilter()
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
}