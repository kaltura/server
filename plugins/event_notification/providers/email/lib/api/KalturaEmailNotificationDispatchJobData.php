<?php
/**
 * @package plugins.emailNotification
 * @subpackage api.objects
 */
class KalturaEmailNotificationDispatchJobData extends KalturaEventNotificationDispatchJobData
{
	
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
	 * Email recipient emails and names, key is mail address and value is the name
	 * @var KalturaEmailNotificationRecipientJobData
	 */
	public $to;
	
	/**
	 * Email cc emails and names, key is mail address and value is the name
	 * @var KalturaEmailNotificationRecipientJobData
	 */
	public $cc;
	
	/**
	 * Email bcc emails and names, key is mail address and value is the name
	 * @var KalturaEmailNotificationRecipientJobData
	 */
	public $bcc;
	
	/**
	 * Email addresses that a replies should be sent to, key is mail address and value is the name
	 * 
	 * @var KalturaEmailNotificationRecipientJobData
	 */
	public $replyTo;
	
	/**
	 * Define the email priority
	 * @var KalturaEmailNotificationTemplatePriority
	 */
	public $priority;
	
	/**
	 * Email address that a reading confirmation will be sent to
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
	
	private static $map_between_objects = array
	(
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

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function fromObject($dbObject)
	{
		/* @var $dbObject kEmailNotificationDispatchJobData */
		parent::fromObject($dbObject);
		
		$this->to = KalturaEmailNotificationRecipientJobData::getDataInstance($dbObject->getTo());
		$this->cc = KalturaEmailNotificationRecipientJobData::getDataInstance($dbObject->getCc());
		$this->bcc = KalturaEmailNotificationRecipientJobData::getDataInstance($dbObject->getBcc());
		$this->replyTo = KalturaEmailNotificationRecipientJobData::getDataInstance($dbObject->getReplyTo());
		$this->customHeaders = KalturaKeyValueArray::fromKeyValueArray($dbObject->getCustomHeaders());
		$this->contentParameters = KalturaKeyValueArray::fromKeyValueArray($dbObject->getContentParameters());
	}
}
