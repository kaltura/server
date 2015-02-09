<?php
/**
 * API class for recipient provider which constructs a dynamic list of recipients according to a user filter
 *
 * @package plugins.emailNotification
 * @subpackage model.data
 */
class KalturaEmailNotificationUserRecipientProvider extends KalturaEmailNotificationRecipientProvider
{
	/**
	 * @var KalturaUserFilter
	 */
	public $filter;
	
	private static $map_between_objects = array(
		'filter',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($dbObject = null, $propertiesToSkip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kEmailNotificationUserRecipientProvider();
			
		return parent::toObject($dbObject, $propertiesToSkip);
	}	
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject($source_object)
	 */
	public function fromObject($dbObject, IResponseProfile $responseProfile = null)
	{
		parent::fromObject($dbObject, $responseProfile);
		if ($dbObject->getFilter())
		{
			$this->filter = new KalturaUserFilter();
			$this->filter->fromObject($dbObject->getFilter());
		}
	}
}