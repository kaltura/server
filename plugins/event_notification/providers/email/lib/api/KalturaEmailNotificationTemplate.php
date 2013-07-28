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
	 * @requiresPermission update
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
	 * @var KalturaEmailNotificationRecipientProvider
	 */
	public $to;
	
	/**
	 * Email recipient emails and names
	 * @var KalturaEmailNotificationRecipientProvider
	 */
	public $cc;
	
	/**
	 * Email recipient emails and names
	 * @var KalturaEmailNotificationRecipientProvider
	 */
	public $bcc;
	
	/**
	 * Default email addresses to whom the reply should be sent. 
	 * 
	 * @var KalturaEmailNotificationRecipientProvider
	 */
	public $replyTo;
	
	/**
	 * Define the email priority
	 * @var KalturaEmailNotificationTemplatePriority
	 * @requiresPermission update
	 */
	public $priority;
	
	/**
	 * Email address that a reading confirmation will be sent
	 * 
	 * @var string
	 */
	public $confirmReadingTo;
	
	/**
	 * Hostname to use in Message-Id and Received headers and as default HELLO string. 
	 * If empty, the value returned by SERVER_NAME is used or 'localhost.localdomain'.
	 * 
	 * @var string
	 * @requiresPermission update
	 */
	public $hostname;
	
	/**
	 * Sets the message ID to be used in the Message-Id header.
	 * If empty, a unique id will be generated.
	 * 
	 * @var string
	 * @requiresPermission update
	 */
	public $messageID;
	
	/**
	 * Adds a e-mail custom header
	 * 
	 * @var KalturaKeyValueArray
	 * @requiresPermission update
	 */
	public $customHeaders;
	
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
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject($source_object)
	 */
	public function fromObject($dbObject)
	{
		/* @var $dbObject EmailNotificationTemplate */
		parent::fromObject($dbObject);
		
		if($dbObject->getTo())
			$this->to = KalturaEmailNotificationRecipientProvider::getProviderInstance($dbObject->getTo());
		if($dbObject->getCc())
			$this->cc = KalturaEmailNotificationRecipientProvider::getProviderInstance($dbObject->getCc());
		if($dbObject->getBcc())
			$this->bcc = KalturaEmailNotificationRecipientProvider::getProviderInstance($dbObject->getBcc());
		if($dbObject->getReplyTo())
			$this->replyTo = KalturaEmailNotificationRecipientProvider::getProviderInstance($dbObject->getReplyTo());
	}
}