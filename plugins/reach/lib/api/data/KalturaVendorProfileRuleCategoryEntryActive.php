<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorProfileRuleCategoryEntryActive extends KalturaVendorProfileRuleOption
{
	/**
	 * @var string
	 */
	public $categoryIds;

	private static $map_between_objects = array
	(
		'categoryIds',

	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull(array('categoryIds'));
		parent::validateForInsert($propertiesToSkip);
	}
}