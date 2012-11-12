<?php
/**
 * Core class for recipient provider containing a static list of email recipients.
 *
 * @package plugins.eventNotification
 * @subpackage model.data
 */
class EmailNotificationStaticRecipientsProvider extends EmailNotificationRecipientProvider
{
	/**
	 * Email notification "to" sendees
	 * @var array
	 */
	protected $to;
	
	/**
	 * Email notification "CC" sendees
	 * @var array
	 */
	protected $cc;
	
	/**
	 * Email notification "BCC" sendees
	 * @var array
	 */
	protected $bcc;
	
	/**
	 * @return the $bcc
	 */
	public function getBcc() {
		return $this->bcc;
	}

	/**
	 * @param field_type $bcc
	 */
	public function setBcc($bcc) {
		$this->bcc = $bcc;
	}

	/**
	 * @return the $cc
	 */
	public function getCc() {
		return $this->cc;
	}

	/**
	 * @param field_type $cc
	 */
	public function setCc($cc) {
		$this->cc = $cc;
	}

	/**
	 * @return the $to
	 */
	public function getTo() {
		return $this->to;
	}

	/**
	 * @param field_type $to
	 */
	public function setTo($to) {
		$this->to = $to;
	}

	
	
}