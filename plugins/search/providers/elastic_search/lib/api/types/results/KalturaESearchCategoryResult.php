<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchCategoryResult extends KalturaESearchResult
{
	/**
	 * @var KalturaCategory
	 */
	public $object;

	private static $map_between_objects = array(
		'object',
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	protected function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$object = new KalturaCategory();
		$object->fromObject($srcObj->getObject(), $responseProfile);
		$this->object = $object;
		return parent::doFromObject($srcObj, $responseProfile);
	}

}
