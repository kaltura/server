<?php
/**
 * Core class for recipient provider containing a static list of email recipients.
 *
 * @package plugins.eventNotification
 * @subpackage model.data
 */
class kEmailNotificationStaticRecipientsProvider extends kEmailNotificationRecipientProvider
{
	/**
	 * Email notification "to" sendees
	 * @var array
	 */
	protected $emailRecipients;

	/**
	 * @return the $to
	 */
	public function getEmailRecipients() {
		return $this->emailRecipients;
	}

	/**
	 * @param field_type $to
	 */
	public function setEmailRecipients($v) {
		$this->emailRecipients = $v;
	}

	
	
}