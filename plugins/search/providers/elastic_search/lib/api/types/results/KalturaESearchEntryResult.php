<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchEntryResult extends KalturaESearchResult
{
	/**
	 * @var KalturaBaseEntry
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
		$isAdmin = kCurrentContext::$ks_object->isAdmin();
		$object = KalturaEntryFactory::getInstanceByType($srcObj->getObject()->getType(), $isAdmin);
		$object->fromObject($srcObj->getObject());
		$this->object = $object;
		return parent::doFromObject($srcObj, $responseProfile);
	}

}
