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
			$entryQuery = array();
			//Start handling entry fields
			foreach($entryAllowedFields as $fieldName => $fieldAllowedTypes)
			{
				if (in_array($eSearchUnifiedItem->getItemType(), $fieldAllowedTypes) && in_array('Unified', $fieldAllowedTypes))
				{
					$hasQuery = true;
					$entryQuery[][$queryVerbs[1]][$fieldName] = $eSearchUnifiedItem->getSearchTerm();
				}
			}
			if ($hasQuery)
			{
				$fullEntryQuery['bool']['minimum_should_match'] = 1;
				$fullEntryQuery['bool']['should'] = $entryQuery;
				$outQuery['bool']['should'][] = $fullEntryQuery;
			}

			$hasQuery = false;
			$cuePointQuery = array();
			//Start handling cue-point fields
			foreach($cuePointAllowedFields as $fieldName => $fieldAllowedTypes)
			{
				if (in_array($eSearchUnifiedItem->getItemType(), $fieldAllowedTypes) && in_array('Unified', $fieldAllowedTypes))
				{
					$hasQuery = true;
					$cuePointQuery['nested']['query']['bool']['should'][][$queryVerbs[1]] =  array($fieldName => strtolower($eSearchUnifiedItem->getSearchTerm()));
				}
			}
			if ($hasQuery)
			{
				$cuePointQuery['nested']['path'] = 'cue_points';
				$cuePointQuery['nested']['inner_hits'] = array('size' => 10, '_source' => true);
				$cuePointQuery['nested']['query']['bool']['minimum_should_match'] = 1;
				$outQuery['bool']['should'][] = $cuePointQuery;
			}

			//Start handling caption fields
			$captionQuery['nested']['path'] = 'caption_assets';
			$captionQuery['nested']['query']['nested']['inner_hits'] = array('size' => 10); //TODO: get this parameter from config
			$captionQuery['nested']['inner_hits'] = array('size' => 10, '_source' => false);
			$captionQuery['nested']['query']['nested']['path'] = "caption_assets.lines";

			ESearchCaptionItem::createSingleItemSearchQuery($boolOperator, $eSearchUnifiedItem, $captionQuery);
			$outQuery['bool']['should'][] = $captionQuery;

			//Start handling metadata fields
			$metadataQuery['nested']['path'] = 'metadata';
			$metadataQuery['nested']['inner_hits'] = array('size' => 10, '_source' => true);
			ESearchMetadataItem::createSingleItemQuery($boolOperator, $eSearchUnifiedItem, $metadataQuery);

			$outQuery['bool']['should'][] = $metadataQuery;

		}

		return $outQuery;
	}


}