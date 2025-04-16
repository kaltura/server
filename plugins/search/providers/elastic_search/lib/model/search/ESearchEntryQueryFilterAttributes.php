<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class ESearchEntryQueryFilterAttributes extends ESearchBaseQueryFilterAttributes
{
	public function getDisplayInSearchFilter(ESearchOperator $eSearchOperator)
	{
		foreach	($this->ignoreDisplayInSearchValues as $key => $value)
		{
			switch ($key)
			{
				case ESearchEntryFieldName::PARENT_ENTRY_ID:
				case ESearchEntryFieldName::ID:
				case ESearchEntryFieldName::DISPLAY_IN_SEARCH:
					if($value)
					{
						return null;
					}
					break;
				case ESearchEntryFieldName::RECYCLED_AT:
					return null;
				default:
					break;
			}
		}

		return $this->getMustNotDisplayInSearch();
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
