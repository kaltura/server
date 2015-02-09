<?php
/**
 * @package plugins.emailNotification
 * @subpackage api.objects
 */
class KalturaEmailNotificationRecipient extends KalturaObject
{
	/**
	 * Recipient e-mail address
	 * @var KalturaStringValue
	 */
	public $email;
	
	/**
	 * Recipient name
	 * @var KalturaStringValue
	 */
	public $name;
	
	private static $map_between_objects = array
	(
		'email',
		'name',
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kEmailNotificationRecipient();
			
		return parent::toObject($dbObject, $skip);
	}
	 
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function fromObject($dbObject, IResponseProfile $responseProfile = null)
	{
		/* @var $dbObject kEmailNotificationRecipient */
		parent::fromObject($dbObject, $responseProfile);
		
		
		$emailType = get_class($dbObject->getEmail());
		KalturaLog::debug("Loading KalturaStringValue from type [$emailType]");
		switch ($emailType)
		{
			case 'kStringValue':
				$this->email = new KalturaStringValue();
				break;
				
			case 'kEvalStringField':
				$this->email = new KalturaEvalStringField();
				break;
				
			case 'kUserEmailContextField':
				$this->email = new KalturaUserEmailContextField();
				break;
				
			default:
				$this->email = KalturaPluginManager::loadObject('KalturaStringValue', $emailType);
				break;
		}
		if($this->email)
			$this->email->fromObject($dbObject->getEmail());
		
			
		$nameType = get_class($dbObject->getName());
		KalturaLog::debug("Loading KalturaStringValue from type [$nameType]");
		switch ($nameType)
		{
			case 'kStringValue':
				$this->name = new KalturaStringValue();
				break;
				
			case 'kEvalStringField':
				$this->name = new KalturaEvalStringField();
				break;
				
			default:
				$this->name = KalturaPluginManager::loadObject('KalturaStringValue', $nameType);
				break;
		}
		if($this->name)
			$this->name->fromObject($dbObject->getName());
	}
}
