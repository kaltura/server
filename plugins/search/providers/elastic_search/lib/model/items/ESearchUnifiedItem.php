<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchUnifiedItem extends ESearchItem
{

	const UNIFIED = 'unified';
	
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
		return self::UNIFIED;
	}

	public static function createSearchQuery(array $eSearchItemsArr, $boolOperator, $eSearchOperatorType = null)
	{
		$outQuery = array();

		$entryAllowedFields = ESearchEntryItem::getAllowedSearchTypesForField();
		foreach($eSearchItemsArr as $eSearchUnifiedItem)
		{
			$subQuery = array();
			$entryUnifiedQuery = array();
			/** @var ESearchUnifiedItem $eSearchUnifiedItem */
			$entryItems = array();
			//Start handling entry fields
			foreach($entryAllowedFields as $fieldName => $fieldAllowedTypes)
			{
				if (in_array($eSearchUnifiedItem->getItemType(), $fieldAllowedTypes) && in_array(self::UNIFIED, $fieldAllowedTypes))
				{
					$entryItem = new ESearchEntryItem();
					$entryItem->setFieldName($fieldName);
					$entryItem->setSearchTerm($eSearchUnifiedItem->getSearchTerm());
					$entryItem->setItemType($eSearchUnifiedItem->getItemType());
					$entryItems[] = $entryItem;
				}
			}

			if(count($entryItems))
			{
				$entryUnifiedQuery = ESearchEntryItem::createSearchQuery($entryItems, 'should', null);
			}


			$cuePointAllowedFields = ESearchCuePointItem::getAllowedSearchTypesForField();
			$cuePointItems = array();
			//Start handling cue-point fields
			foreach($cuePointAllowedFields as $fieldName => $fieldAllowedTypes)
			{
				if (in_array($eSearchUnifiedItem->getItemType(), $fieldAllowedTypes) && in_array(self::UNIFIED, $fieldAllowedTypes))
				{
					$cuePointItem = new ESearchCuePointItem();
					$cuePointItem->setFieldName($fieldName);
					$cuePointItem->setSearchTerm($eSearchUnifiedItem->getSearchTerm());
					$cuePointItem->setItemType($eSearchUnifiedItem->getItemType());
					$cuePointItems[] = $cuePointItem;
				}
			}

			if(count($cuePointItems))
			{
				$cuePointQuery = ESearchCuePointItem::createSearchQuery($cuePointItems, 'should', null);
				if(count($cuePointQuery))
					$entryUnifiedQuery[] = $cuePointQuery;
			}

			//Start handling caption fields
			$captionItems = array();
			$captionAllowedFields = ESearchCaptionItem::getAllowedSearchTypesForField();
			foreach($captionAllowedFields as $fieldName => $fieldAllowedTypes)
			{
				if (in_array($eSearchUnifiedItem->getItemType(), $fieldAllowedTypes) && in_array(self::UNIFIED, $fieldAllowedTypes))
				{
					$captionItem = new ESearchCaptionItem();
					$captionItem->setFieldName($fieldName);
					$captionItem->setSearchTerm($eSearchUnifiedItem->getSearchTerm());
					$captionItem->setItemType($eSearchUnifiedItem->getItemType());
					$captionItems[] = $captionItem;
				}
			}

			if(count($captionItems))
			{
				$captionQuery = ESearchCaptionItem::createSearchQuery($captionItems, 'should', null);
				if(count($captionQuery))
					$entryUnifiedQuery[] = $captionQuery;
			}


			
			$metadataItems = array();
			$metadataAllowedFields = ESearchMetadataItem::getAllowedSearchTypesForField();
			foreach($metadataAllowedFields as $fieldName => $fieldAllowedTypes)
			{
				if (in_array($eSearchUnifiedItem->getItemType(), $fieldAllowedTypes) && in_array(self::UNIFIED, $fieldAllowedTypes))//todo
				{
					$metadataItem = new ESearchMetadataItem();
					$metadataItem->setSearchTerm($eSearchUnifiedItem->getSearchTerm());
					$metadataItem->setItemType($eSearchUnifiedItem->getItemType());
					$metadataItems[] = $metadataItem;
				}
			}

			if(count($metadataItems))
			{
				$metadataQuery = ESearchMetadataItem::createSearchQuery($metadataItems, 'should', null);
				if(count($metadataQuery))
					$entryUnifiedQuery[] = $metadataQuery;
			}

			
			if(count($entryUnifiedQuery))
			{
				$subQuery['bool']['should'] = $entryUnifiedQuery;
				$subQuery['bool']['minimum_should_match'] = 1;
				$outQuery[] = $subQuery;
			}
			
		}

		return $outQuery;
	}

}