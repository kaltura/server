<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */


class KalturaESearchCategoryAggregationItem extends KalturaESearchAggregationItem
{
	/**
	 *  @var KalturaESearchCategoryAggregateByFieldName
	 */
	public $fieldName;

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new ESearchCategoryAggregationItem();
		return parent::toObject($object_to_fill, $props_to_skip);
	}

	public function getFieldEnumMap()
	{
		return array (KalturaESearchCategoryAggregateByFieldName::CATEGORY_NAME => ESearchCategoryAggregationFieldName::CATEGORY_NAME);
	}

	public function coreToApiResponse($aggregation)
	{

	}

}