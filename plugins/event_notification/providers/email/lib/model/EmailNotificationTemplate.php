<?php
/**
 * @package plugins.emailNotification
 * @subpackage model
 */
class EmailNotificationTemplate extends EventNotificationTemplate implements ISyncableFile
{
	const CUSTOM_DATA_FORMAT = 'format';
	const CUSTOM_DATA_SUBJECT = 'subject';
	const CUSTOM_DATA_FROM_EMAIL = 'fromEmail';
	const CUSTOM_DATA_FROM_NAME = 'fromName';
	const CUSTOM_DATA_TO = 'to';
	const CUSTOM_DATA_CC = 'cc';
	const CUSTOM_DATA_BCC = 'bcc';
	const CUSTOM_DATA_REPLY_TO = 'replyTo';
	const CUSTOM_DATA_PRIORITY = 'priority';
	const CUSTOM_DATA_CONFIRM_READING_TO = 'confirmReadingTo';
	const CUSTOM_DATA_HOSTNAME = 'hostname';
	const CUSTOM_DATA_MESSAGE_ID = 'messageID';
	const CUSTOM_DATA_CUSTOM_HEADERS = 'customHeaders';
	const CUSTOM_DATA_BODY_FILE_VERSION = 'bodyFileVersion';
	
	const FILE_SYNC_BODY = 1;
	
	public function __construct()
	{
		$this->setType(EmailNotificationPlugin::getEmailNotificationTemplateTypeCoreValue(EmailNotificationTemplateType::EMAIL));
		parent::__construct();
	}
	
	/* (non-PHPdoc)
	 * @see BaseEventNotificationTemplate::copy()
	 */
	public function copy($deepCopy = false)
	{
		$returnObj = parent::copy($deepCopy);
		$returnObj->setBody($this->getBody());
		return $returnObj;
	}

	/* (non-PHPdoc)
	 * @see EventNotificationTemplate::getJobData()
	 */
	public function getJobData(kScope $scope = null)
	{
		$jobData = new kEmailNotificationDispatchJobData();
		$jobData->setTemplateId($this->getId());
		$jobData->setFromEmail($this->getFromEmail());
		$jobData->setFromName($this->getFromName());
		$jobData->setPriority($this->getPriority());
		$jobData->setConfirmReadingTo($this->getConfirmReadingTo());
		$jobData->setHostname($this->getHostname());
		$jobData->setMessageID($this->getMessageID());
		$jobData->setCustomHeaders($this->getCustomHeaders());
		
		if ($this->getTo())
			$jobData->setTo($this->getTo()->getScopedProviderJobData($scope));
		if ($this->getCc())
			$jobData->setCc($this->getCc()->getScopedProviderJobData($scope));
		if ($this->getBcc())
			$jobData->setBcc($this->getBcc()->getScopedProviderJobData($scope));
		if ($this->getReplyTo())
			$jobData->setReplyTo($this->getReplyTo()->getScopedProviderJobData($scope));
		
		$contentParametersValues = array();
		$contentParameters = $this->getContentParameters();
		foreach($contentParameters as $contentParameter)
		{
			/* @var $contentParameter kEventNotificationParameter */
			$value = $contentParameter->getValue();
			if($scope && $value instanceof kStringField)
				$value->setScope($scope);
				
			$contentParametersValues[$contentParameter->getKey()] = $value->getValue();
		}
		$jobData->setContentParameters($contentParametersValues);
		
		return $jobData;
	}
	
	/**
	 * @param int $sub_type
	 * @throws string
	 */
	private function getFileSyncVersion($sub_type)
	{
		switch($sub_type)
		{
			case self::FILE_SYNC_BODY:
				return $this->getBodyFileVersion();
		}
		return null;
	}
	
	/**
	 * @param int $sub_type
	 * @throws FileSyncException
	 */
	private static function validateFileSyncSubType($sub_type)
	{
		$valid_sub_types = array(
			self::FILE_SYNC_BODY,
		);
		
		if(! in_array($sub_type, $valid_sub_types))
			throw new FileSyncException(EmailNotificationFileSyncObjectType::EMAIL_NOTIFICATION_TEMPLATE, $sub_type, $valid_sub_types);
	}
	
