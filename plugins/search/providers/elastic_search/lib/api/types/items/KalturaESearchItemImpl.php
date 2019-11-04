<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
abstract class KalturaESearchItemImpl
{

	const MAX_SEARCH_TERM_LENGTH = 128;
	const ENCLOSED_WITH_DOUBLE_QUOTATION_MARK_REGEX =  '/(\"){1}[^\"]+(\"){1}/';
	const QUOTATION_MARK_BREAKER_REGEX = '/(\"){1}[^\"]+(\"){1}|[^\"]*/';

	public static function eSearchItemToObjectImpl(&$eSearchItem, $dynamicEnumMap, $itemFieldName, $fieldEnumMap, $object_to_fill = null, $props_to_skip = array())
	{
		self::validateSearchTermLength($eSearchItem);
		$searchTerm = trim($eSearchItem->searchTerm);
		if(in_array($eSearchItem->itemType, array(KalturaESearchItemType::PARTIAL, KalturaESearchItemType::EXACT_MATCH)) &&
			self::enclosedInQuotationMarks($searchTerm))
		{
			$searchTerm  = substr($searchTerm, 1, -1);
			$object_to_fill->setItemType(KalturaESearchItemType::EXACT_MATCH);
			$props_to_skip[] = 'itemType';
		}
		list ($object_to_fill, $props_to_skip) = self::addSubTermToObj($object_to_fill, $props_to_skip, $searchTerm);
		list ($object_to_fill, $props_to_skip) = self::handleItemFieldNameHelper($dynamicEnumMap, $itemFieldName, $eSearchItem, $object_to_fill, $props_to_skip, $fieldEnumMap);
		return array($object_to_fill, $props_to_skip);
	}

	public static function eSearchComplexItemToObjectImpl(&$eSearchItem, $dynamicEnumMap, $itemFieldName, $fieldEnumMap, $object_to_fill = null, $props_to_skip = array())
	{
		self::validateSearchTermLength($eSearchItem);
		list($object_to_fill, $props_to_skip) = self::handleSearchTerm($eSearchItem->searchTerm, $eSearchItem->itemType, $object_to_fill, $itemFieldName, $props_to_skip);
		return self::handleItemFieldName($object_to_fill, $dynamicEnumMap, $itemFieldName, $eSearchItem, $props_to_skip, $fieldEnumMap);
	}

	protected static function validateSearchTermLength($eSearchItem)
	{
		if(strlen($eSearchItem->searchTerm) > self::MAX_SEARCH_TERM_LENGTH)
		{
			$eSearchItem->searchTerm =  mb_strcut($eSearchItem->searchTerm, 0, self::MAX_SEARCH_TERM_LENGTH, "utf-8");
			KalturaLog::log("Search term exceeded maximum allowed length, setting search term to [$eSearchItem->searchTerm]");
		}
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
				$searchTerm = $object_to_fill->getSearchTerm();
				$SearchTermValue = kPluginableEnumsManager::apiToCore($enumType, $searchTerm);
				$object_to_fill->setSearchTerm($SearchTermValue);
				$props_to_skip[] = 'searchTerm';
			}
			catch (kCoreException $e)
			{
				if($e->getCode() == kCoreException::ENUM_NOT_FOUND)
					throw new KalturaAPIException(KalturaErrors::INVALID_ENUM_VALUE, $searchTerm, 'searchTerm', $dynamicEnumMap[$itemFieldName]);
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
		if(preg_match_all(self::ENCLOSED_WITH_DOUBLE_QUOTATION_MARK_REGEX, $searchTerm, $matches))
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
				$searchTerm  = substr($searchTerm, 1, -1);
			}
			list ($object_to_fill, $props_to_skip) = self::addSubTermToObj($object_to_fill, $props_to_skip, $searchTerm);
			return array($object_to_fill, $props_to_skip);
		}
		else if ($itemType === KalturaESearchItemType::PARTIAL && preg_match_all(self::QUOTATION_MARK_BREAKER_REGEX, $searchTerm, $matches))
		{
			$searchItems = self::handleMatches($matches[0], $itemFieldName, $object_to_fill);
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

	protected static function handleMatches($matches, $itemFieldName, $object_to_fill)
	{
		$searchItemsArray = array();
		foreach ($matches as $match)
		{
			$match = trim($match);
			if ($match)
			{
				if (self::enclosedInQuotationMarks($match))
				{
					$searchItemsArray [] = self::addSearchItem($itemFieldName, KalturaESearchItemType::EXACT_MATCH, substr($match, 1, -1), $object_to_fill);
				}
				else
				{
					$searchItemsArray [] =  self::addSearchItem($itemFieldName, KalturaESearchItemType::PARTIAL, $match, $object_to_fill);
				}
			}
		}
		return $searchItemsArray;
	}

	protected static function addSearchItem($itemFieldName, $itemType, $searchTermPart, $object_to_fill)
	{
		if (!$object_to_fill)
		{
			return null;
		}
		$className = get_class($object_to_fill);
		$searchItem = new $className();
		if($itemFieldName)
		{
			$searchItem->setFieldName($itemFieldName);
		}
		$searchItem->setItemType($itemType);
		$searchItem->setSearchTerm($searchTermPart);
		return $searchItem;
	}
}
