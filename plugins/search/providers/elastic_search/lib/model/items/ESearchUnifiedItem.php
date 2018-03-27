<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchUnifiedItem extends ESearchItem
{

	const UNIFIED = 'unified';
	const KUSER_ID_THAT_DOESNT_EXIST = -1;

	/**
	 * @var string
	 */
	protected $searchTerm;

	private static $entryItemUserFields = array(
		ESearchEntryFieldName::CREATOR_ID,
		ESearchEntryFieldName::ENTITLED_USER_EDIT,
		ESearchEntryFieldName::ENTITLED_USER_PUBLISH,
		ESearchEntryFieldName::USER_ID,
	);

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
			$subQuery = new kESearchBoolQuery();

			self::addEntryFieldsToUnifiedQuery($eSearchUnifiedItem, $subQuery, $queryAttributes);
			self::addCategoryEntryFieldsToUnifiedQuery($eSearchUnifiedItem, $subQuery, $queryAttributes);
			self::addCuePointFieldsToUnifiedQuery($eSearchUnifiedItem, $subQuery, $queryAttributes);
			self::addCaptionFieldsToUnifiedQuery($eSearchUnifiedItem, $subQuery, $queryAttributes);
			self::addMetadataFieldsToUnifiedQuery($eSearchUnifiedItem, $subQuery, $queryAttributes);

			$outQuery[] = $subQuery;
		}

		return $outQuery;
	}

	private static function addEntryFieldsToUnifiedQuery($eSearchUnifiedItem, &$entryUnifiedQuery, &$queryAttributes)
	{
		$entryItems = array();
		$entryAllowedFields = ESearchEntryItem::getAllowedSearchTypesForField();
		//Start handling entry fields
		$fetchKuser = true;
		$kuserId = null;
		foreach($entryAllowedFields as $fieldName => $fieldAllowedTypes)
		{
			if (in_array($eSearchUnifiedItem->getItemType(), $fieldAllowedTypes) && in_array(self::UNIFIED, $fieldAllowedTypes))
			{
				$entryItem = new ESearchEntryItem();
				$entryItem->setFieldName($fieldName);
				$searchTerm = $eSearchUnifiedItem->getSearchTerm();

				if(in_array($fieldName, self::$entryItemUserFields))
				{
					if($fetchKuser)
					{
						$kuserId = self::KUSER_ID_THAT_DOESNT_EXIST;
						$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::getCurrentPartnerId(), $eSearchUnifiedItem->getSearchTerm(), true);
						if($kuser)
							$kuserId = $kuser->getId();
						$fetchKuser = false;
					}
					$searchTerm = $kuserId;
				}

				$entryItem->setSearchTerm($searchTerm);
				$entryItem->setItemType($eSearchUnifiedItem->getItemType());
				$entryItem->setAddHighlight($eSearchUnifiedItem->getAddHighlight());
				if($eSearchUnifiedItem->getItemType() == ESearchItemType::RANGE)
					$entryItem->setRange($eSearchUnifiedItem->getRange());
				$entryItems[] = $entryItem;
			}
		}

		if(count($entryItems))
		{
			$entryQueries = ESearchEntryItem::createSearchQuery($entryItems, 'should', $queryAttributes,  null);
			foreach ($entryQueries as $entryQuery)
			{
				$entryUnifiedQuery->addToShould($entryQuery);
			}
		}

	}

	private static function addCategoryEntryFieldsToUnifiedQuery($eSearchUnifiedItem, &$entryUnifiedQuery, &$queryAttributes)
	{
		$categoryEntryItems = array();
		$categoryEntryNameAllowedFields = ESearchCategoryEntryNameItem::getAllowedSearchTypesForField();


		foreach($categoryEntryNameAllowedFields as $fieldName => $fieldAllowedTypes)
		{
			if (in_array($eSearchUnifiedItem->getItemType(), $fieldAllowedTypes) && in_array(self::UNIFIED, $fieldAllowedTypes))
			{
				$categoryEntryItem = new ESearchCategoryEntryNameItem();
				$categoryEntryItem->setFieldName($fieldName);
				$categoryEntryItem->setSearchTerm($eSearchUnifiedItem->getSearchTerm());
				$categoryEntryItem->setItemType($eSearchUnifiedItem->getItemType());
				$categoryEntryItem->setAddHighlight($eSearchUnifiedItem->getAddHighlight());
				$categoryEntryItems[] = $categoryEntryItem;
			}
		}

		$categoryEntryAncestorNameAllowedFields = ESearchCategoryEntryAncestorNameItem::getAllowedSearchTypesForField();
		foreach($categoryEntryAncestorNameAllowedFields as $fieldName => $fieldAllowedTypes)
		{
			if (in_array($eSearchUnifiedItem->getItemType(), $fieldAllowedTypes) && in_array(self::UNIFIED, $fieldAllowedTypes))
			{
				$categoryEntryItem = new ESearchCategoryEntryAncestorNameItem();
				$categoryEntryItem->setFieldName($fieldName);
				$categoryEntryItem->setSearchTerm($eSearchUnifiedItem->getSearchTerm());
				$categoryEntryItem->setItemType($eSearchUnifiedItem->getItemType());
				$categoryEntryItem->setAddHighlight($eSearchUnifiedItem->getAddHighlight());

				$categoryEntryItems[] = $categoryEntryItem;
			}
		}

		if(count($categoryEntryItems))
		{
			$categoryEntryQueries = ESearchBaseCategoryEntryItem::createSearchQuery($categoryEntryItems, 'should', $queryAttributes,  null);
			foreach ($categoryEntryQueries as $categoryEntryQuery)
			{
				$entryUnifiedQuery->addToShould($categoryEntryQuery);
			}
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
				$cuePointItem->setAddHighlight($eSearchUnifiedItem->getAddHighlight());
				if($eSearchUnifiedItem->getItemType() == ESearchItemType::RANGE)
					$cuePointItem->setRange($eSearchUnifiedItem->getRange());
				$cuePointItems[] = $cuePointItem;
			}
		}

		if(count($cuePointItems))
		{
			$cuePointQueries = ESearchCuePointItem::createSearchQuery($cuePointItems, 'should', $queryAttributes, null);
			foreach ($cuePointQueries as $cuePointQuery)
			{
				$entryUnifiedQuery->addToShould($cuePointQuery);
			}
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
				$captionItem->setAddHighlight($eSearchUnifiedItem->getAddHighlight());
				if($eSearchUnifiedItem->getItemType() == ESearchItemType::RANGE)
					$captionItem->setRange($eSearchUnifiedItem->getRange());
				$captionItems[] = $captionItem;
			}
		}

		if(count($captionItems))
		{
			$captionQueries = ESearchCaptionItem::createSearchQuery($captionItems, 'should', $queryAttributes, null);
			foreach ($captionQueries as $captionQuery)
			{
				$entryUnifiedQuery->addToShould($captionQuery);
			}
		}

	}

	private static function addMetadataFieldsToUnifiedQuery($eSearchUnifiedItem, &$entryUnifiedQuery, &$queryAttributes)
	{
		//metadata is special case - we don't need to check for allowed field types
		$metadataItems = array();
		$metadataItem = new ESearchMetadataItem();
		$metadataItem->setSearchTerm($eSearchUnifiedItem->getSearchTerm());
		$metadataItem->setItemType($eSearchUnifiedItem->getItemType());
		$metadataItem->setAddHighlight($eSearchUnifiedItem->getAddHighlight());
		if($eSearchUnifiedItem->getItemType() == ESearchItemType::RANGE)
			$metadataItem->setRange($eSearchUnifiedItem->getRange());
		$metadataItems[] = $metadataItem;

		if(count($metadataItems))
		{
			$metadataQueries = ESearchMetadataItem::createSearchQuery($metadataItems, 'should', $queryAttributes, null);
			foreach ($metadataQueries as $metadataQuery)
			{
				$entryUnifiedQuery->addToShould($metadataQuery);
			}
		}
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