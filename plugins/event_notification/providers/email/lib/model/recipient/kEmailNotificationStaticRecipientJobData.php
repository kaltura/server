<?php
/**
 * Class representing the finalized implicit list of recipients passed into the batch mechanism (after application of scope).
 *
 * @package plugins.emailNotification
 * @subpackage model.data
 */
class kEmailNotificationStaticRecipientJobData extends kEmailNotificationRecipientJobData
{
	/**
	 * Static list of email recipients in the form of key-value pairs
	 * @var array
	 */
	protected $emailRecipients;
	
	/**
	 * @return the $emailRecipients
	 */
	public function getEmailRecipients() {
		return $this->emailRecipients;
	}

	/**
	 * @param array $emailRecipients
	 */
	public function setEmailRecipients($emailRecipients) {
		$this->emailRecipients = $emailRecipients;
	}

}