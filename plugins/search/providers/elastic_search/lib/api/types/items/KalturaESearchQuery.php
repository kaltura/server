<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchQuery extends KalturaESearchBaseItem
{
	/**
	 * @var string
	 */
	public $eSearchQuery;

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		$apiOperatorOrItem = kESearchQueryParser::buildKESearchParamsFromKESearchQuery($this->eSearchQuery);
		$object_to_fill = $apiOperatorOrItem->toObject();
		//return the core object that represent the query tree of eSearchQuery
		return $object_to_fill;
	}

}
