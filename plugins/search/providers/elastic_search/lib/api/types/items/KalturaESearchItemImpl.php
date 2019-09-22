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
		list($object_to_fill, $props_to_skip) = self::handleSearchTerm($eSearchItem->searchTerm, $eSearchItem->itemType, $object_to_fill, $itemFieldName, $props_to_skip);
		return self::handleItemFieldName($object_to_fill, $dynamicEnumMap, $itemFieldName, $eSearchItem, $props_to_skip, $fieldEnumMap);
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
		return array($object_to_fill, array_unique($props_to_skip));
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


	protected static function enclosedInQuotationMarks($searchTerm)
	{
		/*
		 * if searchTerm is wrapped with '"' - return true
		 */
		if(preg_match_all('/(\'|\"){1}[^\'\"]+(\'|\"){1}/',$searchTerm, $matches))
		{
			return true;
		}
		return false;
	}

	protected static function handleSearchTerm($searchTerm, $itemType, $object_to_fill, $itemFieldName, $props_to_skip)
	{
		if ($itemType === KalturaESearchItemType::EXACT_MATCH)
		{
			if(self::enclosedInQuotationMarks($searchTerm))
			{
				list ($object_to_fill, $props_to_skip) = self::addSubTermToObj($object_to_fill, $props_to_skip, substr($searchTerm, 1, -1));
			}
			return array($object_to_fill, $props_to_skip);
		}
		else if ($itemType === KalturaESearchItemType::PARTIAL && preg_match_all('/(\'|\"){1}[^\'\"]+(\'|\"){1}|[^\'\"]*/', $searchTerm, $matches))
		{
			$searchItems = self::handleMatches($matches[0], $itemFieldName);
			if ($searchItems)
			{
				list ($object_to_fill, $props_to_skip) = self::addOperator($props_to_skip, $searchItems);
			}
		}
		return array($object_to_fill, $props_to_skip);
	}

	protected static function addSubTermToObj($object_to_fill, $props_to_skip, $searchTerm)
	{
		$object_to_fill->setSearchTerm($searchTerm);
		$props_to_skip[] = 'searchTerm';
		return array($object_to_fill, $props_to_skip);
	}

	protected static function addOperator($props_to_skip, $searchItems)
	{
		$object_to_fill = new ESearchOperator();
		$object_to_fill->setOperator(ESearchOperatorType::OR_OP);
		$object_to_fill->setSearchItems($searchItems);
		$props_to_skip[] = 'searchTerm';
		$props_to_skip[] = 'itemType';
		return array($object_to_fill, $props_to_skip);
	}

	protected static function handleMatches($matches, $itemFieldName)
	{
		$searchItemsArray = array();
		foreach ($matches as $match)
		{
			$match = trim($match);
			if ($match)
			{
				if (self::enclosedInQuotationMarks($match))
				{
					$searchItemsArray [] = self::addSearchItem($itemFieldName, KalturaESearchItemType::EXACT_MATCH, substr($match, 1, -1));
				}
				else
				{
					$searchItemsArray [] =  self::addSearchItem($itemFieldName, KalturaESearchItemType::PARTIAL, $match);
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
