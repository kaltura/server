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
		$mustNotDisplayInSearchBoolQuery = new kESearchBoolQuery();
		$mustNotDisplayInSearchBoolQuery->addToMustNot($displayInSearchQuery);

		$ignoreDisplayInSearchQueries = array();

		foreach	($this->ignoreDisplayInSearchValues as $key => $value)
		{
			if($value)
				$ignoreDisplayInSearchQueries[] = new kESearchTermsQuery($key, $value);
		}

		if(count($ignoreDisplayInSearchQueries))
		{
			$displayInSearchBoolQuery = new kESearchBoolQuery();
			$displayInSearchBoolQuery->addQueriesToShould($ignoreDisplayInSearchQueries);
			$displayInSearchBoolQuery->addToShould($mustNotDisplayInSearchBoolQuery);
			return $displayInSearchBoolQuery;
		}

		return $mustNotDisplayInSearchBoolQuery;
	}
}