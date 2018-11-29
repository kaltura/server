<?php
/**
 * @package plugins.beacon
 * @subpackage api.objects
 */
class KalturaBeaconScheduledResourceItem extends KalturaBeaconAbstractScheduledResourceItem
{
	/**
	 * @var KalturaBeaconScheduledResourceFieldName
	 */
	public $fieldName;

	private static $map_between_objects = array(
		'fieldName'
	);

	private static $map_dynamic_enum = array();

	private static $map_field_enum = array(
		KalturaBeaconScheduledResourceFieldName::EVENT_TYPE => BeaconScheduledResourceFieldName::EVENT_TYPE,
		KalturaBeaconScheduledResourceFieldName::OBJECT_ID => BeaconScheduledResourceFieldName::OBJECT_ID,
		KalturaBeaconScheduledResourceFieldName::IS_LOG => BeaconScheduledResourceFieldName::IS_LOG,
		KalturaBeaconScheduledResourceFieldName::STATUS => BeaconScheduledResourceFieldName::STATUS,
		KalturaBeaconScheduledResourceFieldName::RECORDING => BeaconScheduledResourceFieldName::RECORDING,
		KalturaBeaconScheduledResourceFieldName::RESOURCE_NAME => BeaconScheduledResourceFieldName::RESOURCE_NAME,
		KalturaBeaconScheduledResourceFieldName::UPDATED_AT => BeaconScheduledResourceFieldName::UPDATED_AT,
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new BeaconScheduledResourceItem();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}

	protected function getItemFieldName()
	{
		return $this->fieldName;
	}

	protected function getFieldEnumMap()
	{
		return self::$map_field_enum;
	}

	protected function getDynamicEnumMap()
	{
		return self::$map_dynamic_enum;
	}
}