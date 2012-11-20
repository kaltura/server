<?php
/**
 * API class for recipient provider containing a static list of email recipients.
 *
 * @package plugins.eventNotification
 * @subpackage model.data
 */
class KalturaEmailNotificationStaticRecipientProvider extends KalturaEmailNotificationRecipientProvider
{	
	/**
	 * Email to emails and names
	 * @var KalturaEmailNotificationRecipientArray
	 */
	public $emailRecipients;
	
}