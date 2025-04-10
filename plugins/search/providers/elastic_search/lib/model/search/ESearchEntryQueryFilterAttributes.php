<?php
///**
//* @package plugins.elasticSearch
//* @subpackage model.search
//*/
//class ESearchEntryQueryFilterAttributes extends ESearchBaseQueryFilterAttributes
//{
//		public function getDisplayInSearchFilter(ESearchOperator $eSearchOperator)
//		{
//			$recycleDisplayInSearch = false;
//			$ignoreDisplayInSearchQueries = array();
//			foreach($this->ignoreDisplayInSearchValues as $key => $value)
//			{
//				if ($key == ESearchEntryFieldName::RECYCLED_AT)
//				{
//					return null;
//				}
//				elseif ($value)
//				{
//					$ignoreDisplayInSearchQueries[] = new kESearchTermsQuery($key, $value);
//					if ($key == ESearchEntryFieldName::DISPLAY_IN_SEARCH)
//					{
//						if (in_array(EntryDisplayInSearchType::RECYCLED, $value))
//							{
//								$recycleDisplayInSearch = true;
//							}
//					}
//				}
//			}
//		if (!count($ignoreDisplayInSearchQueries))
//		{
//			return $this->getMustNotDisplayInSearch();
//		}
//
//		if ($recycleDisplayInSearch)
//		{
//			$excludedSystemDisplayInSearch = array(EntryDisplayInSearchType::SYSTEM);
//			$systemDisplayInSearchQuery = new kESearchTermsQuery(ESearchEntryFieldName::DISPLAY_IN_SEARCH, $excludedSystemDisplayInSearch);
//			$mustNotDisplayInSearchSystem = new kESearchBoolQuery();
//			$mustNotDisplayInSearchSystem->addToMustNot($systemDisplayInSearchQuery);
//			return $mustNotDisplayInSearchSystem;
//		}
//		else
//		{
//			$displayInSearchBoolQuery = new kESearchBoolQuery();
//			$displayInSearchBoolQuery->addToShould($this->getMustNotDisplayInSearch());
//			return $displayInSearchBoolQuery;
//		}
//	}
//	protected function getMustNotDisplayInSearch()
//	{
//		$excludedDisplayInSearchStatuses = array(EntryDisplayInSearchType::RECYCLED, EntryDisplayInSearchType::SYSTEM);
//		$displayInSearchQuery = new kESearchTermsQuery(ESearchEntryFieldName::DISPLAY_IN_SEARCH, $excludedDisplayInSearchStatuses);
//		$mustNotDisplayInSearchBoolQuery = new kESearchBoolQuery();
//		$mustNotDisplayInSearchBoolQuery->addToMustNot($displayInSearchQuery);
//		return $mustNotDisplayInSearchBoolQuery;
//	}
//}

/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class ESearchEntryQueryFilterAttributes extends ESearchBaseQueryFilterAttributes
{
	public function getDisplayInSearchFilter(ESearchOperator $eSearchOperator)
	{
		$queryContainsDisplayInSearch = false;
		$mustIgnoreDisplayInSearchQueries = array();
		$shouldIgnoreDisplayInSearchQueries = array();
		$mustNotIgnoreDisplayInSearchQueries = array();

		foreach ($this->ignoreDisplayInSearchValues as $key => $value)
		{
			if ($key == ESearchEntryFieldName::RECYCLED_AT)
			{
				return null;
			}
			elseif ($value)
			{
				$eSearchOperatorType= $eSearchOperator->getOperator();
				switch ($eSearchOperatorType) {
					case ESearchOperatorType::AND_OP:
						$mustIgnoreDisplayInSearchQueries[] = new kESearchTermsQuery($key, $value);
						break;
					case ESearchOperatorType::OR_OP:
						$shouldIgnoreDisplayInSearchQueries[] = new kESearchTermsQuery($key, $value);
						break;
					case ESearchOperatorType::NOT_OP:
						$mustNotIgnoreDisplayInSearchQueries[] = new kESearchTermsQuery($key, $value);
						break;
					default:
						throw new kESearchException('Missing operator type', kESearchException::MISSING_OPERATOR_TYPE);
				}
				if ($key == ESearchEntryFieldName::DISPLAY_IN_SEARCH)
				{
					$queryContainsDisplayInSearch = true;
				}
			}
		}

		if (!(count($mustIgnoreDisplayInSearchQueries) + count($shouldIgnoreDisplayInSearchQueries) + count($mustNotIgnoreDisplayInSearchQueries)))
		{
			return $this->getMustNotDisplayInSearch();
		}

		$displayInSearchBoolQuery = new kESearchBoolQuery();
		$this->addQueryIfNotEmpty($shouldIgnoreDisplayInSearchQueries, 'addQueriesToShould', $displayInSearchBoolQuery);
		$this->addQueryIfNotEmpty($mustIgnoreDisplayInSearchQueries, 'addToMust', $displayInSearchBoolQuery);
		$this->addQueryIfNotEmpty($mustNotIgnoreDisplayInSearchQueries, 'addToMustNot', $displayInSearchBoolQuery);
		if (!$queryContainsDisplayInSearch)
		{
			$displayInSearchBoolQuery->addToShould($this->getMustNotDisplayInSearch());
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

	protected function addQueryIfNotEmpty($array, $method, $queryObj)
	{
		if (!empty($array))
		{
			$queryObj->$method($array);
		}
	}
}
