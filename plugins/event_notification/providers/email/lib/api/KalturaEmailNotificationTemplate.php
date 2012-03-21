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
	 * Email recipient emails and names
	 * @var KalturaEmailNotificationRecipientArray
	 */
	public $to;
	
	/**
	 * Email cc emails and names
	 * @var KalturaEmailNotificationRecipientArray
	 */
	public $cc;
	
	/**
	 * Email bcc emails and names
	 * @var KalturaEmailNotificationRecipientArray
	 */
	public $bcc;
	
	/**
	 * Email addresses that a reading confirmation will be sent to
	 * 
	 * @var KalturaEmailNotificationRecipientArray
	 */
	public $replyTo;
	
	/**
	 * Define the email priority
	 * @var KalturaEmailNotificationTemplatePriority
	 */
	public $priority;
	
	/**
	 * Email address that a reading confirmation will be sent
	 * 
	 * @var string
	 */
	public $confirmReadingTo;
	
	/**
	 * Hostname to use in Message-Id and Received headers and as default HELO string. 
	 * If empty, the value returned by SERVER_NAME is used or 'localhost.localdomain'.
	 * 
	 * @var string
	 */
	public $hostname;
	
	/**
	 * Sets the message ID to be used in the Message-Id header.
	 * If empty, a unique id will be generated.
	 * 
	 * @var string
	 */
	public $messageID;
	
	/**
	 * Adds a e-mail custom header
	 * 
	 * @var KalturaKeyValueArray
	 */
	public $customHeaders;
	
	/**
	 * Define the content dynamic parameters
	 * @var KalturaEmailNotificationParameterArray
	 */
	public $contentParameters;
	
	/**
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'format',
		'subject',
		'body',
		'fromEmail',
		'fromName',
		'to',
		'cc',
		'bcc',
		'replyTo',
		'priority',
		'confirmReadingTo',
		'hostname',
		'messageID',
		'customHeaders',
		'contentParameters',
	);
		 
	public function __construct()
	{
		$this->type = EmailNotificationPlugin::getApiValue(EmailNotificationTemplateType::EMAIL);
	}
	
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
		$propertiesToSkip[] = 'type';
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