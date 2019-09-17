<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
abstract class KalturaESearchItemImpl
{

	const MAX_SEARCH_TERM_LENGTH = 128;

	public static function eSearchItemToObjectImpl(&$eSearchItem, $dynamicEnumMap, $itemFieldName, $fieldEnumMap, $object_to_fill = null, $props_to_skip = array())
	{
		if(strlen($eSearchItem->searchTerm) > self::MAX_SEARCH_TERM_LENGTH)
		{
			$eSearchItem->searchTerm =  mb_strcut($eSearchItem->searchTerm, 0, self::MAX_SEARCH_TERM_LENGTH, "utf-8");
			KalturaLog::log("Search term exceeded maximum allowed length, setting search term to [$eSearchItem->searchTerm]");
		}
		$handledObjs = self::handleSearchTerm(trim($eSearchItem->searchTerm), $eSearchItem->itemType, $object_to_fill, $itemFieldName, $props_to_skip);
		if($handledObjs)
		{
			$object_to_fill = $handledObjs[0];
			$props_to_skip = $handledObjs[1];
		}
		list ($object_to_fill, $props_to_skip) = self::handleItemFieldName($object_to_fill, $dynamicEnumMap, $itemFieldName, $eSearchItem, $props_to_skip, $fieldEnumMap);
		return array($object_to_fill, $props_to_skip);
	}

	protected static function handleItemFieldName($object_to_fill, $dynamicEnumMap, $itemFieldName, $eSearchItem, $props_to_skip, $fieldEnumMap)
	{
		if ($object_to_fill instanceof ESearchOperator)
		{
			$searchTermFromObject = $object_to_fill->getSearchItems();
			foreach ($searchTermFromObject as $searchTermFromObj)
			{
				list ($searchTermFromObj, $props_to_skip) = self::handleItemFieldNameHelper($dynamicEnumMap, $itemFieldName, $eSearchItem, $searchTermFromObj, $props_to_skip, $fieldEnumMap);
			}
		}
		else
		{
			list ($object_to_fill, $props_to_skip) = self::handleItemFieldNameHelper($dynamicEnumMap, $itemFieldName, $eSearchItem, $object_to_fill, $props_to_skip, $fieldEnumMap);

		}
		return array($object_to_fill, $props_to_skip);
	}

	protected static function handleItemFieldNameHelper($dynamicEnumMap, $itemFieldName, $eSearchItem, $object_to_fill, $props_to_skip, $fieldEnumMap)
	{
		if(isset($dynamicEnumMap[$itemFieldName]))
		{
			try
			{
				$enumType = call_user_func(array($dynamicEnumMap[$itemFieldName], 'getEnumClass'));
				$SearchTermValue = kPluginableEnumsManager::apiToCore($enumType, $eSearchItem->searchTerm);
				$object_to_fill->setSearchTerm($SearchTermValue);
				$props_to_skip[] = 'searchTerm';
			}
			catch (kCoreException $e)
			{
				if($e->getCode() == kCoreException::ENUM_NOT_FOUND)
					throw new KalturaAPIException(KalturaErrors::INVALID_ENUM_VALUE, $eSearchItem->searchTerm, 'searchTerm', $dynamicEnumMap[$itemFieldName]);
			}

		}

		if(isset($fieldEnumMap[$itemFieldName]))
		{
			$coreFieldName = $fieldEnumMap[$itemFieldName];
			$object_to_fill->setFieldName($coreFieldName);
			$props_to_skip[] = 'fieldName';
		}

		return array($object_to_fill, $props_to_skip);
	}


	private static function shouldChangeToExact($searchTerm, $itemType)
	{
		/*
		 * if itemType is PARTIAL and the searchTerm is wrapped with '"' - change search to EXACT_MATCH and trim '"'
		 * if itemType is EXACT_MATCH and the searchTerm is wrapped with '"' - trim '"'
		 */
		if(in_array($itemType, array(KalturaESearchItemType::PARTIAL, KalturaESearchItemType::EXACT_MATCH)) &&
			strlen($searchTerm) > 2 &&
			substr($searchTerm, 0, 1) == '"' &&
			substr($searchTerm,-1) == '"')
			return true;

		return false;
	}

	protected static function shouldChangeToOperator($searchTerm, $itemType)
	{
		//if itemType is PARTIAL and there are no inner '"' (exclude the first and last chars)
		if($itemType == KalturaESearchItemType::PARTIAL &&
			strlen($searchTerm) > 2 &&
			strpos(substr($searchTerm, 1, -1), '"')!== false)
		{
			return true;
		}
		return false;
	}

	protected static function handleSearchTerm($searchTerm, $itemType, $object_to_fill, $itemFieldName, $props_to_skip)
	{
		if (self::shouldChangeToOperator($searchTerm, $itemType))
		{
			$searchItems = self::handleInnerQuotes($searchTerm, $itemType, $itemFieldName);
			$object_to_fill = new ESearchOperator();
			$object_to_fill->setOperator(ESearchOperatorType::OR_OP);
			$object_to_fill->setSearchItems($searchItems);
		}
		else if(self::shouldChangeToExact($searchTerm, $itemType))
		{
			$searchTerm = substr($searchTerm, 1, -1);
			$object_to_fill->setSearchTerm($searchTerm);
			$object_to_fill->setItemType(KalturaESearchItemType::EXACT_MATCH);
		}
		else
		{
			return null;
		}
		$props_to_skip[] = 'searchTerm';
		$props_to_skip[] = 'itemType';
		return array($object_to_fill, $props_to_skip);
	}

	protected static function handleInnerQuotes($searchTerm, $itemType, $itemFieldName)
	{
		$searchTermParts = explode(' ', $searchTerm);
		$searchItemsArray = array();
		foreach ($searchTermParts as $searchTermPart)
		{
			$searchTermPart = trim($searchTermPart);
			if ($searchTermPart)
			{
				if (self::shouldChangeToExact($searchTermPart, $itemType))
				{
					$searchItemsArray [] = self::addSearchItem($itemFieldName, KalturaESearchItemType::EXACT_MATCH, substr($searchTermPart, 1, -1));
				}
				else
				{
					$searchItemsArray [] =  self::addSearchItem($itemFieldName, KalturaESearchItemType::PARTIAL, $searchTermPart);
				}
			}
		}
		return $searchItemsArray;
	}

	protected static function addSearchItem($itemFieldName, $itemType, $searchTermPart)
	{
		$searchItem = new ESearchEntryItem();
		$searchItem->setFieldName($itemFieldName);
		$searchItem->setItemType($itemType);
		$searchItem->setSearchTerm($searchTermPart);
		return $searchItem;
	}
}
