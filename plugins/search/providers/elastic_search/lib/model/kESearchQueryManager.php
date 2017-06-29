<?php


class kESearchQueryManager
{
	public static function createOperatorSearchQuery(ESearchOperator $eSearchOperator)
	{
		if (!count($eSearchOperator->getSearchItems()))
		{
			return array();
		}
		$additionalParams = array();
		switch ($eSearchOperator->getOperator())
		{
			case ESearchOperatorType::AND_OP:
				$boolOperator = 'must';
				break;
			case ESearchOperatorType::OR_OP:
				$boolOperator = 'should';
				$additionalParams['minimum_should_match'] = 1;
				break;
			default:
				KalturaLog::crit('unknown operator type');
				return null;
		}
		$outQuery = array();
		$categorizedSearchItems = array();
		//categorize each different search item by type.
		foreach ($eSearchOperator->getSearchItems() as $searchItem)
		{
			/**
			 * @var ESearchItem $searchItem
			 */
			$className = get_class($searchItem);
			if (!isset($categorizedSearchItems[$className]))
				$categorizedSearchItems[$className] = array();
			$categorizedSearchItems[$className][] = $searchItem;
		}

		foreach($categorizedSearchItems as $className => $searchItems)
		{
			$outQuery['bool']['must'][] = $className::createSearchQuery($searchItems, $boolOperator, $additionalParams);
		}

		return $outQuery;
	}
}


?>



