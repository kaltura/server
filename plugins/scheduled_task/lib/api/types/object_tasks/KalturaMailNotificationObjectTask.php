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
	public $mailTo;
	/**
	 * The sender in the mail
	 *
	 * @var string
	 */
	public $sender;
	/**
	 * The subject of the entry
	 *
	 * @var string
	 */
	public $subject;
	/**
	 * The message to send in the notification mail
	 *
	 * @var string area
	 */
	public $message;
	/**
	 * The basic link for the KMC site
	 *
	 * @var string
	 */
	public $link;
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
		$dbObject->setDataValue('mailTo', $this->mailTo);
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
		$this->mailTo = $srcObj->getDataValue('mailTo');
		$this->message = $srcObj->getDataValue('message');
		$this->sendToUsers = $srcObj->getDataValue('sendToUsers');
		$this->sender = $srcObj->getDataValue('sender');
		$this->subject = $srcObj->getDataValue('subject');
		$this->link = $srcObj->getDataValue('link');
	}

}
