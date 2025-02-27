<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class ESearchEntryQueryFilterAttributes extends ESearchBaseQueryFilterAttributes
{
	public function getDisplayInSearchFilter()
	{
		$excludedDisplayInSearchStatuses = array(EntryDisplayInSearchType::RECYCLED, EntryDisplayInSearchType::SYSTEM);
		$displayInSearchQuery = new kESearchTermsQuery(ESearchEntryFieldName::DISPLAY_IN_SEARCH, $excludedDisplayInSearchStatuses);
		$mustNotDisplayInSearchBoolQuery = new kESearchBoolQuery();
		$mustNotDisplayInSearchBoolQuery->addToMustNot($displayInSearchQuery);
		
		$queryContainsDisplayInSearch = false;
		$recycleDisplayInSearch = false;
		$ignoreDisplayInSearchQueries = array();
		foreach	($this->ignoreDisplayInSearchValues as $key => $value)
		{
			if ($key == ESearchEntryFieldName::RECYCLED_AT)
			{
				return null;
			}
			elseif ($value)
			{
				$ignoreDisplayInSearchQueries[] = new kESearchTermsQuery($key, $value);
				if ($key == ESearchEntryFieldName::DISPLAY_IN_SEARCH)
				{
					$queryContainsDisplayInSearch = true;
					if (in_array(EntryDisplayInSearchType::RECYCLED, $value))
					{
						$recycleDisplayInSearch = true;
					}

				}
			}
		}
		
		if (!count($ignoreDisplayInSearchQueries))
		{
			return $mustNotDisplayInSearchBoolQuery;
		}

		if (!$queryContainsDisplayInSearch || ($queryContainsDisplayInSearch && !$recycleDisplayInSearch))
		{
			$displayInSearchBoolQuery = new kESearchBoolQuery();
			$displayInSearchBoolQuery->addToShould($mustNotDisplayInSearchBoolQuery);
			return $displayInSearchBoolQuery;
		}
		if ($queryContainsDisplayInSearch && $recycleDisplayInSearch)
		{
			$excludedSystemDisplayInSearch = array(EntryDisplayInSearchType::SYSTEM);
			$systemDisplayInSearchQuery = new kESearchTermsQuery(ESearchEntryFieldName::DISPLAY_IN_SEARCH, $excludedSystemDisplayInSearch);
			$mustNotDisplayInSearchSystem = new kESearchBoolQuery();
			$mustNotDisplayInSearchSystem->addToMustNot($systemDisplayInSearchQuery);
			return $mustNotDisplayInSearchSystem;
		}

		return null;
	}
}
