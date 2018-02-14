<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorProfileRuleEntryTagsModified extends KalturaVendorProfileRuleOption
{
	/**
	 * @var string
	 */
	public $tags;

	private static $map_between_objects = array
	(
		"tags",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull(array('tags'));
		parent::validateForInsert($propertiesToSkip);
	}
}