<?php
/**
 * @package plugins.beacon
 * @subpackage api.objects
 */
class KalturaBeaconSearchScheduledResourceOrderByItem extends KalturaESearchOrderByItem
{
	/**
	 *  @var KalturaBeaconScheduledResourceOrderByFieldName
	 */
	public $sortField;

	private static $map_between_objects = array(
		'sortField',
	);

	private static $map_field_enum = array(
		KalturaBeaconScheduledResourceOrderByFieldName::STATUS => BeaconScheduledResourceOrderByFieldName::STATUS,
		KalturaBeaconScheduledResourceOrderByFieldName::RECORDING => BeaconScheduledResourceOrderByFieldName::RECORDING,
		KalturaBeaconScheduledResourceOrderByFieldName::RESOURCE_NAME => BeaconScheduledResourceOrderByFieldName::RESOURCE_NAME,
		KalturaBeaconScheduledResourceOrderByFieldName::UPDATED_AT => BeaconScheduledResourceOrderByFieldName::UPDATED_AT,
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new kBeaconScheduledResourceOrderByItem();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}

	public function getFieldEnumMap()
	{
		return self::$map_field_enum;
	}

	public function getItemFieldName()
	{
		return $this->sortField;
	}

}
