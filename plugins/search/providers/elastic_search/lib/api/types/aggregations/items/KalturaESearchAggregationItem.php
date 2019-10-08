<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */

abstract class KalturaESearchAggregationItem extends KalturaObject
{
	/**
	 *  @var int
	 */
	public $size;

	public $fieldName;

	private static $map_between_objects = array(
		'size',
		'fieldName'
	);

	abstract public function getFieldEnumMap();

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if($object_to_fill)
		{
			$fieldEnumMap = $this->getFieldEnumMap();
			if (isset($fieldEnumMap[$this->fieldName]))
			{
				$coreFieldName = $fieldEnumMap[$this->fieldName];
				$object_to_fill->setFieldName($coreFieldName);
				$props_to_skip[] = 'fieldName';
			}
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}

	abstract public function coreToApiResponse($coreResponse, $fieldName = null);

	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		if(is_null($this->fieldName))
		{
			throw new KalturaAPIException(KalturaESearchAggregationErrors::AGGREGATION_FIELD_NAME_MUST_BE_SUPPLIED);
		}
		parent::validateForUsage($sourceObject, $propertiesToSkip);
	}
}