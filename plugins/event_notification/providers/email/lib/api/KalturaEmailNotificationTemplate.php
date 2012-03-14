<?php
/**
 * @package plugins.emailNotification
 * @subpackage api.objects
 */
class KalturaEmailNotificationTemplate extends KalturaEventNotificationTemplate
{	
	/**
	 * Define the email body format
	 * @var KalturaEmailNotificationFormat
	 */
	public $format;
	
	/**
	 * Define the email subject 
	 * @var string
	 */
	public $subject;
	
	/**
	 * Define the email body content
	 * @var string
	 */
	public $body;
	
	/**
	 * Define the email sender email
	 * @var string
	 */
	public $fromEmail;
	
	/**
	 * Define the email sender name
	 * @var string
	 */
	public $fromName;
	
	/**
	 * Define the email receipient email
	 * @var string
	 */
	public $toEmail;
	
	/**
	 * Define the email receipient name
	 * @var string
	 */
	public $toName;
	
	/**
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'format',
		'subject',
		'body',
		'fromEmail',
		'fromName',
		'toEmail',
		'toName',
	);
		 
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('format');
		return parent::validateForInsert($propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('format');
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $propertiesToSkip = array())
	{
		if(is_null($dbObject))
			$dbObject = new EmailNotificationTemplate();
			
		return parent::toObject($dbObject, $propertiesToSkip);
	}
}