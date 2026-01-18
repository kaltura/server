<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchAttachmentItem extends KalturaESearchEntryAbstractNestedItem
{

	/**
	 * @var KalturaESearchAttachmentFieldName
	 */
	public $fieldName;

	private static $map_between_objects = array(
		'fieldName'
	);

	private static $map_dynamic_enum = array();

	private static $map_field_enum = array(
		KalturaESearchAttachmentFieldName::CONTENT => ESearchAttachmentFieldName::CONTENT,
		KalturaESearchAttachmentFieldName::FILE_NAME => ESearchAttachmentFieldName::FILE_NAME,
		KalturaESearchAttachmentFieldName::PAGE_NUMBER => ESearchAttachmentFieldName::PAGE_NUMBER,
		KalturaESearchAttachmentFieldName::ASSET_ID => ESearchAttachmentFieldName::ASSET_ID
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new ESearchAttachmentItem();
		return parent::toObject($object_to_fill, $props_to_skip);
	}

	protected function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$this->fieldName = self::getApiFieldName($srcObj->getFieldName());
		return parent::doFromObject($srcObj, $responseProfile);
	}

	protected static function getApiFieldName ($srcFieldName)
	{
		foreach (self::$map_field_enum as $key => $value)
		{
			if ($value == $srcFieldName)
			{
				return $key;
			}
		}

		return null;
	}

	protected function getItemFieldName()
	{
		return $this->fieldName;
	}

	protected function getDynamicEnumMap()
	{
		return self::$map_dynamic_enum;
	}

	protected function getFieldEnumMap()
	{
		return self::$map_field_enum;
	}
}