	/* (non-PHPdoc)
	 * @see ISyncableFile::getSyncKey()
	 */
	public function getSyncKey( $sub_type , $version=null)
	{
		self::validateFileSyncSubType($sub_type);
		
		if(!$version)
			$version = $this->getFileSyncVersion($sub_type);
		
		$key = new FileSyncKey();
		$key->object_type = EmailNotificationPlugin::getEmailNotificationFileSyncObjectTypeCoreValue(EmailNotificationFileSyncObjectType::EMAIL_NOTIFICATION_TEMPLATE);
		$key->object_sub_type = $sub_type;
		$key->object_id = $this->getId();
		$key->version = $version;
		$key->partner_id = $this->getPartnerId();
		
		return $key;
	}
	
	/* (non-PHPdoc)
	 * @see ISyncableFile::generateFilePathArr()
	 */
	public function generateFilePathArr ( $sub_type , $version=null )
	{
		self::validateFileSyncSubType ( $sub_type );
		
		if(!$version)
			$version = $this->getFileSyncVersion($sub_type);
		
		$dir = (intval($this->getId() / 1000000)) . '/' . (intval($this->getId() / 1000) % 1000);
		$path =  "/content/notifications/$dir/" . $this->generateFileName($sub_type, $version);

		return array(myContentStorage::getFSContentRootPath(), $path); 
	} 

	/* (non-PHPdoc)
	 * @see ISyncableFile::generateFileName()
	 */
	public function generateFileName( $sub_type, $version = null)
	{
		self::validateFileSyncSubType($sub_type);
		
		if(!$version)
			$version = $this->getFileSyncVersion($sub_type);
	
		$extension = 'txt';
		switch ($this->getFormat())
		{
			case EmailNotificationFormat::TEXT:
				$extension = 'txt';
				break;
				
			case EmailNotificationFormat::HTML:
				$extension = 'htm';
				break;
		}
		
		return $this->getId() . "_{$sub_type}_{$version}.{$extension}";	
	}
	
	/**
	 * @var FileSync
	 */
	private $fileSync;

	/* (non-PHPdoc)
	 * @see ISyncableFile::getFileSync()
	 */
	public function getFileSync()
	{
		return $this->fileSync; 
	}

	/* (non-PHPdoc)
	 * @see ISyncableFile::setFileSync()
	 */
	public function setFileSync(FileSync $fileSync)
	{
		 $this->fileSync = $fileSync;
	}
	
	/**
	 * @var string
	 */
	private $setBody = null;
	
	/**
	 * @var string
	 */
	private $cachedBody = null;
	
	/**
	 * @var int
	 */
	private $bodyPreviousVersion = null;

	public function getBody()
	{
		if($this->cachedBody)
			return $this->cachedBody;
			
		$key = $this->getSyncKey(self::FILE_SYNC_BODY);
		$this->cachedBody = kFileSyncUtils::file_get_contents($key, true, false);
		return $this->cachedBody;
	}

	public function setBody($body)
	{
		$this->getBody();
		if($body != $this->cachedBody)
			$this->setBody = $body;
	}

	/* (non-PHPdoc)
	 * @see BaseEventNotificationTemplate::preSave()
	 */
	public function preSave(PropelPDO $con = null)
	{
		if($this->setBody)
		{
			$this->bodyPreviousVersion = $this->getBodyFileVersion();
			if($this->bodyPreviousVersion)
				$this->incrementBodyFileVersion();
			else 
				$this->resetBodyFileVersion();
		}
			
		return parent::preSave($con);
	}

	/* (non-PHPdoc)
	 * @see BaseEventNotificationTemplate::postSave()
	 */
	public function postSave(PropelPDO $con = null)
	{
		if($this->wasObjectSaved() && $this->setBody)
		{
			$key = $this->getSyncKey(self::FILE_SYNC_BODY);
			kFileSyncUtils::file_put_contents($key, $this->setBody);
			$this->cachedBody = $this->setBody;
			$this->setBody = null;
			
			kEventsManager::raiseEvent(new kObjectDataChangedEvent($this, $this->bodyPreviousVersion));	
		}
		
		return parent::postSave($con);
	}
	
