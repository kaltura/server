<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class ESearchEntryQueryFilterAttributes extends ESearchBaseQueryFilterAttributes
{
	public function getDisplayInSearchFilter(ESearchOperator $eSearchOperator)
	{
		$ignoreDisplayInSearchQueries = array();
		$eSearchOperatorType= $eSearchOperator->getOperator();
		foreach	($this->ignoreDisplayInSearchValues as $key => $value)
		{
			if ($key == ESearchEntryFieldName::RECYCLED_AT)
			{
				return null;
			}
			elseif ($value)
			{
				$ignoreDisplayInSearchQueries[] = new kESearchTermsQuery($key, $value);
			}
		}

		if (!count($ignoreDisplayInSearchQueries))
		{
			return $this->getMustNotDisplayInSearch();
		}

		$displayInSearchBoolQuery = new kESearchBoolQuery();
		if ($eSearchOperatorType == ESearchOperatorType::NOT_OP)
		{
				$displayInSearchBoolQuery->addQueriesToMustNot($ignoreDisplayInSearchQueries);
		}
		else
		{
			$displayInSearchBoolQuery->addQueriesToShould($ignoreDisplayInSearchQueries);
		}

		return $displayInSearchBoolQuery;
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
