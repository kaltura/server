<?php
/**
 * If this class used as the template data, the fields will be taken from the content parameters
 * @package plugins.httpNotification
 * @subpackage api.objects
 */
class KalturaHttpNotificationDataFields extends KalturaHttpNotificationData
{
	/**
	 * @var string
	 */
	public $contentType;

	/**
	 * It's protected on purpose, used by getData
	 * @see KalturaHttpNotificationDataFields::getData()
	 * @var string
	 */
	protected $data;

	private static $map_between_objects = array
	(
		'contentType',
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $propertiesToSkip = array())
	{
		if(is_null($dbObject))
			$dbObject = new kHttpNotificationDataFields();

		return parent::toObject($dbObject, $propertiesToSkip);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject($srcObj)
	 */
	public function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/* @var $srcObj kHttpNotificationDataFields */
		parent::doFromObject($srcObj, $responseProfile);

		if($this->shouldGet('data', $responseProfile))
			$this->data = $srcObj->getData();
	}

	/* (non-PHPdoc)
	 * @see KalturaHttpNotificationData::getData()
	 */
	public function getData(kHttpNotificationDispatchJobData $jobData = null)
	{
		return $this->data;
	}

	public function getContentType()
	{
		return $this->contentType;
	}
}
