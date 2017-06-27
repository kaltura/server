<?php

class ESearchUnifiedItem extends ESearchItem
{

	/**
	 * @var string
	 */
	protected $searchTerm;

	/**
	 * @return string
	 */
	public function getSearchTerm()
	{
		return $this->searchTerm;
	}

	/**
	 * @param string $searchTerm
	 */
	public function setSearchTerm($searchTerm)
	{
		$this->searchTerm = $searchTerm;
	}

	public function getType()
	{
		return 'unified';
	}

	public static function createSearchQuery(array $eSearchItemsArr, $boolOperator, $additionalParams = null)
	{
		$outQuery['bool']['must'] = array();
		$outQuery = array();
		$entryAllowedFields = ESearchEntryItem::getAallowedSearchTypesForField();
		$cuePointAllowedFields = ESearchCuePointItem::getAallowedSearchTypesForField();
		foreach($eSearchItemsArr as $eSearchUnifiedItem)
		{
			/** @var ESearchUnifiedItem $eSearchUnifiedItem */
			$queryVerbs = $eSearchUnifiedItem->getQueryVerbs();
			$hasQuery = false;
			$innerQuery = array();
			foreach($entryAllowedFields as $fieldName => $fieldAllowedTypes)
			{
				if (in_array($eSearchUnifiedItem->getItemType(), $fieldAllowedTypes) && in_array('Unified', $fieldAllowedTypes))
				{
					$hasQuery = true;
					$innerQuery[][$queryVerbs[1]][$fieldName] = $eSearchUnifiedItem->getSearchTerm();
				}
			}
			if ($hasQuery)
			{
				$fullInnerQuery['bool']['minimum_should_match'] = 1;
				$fullInnerQuery['bool']['should'] = $innerQuery;
				$outQuery['bool']['should'][] = $fullInnerQuery;
			}

			$hasQuery = false;
			$nestedQuery = array();
			foreach($cuePointAllowedFields as $fieldName => $fieldAllowedTypes)
			{
				if (in_array($eSearchUnifiedItem->getItemType(), $fieldAllowedTypes) && in_array('Unified', $fieldAllowedTypes))
				{
					$hasQuery = true;
					$nestedQuery['nested']['query']['bool']['should'][][$queryVerbs[1]] =  array($fieldName => strtolower($eSearchUnifiedItem->getSearchTerm()));
				}
			}
			if ($hasQuery)
			{
				$nestedQuery['nested']['path'] = 'cue_points';
				$nestedQuery['nested']['inner_hits'] = array('size' => 10, '_source' => true);
				$nestedQuery['nested']['query']['bool']['minimum_should_match'] = 1;
				$outQuery['bool']['should'][] = $nestedQuery;
			}

		}

		return $outQuery;
	}


}