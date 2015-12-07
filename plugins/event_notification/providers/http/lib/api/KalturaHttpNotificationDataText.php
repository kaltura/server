<?php
/**
 * @package plugins.httpNotification
 * @subpackage api.objects
 */
class KalturaHttpNotificationDataText extends KalturaHttpNotificationData
{
	/**
	 * @var KalturaStringValue
	 */
	public $content;
	
	/**
	 * It's protected on purpose, used by getData
	 * @see KalturaHttpNotificationDataText::getData()
	 * @var string
	 */
	protected $data;
	
	private static $map_between_objects = array
	(
		'content',
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
			$dbObject = new kHttpNotificationDataText();
			
		return parent::toObject($dbObject, $propertiesToSkip);
	}
	 
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function doFromObject($dbObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/* @var $dbObject kHttpNotificationDataText */
		parent::doFromObject($dbObject, $responseProfile);
		
		if($this->shouldGet('content', $responseProfile))
		{
			$contentType = get_class($dbObject->getContent());
			switch ($contentType)
			{
				case 'kStringValue':
					$this->content = new KalturaStringValue();
					break;
					
				case 'kEvalStringField':
					$this->content = new KalturaEvalStringField();
					break;
					
				default:
					$this->content = KalturaPluginManager::loadObject('KalturaStringValue', $contentType);
					break;
			}
			
			if($this->content)
				$this->content->fromObject($dbObject->getContent());
		}
			
		if($this->shouldGet('data', $responseProfile))
			$this->data = $dbObject->getData();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaHttpNotificationData::getData()
	 */
	public function getData(kHttpNotificationDispatchJobData $jobData = null)
	{
		return $this->data;
	}
}