	public function getBodyFileVersion()						{return $this->getFromCustomData(self::CUSTOM_DATA_BODY_FILE_VERSION);}	
	public function getFormat()									{return $this->getFromCustomData(self::CUSTOM_DATA_FORMAT);}
	public function getSubject()								{return $this->getFromCustomData(self::CUSTOM_DATA_SUBJECT);}
	public function getFromEmail()								{return $this->getFromCustomData(self::CUSTOM_DATA_FROM_EMAIL);}
	public function getFromName()								{return $this->getFromCustomData(self::CUSTOM_DATA_FROM_NAME);}
	public function getPriority()								{return $this->getFromCustomData(self::CUSTOM_DATA_PRIORITY);}
	public function getConfirmReadingTo()						{return $this->getFromCustomData(self::CUSTOM_DATA_CONFIRM_READING_TO);}
	public function getHostname()								{return $this->getFromCustomData(self::CUSTOM_DATA_HOSTNAME);}
	public function getMessageID()								{return $this->getFromCustomData(self::CUSTOM_DATA_MESSAGE_ID);}
	public function getCustomHeaders()							{return $this->getFromCustomData(self::CUSTOM_DATA_CUSTOM_HEADERS, null, array());}
	/**
	 * Return recipient provider for the to parameter
	 * @return kEmailNotificationRecipientProvider
	 */
	public function getTo()										{return $this->getFromCustomData(self::CUSTOM_DATA_TO);}
	/**
	 * Return recipient provider for the CC parameter
	 * @return kEmailNotificationRecipientProvider
	 */
	public function getCc()										{return $this->getFromCustomData(self::CUSTOM_DATA_CC);}
	/**
	 * Return recipient provider for the BCC parameter
	 * @return kEmailNotificationRecipientProvider
	 */
	public function getBcc()									{return $this->getFromCustomData(self::CUSTOM_DATA_BCC);}
	/**
	 * Return receipientProvider for the replyTo parameter
	 * @return kEmailNotificationRecipientProvider
	 */
	public function getReplyTo()								{return $this->getFromCustomData(self::CUSTOM_DATA_REPLY_TO);}
	
	public function incrementBodyFileVersion()					{return $this->incInCustomData(self::CUSTOM_DATA_BODY_FILE_VERSION);}
	public function resetBodyFileVersion()						{return $this->putInCustomData(self::CUSTOM_DATA_BODY_FILE_VERSION, 1);}
	public function setFormat($v)								{return $this->putInCustomData(self::CUSTOM_DATA_FORMAT, $v);}
	public function setSubject($v)								{return $this->putInCustomData(self::CUSTOM_DATA_SUBJECT, $v);}
	public function setFromEmail($v)							{return $this->putInCustomData(self::CUSTOM_DATA_FROM_EMAIL, $v);}
	public function setFromName($v)								{return $this->putInCustomData(self::CUSTOM_DATA_FROM_NAME, $v);}
	public function setPriority($v)								{return $this->putInCustomData(self::CUSTOM_DATA_PRIORITY, $v);}
	public function setConfirmReadingTo($v)						{return $this->putInCustomData(self::CUSTOM_DATA_CONFIRM_READING_TO, $v);}
	public function setHostname($v)								{return $this->putInCustomData(self::CUSTOM_DATA_HOSTNAME, $v);}
	public function setMessageID($v)							{return $this->putInCustomData(self::CUSTOM_DATA_MESSAGE_ID, $v);}
	public function setCustomHeaders($v)						{return $this->putInCustomData(self::CUSTOM_DATA_CUSTOM_HEADERS, $v);}
	public function setTo(kEmailNotificationRecipientProvider $v = null)							{return $this->putInCustomData(self::CUSTOM_DATA_TO, $v);}
	public function setCc(kEmailNotificationRecipientProvider $v = null)							{return $this->putInCustomData(self::CUSTOM_DATA_CC, $v);}
	public function setBcc(kEmailNotificationRecipientProvider $v = null)							{return $this->putInCustomData(self::CUSTOM_DATA_BCC, $v);}
	public function setReplyTo(kEmailNotificationRecipientProvider $v = null)						{return $this->putInCustomData(self::CUSTOM_DATA_REPLY_TO, $v);}
}
