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

	public static function createSearchQuery($eSearchItemsArr, $boolOperator, &$queryAttributes, $eSearchOperatorType = null)
	{
		$outQuery = array();

		foreach($eSearchItemsArr as $eSearchUnifiedItem)
		{
			self::validateUnifiedAllowedTypes($eSearchUnifiedItem);
			$subQuery = array();
			$entryUnifiedQuery = array();
			self::addEntryFieldsToUnifiedQuery($eSearchUnifiedItem, $entryUnifiedQuery, $queryAttributes);
			self::addCuePointFieldsToUnifiedQuery($eSearchUnifiedItem,$entryUnifiedQuery, $queryAttributes);
			self::addCaptionFieldsToUnifiedQuery($eSearchUnifiedItem,$entryUnifiedQuery, $queryAttributes);
			self::addMetadataFieldsToUnifiedQuery($eSearchUnifiedItem,$entryUnifiedQuery, $queryAttributes);
			
			if(count($entryUnifiedQuery))
			{
				$subQuery['bool']['should'] = $entryUnifiedQuery;
				$subQuery['bool']['minimum_should_match'] = 1;
				$outQuery[] = $subQuery;
			}
			
		}

		return $outQuery;
	}

	private static function addEntryFieldsToUnifiedQuery($eSearchUnifiedItem, &$entryUnifiedQuery, &$queryAttributes)
	{
		$entryItems = array();
		$entryAllowedFields = ESearchEntryItem::getAllowedSearchTypesForField();
		//Start handling entry fields
		foreach($entryAllowedFields as $fieldName => $fieldAllowedTypes)
		{
			if (in_array($eSearchUnifiedItem->getItemType(), $fieldAllowedTypes) && in_array(self::UNIFIED, $fieldAllowedTypes))
			{
				$entryItem = new ESearchEntryItem();
				$entryItem->setFieldName($fieldName);
				$entryItem->setSearchTerm($eSearchUnifiedItem->getSearchTerm());
				$entryItem->setItemType($eSearchUnifiedItem->getItemType());
				if($eSearchUnifiedItem->getItemType() == ESearchItemType::RANGE)
					$entryItem->setRange($eSearchUnifiedItem->getRange());
				$entryItems[] = $entryItem;
			}
		}

		if(count($entryItems))
		{
			$entryUnifiedQuery = ESearchEntryItem::createSearchQuery($entryItems, 'should', $queryAttributes,  null);
		}
	}

	private static function addCuePointFieldsToUnifiedQuery($eSearchUnifiedItem, &$entryUnifiedQuery, &$queryAttributes)
	{
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
				if($eSearchUnifiedItem->getItemType() == ESearchItemType::RANGE)
					$cuePointItem->setRange($eSearchUnifiedItem->getRange());
				$cuePointItems[] = $cuePointItem;
			}
		}

		if(count($cuePointItems))
		{
			$cuePointQuery = ESearchCuePointItem::createSearchQuery($cuePointItems, 'should', $queryAttributes, null);
			if(count($cuePointQuery))
				$entryUnifiedQuery[] = $cuePointQuery;
		}
	}

	private static function addCaptionFieldsToUnifiedQuery($eSearchUnifiedItem, &$entryUnifiedQuery, &$queryAttributes)
	{
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
				if($eSearchUnifiedItem->getItemType() == ESearchItemType::RANGE)
					$captionItem->setRange($eSearchUnifiedItem->getRange());
				$captionItems[] = $captionItem;
			}
		}

		if(count($captionItems))
		{
			$captionQuery = ESearchCaptionItem::createSearchQuery($captionItems, 'should', $queryAttributes, null);
			if(count($captionQuery))
				$entryUnifiedQuery[] = $captionQuery;
		}
	}

	private static function addMetadataFieldsToUnifiedQuery($eSearchUnifiedItem, &$entryUnifiedQuery, &$queryAttributes)
	{
		//metadata is special case - we don't need to check for allowed field types
		$metadataItems = array();
		$metadataItem = new ESearchMetadataItem();
		$metadataItem->setSearchTerm($eSearchUnifiedItem->getSearchTerm());
		$metadataItem->setItemType($eSearchUnifiedItem->getItemType());
		if($eSearchUnifiedItem->getItemType() == ESearchItemType::RANGE)
			$metadataItem->setRange($eSearchUnifiedItem->getRange());
		$metadataItems[] = $metadataItem;

		$metadataQuery = ESearchMetadataItem::createSearchQuery($metadataItems, 'should', $queryAttributes, null);
		if(count($metadataQuery))
			$entryUnifiedQuery[] = $metadataQuery;
	}

	protected static function validateUnifiedAllowedTypes($eSearchUnifiedItem)
	{
		if (in_array($eSearchUnifiedItem->getItemType(), array(ESearchItemType::RANGE, ESearchItemType::EXISTS)))
		{
			$data = array();
			$data['itemType'] = $eSearchUnifiedItem->getItemType();
			throw new kESearchException('Item type ['.$eSearchUnifiedItem->getItemType().']. is not allowed in Unified Search', kESearchException::SEARCH_TYPE_NOT_ALLOWED_ON_UNIFIED_SEARCH, $data);
		}
	}

	public function shouldAddLanguageSearch()
	{

	}

	public function getItemMappingFieldsDelimiter()
	{

	}

}