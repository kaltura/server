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
			$eSearchItem->searchTerm = substr($eSearchItem->searchTerm, 0, self::MAX_SEARCH_TERM_LENGTH);
			KalturaLog::log("Search term exceeded maximum allowed length, setting search term to [$eSearchItem->searchTerm]");
		}

		if(self::shouldChangePartialToExact($eSearchItem))
		{
			$searchTerm = trim($eSearchItem->searchTerm);
			$searchTerm = substr($searchTerm, 1, -1);
			$object_to_fill->setSearchTerm($searchTerm);
			$object_to_fill->setItemType(KalturaESearchItemType::EXACT_MATCH);
			$props_to_skip[] = 'searchTerm';
			$props_to_skip[] = 'itemType';
		}

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

	private static function shouldChangePartialToExact($eSearchItem)
	{
		$searchTerm = trim($eSearchItem->searchTerm);
		if($eSearchItem->itemType == KalturaESearchItemType::PARTIAL &&
			strlen($searchTerm) > 2 &&
			substr($searchTerm, 0, 1) == '"' &&
			substr($searchTerm,-1) == '"')
			return true;

		return false;
	}

}
