<?php
/**
 * If this class used as the template data, the fields will be taken from the content parameters
 * @package plugins.httpNotification
 * @subpackage api.objects
 */
class KalturaHttpNotificationDataFields extends KalturaHttpNotificationData
{
	/**
	 * It's protected on purpose, used by getData
	 * @see KalturaHttpNotificationDataFields::getData()
	 * @var string
	 */
	protected $data;
	
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
	public function fromObject($srcObj)
	{
		/* @var $srcObj kHttpNotificationDataFields */
		parent::fromObject($srcObj);
		$this->data = $srcObj->getData();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaHttpNotificationData::getData()
	 */
	public function getData(kHttpNotificationDispatchJobData $jobData = null)
	{
		return $this->data;
	}
}
