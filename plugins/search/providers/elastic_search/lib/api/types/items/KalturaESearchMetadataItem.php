<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchMetadataItem extends KalturaESearchItem
{
	/**
	 * @var string
	 */
	public $xpath;

	/**
	 * @var int
	 */
	public $metadataProfileId;

	private static $map_between_objects = array(
		'xpath',
		'metadataProfileId',
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new ESearchMetadataItem();
		return parent::toObject($object_to_fill, $props_to_skip);
	}

	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		$this->validatePropertyNotNull("searchTerm");
		return parent::validateForUsage($sourceObject, $propertiesToSkip);
	}

}
