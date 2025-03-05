<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class ESearchEntryQueryFilterAttributes extends ESearchBaseQueryFilterAttributes
{
	public function getDisplayInSearchFilter()
	{
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
					if (in_array(EntryDisplayInSearchType::RECYCLED, $value))
					{
						$recycleDisplayInSearch = true;
					}
				}
			}
		}
		
		if (!count($ignoreDisplayInSearchQueries))
		{
			return $this->getMustNotDisplayInSearch();
		}

		if ($recycleDisplayInSearch)
		{
			$excludedSystemDisplayInSearch = array(EntryDisplayInSearchType::SYSTEM);
			$systemDisplayInSearchQuery = new kESearchTermsQuery(ESearchEntryFieldName::DISPLAY_IN_SEARCH, $excludedSystemDisplayInSearch);
			$mustNotDisplayInSearchSystem = new kESearchBoolQuery();
			$mustNotDisplayInSearchSystem->addToMustNot($systemDisplayInSearchQuery);
			return $mustNotDisplayInSearchSystem;
		}
		else
		{
			$displayInSearchBoolQuery = new kESearchBoolQuery();
			$displayInSearchBoolQuery->addToShould($this->getMustNotDisplayInSearch());
			return $displayInSearchBoolQuery;
		}

	}
	protected function getMustNotDisplayInSearch()
	{
		$excludedDisplayInSearchStatuses = array(EntryDisplayInSearchType::RECYCLED, EntryDisplayInSearchType::SYSTEM);
		$displayInSearchQuery = new kESearchTermsQuery(ESearchEntryFieldName::DISPLAY_IN_SEARCH, $excludedDisplayInSearchStatuses);
		$mustNotDisplayInSearchBoolQuery = new kESearchBoolQuery();
		$mustNotDisplayInSearchBoolQuery->addToMustNot($displayInSearchQuery);
		return $mustNotDisplayInSearchBoolQuery;
	}
}
