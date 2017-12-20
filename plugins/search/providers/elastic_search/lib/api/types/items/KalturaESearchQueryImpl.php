<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
abstract class KalturaESearchQueryImpl
{

	public static function eSearchItemToObjectImpl(&$eSearchQuery, $object_to_fill = null, $props_to_skip = array())
	{
		$apiOperatorOrItem = kESearchQueryParser::buildKESearchParamsFromKESearchQuery($eSearchQuery);
		$object_to_fill = $apiOperatorOrItem->toObject();
		//return the core object that represent the query tree of eSearchQuery
		return $object_to_fill;
	}

}