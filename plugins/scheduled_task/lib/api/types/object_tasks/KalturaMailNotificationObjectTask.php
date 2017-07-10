<?php

/**
 * @package plugins.scheduledTask
 * @subpackage api.objects.objectTasks
 */
class KalturaMailNotificationObjectTask extends KalturaObjectTask
{
	/**
	 * The mail to send the notification to
	 *
	 * @var string
	 */
	public $mailAddress;
	/**
	 * The message to send in the notification mail
	 *
	 * @var string area
	 */
	public $message;
	/**
	 * The sender in the mail
	 *
	 * @var string
	 */
	public $sender;
	/**
	 * The basic link for the KMC site
	 *
	 * @var string
	 */
	public $link;
	/**
	 * The subject of the entry
	 *
	 * @var string
	 */
	public $subject;
	/**
	 * Send the mail to each user
	 *
	 * @var bool
	 */
	public $sendToUsers;

	public function __construct()
	{
		$this->type = ObjectTaskType::MAIL_NOTIFICATION;
	}

	public function toObject($dbObject = null, $skip = array())
	{
		/** @var kObjectTask $dbObject */
		$dbObject = parent::toObject($dbObject, $skip);
		$dbObject->setDataValue('mailAddress', $this->mailAddress);
		$dbObject->setDataValue('message', $this->message);
		$dbObject->setDataValue('sendToUsers', $this->sendToUsers);
		$dbObject->setDataValue('sender', $this->sender);
		$dbObject->setDataValue('subject', $this->subject);
		$dbObject->setDataValue('link', $this->link);
		return $dbObject;
	}
	public function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($srcObj, $responseProfile);
		/** @var kObjectTask $srcObj */
		$this->mailAddress = $srcObj->getDataValue('mailAddress');
		$this->message = $srcObj->getDataValue('message');
		$this->sendToUsers = $srcObj->getDataValue('sendToUsers');
		$this->sender = $srcObj->getDataValue('sender');
		$this->subject = $srcObj->getDataValue('subject');
		$this->link = $srcObj->getDataValue('link');
	}

}
