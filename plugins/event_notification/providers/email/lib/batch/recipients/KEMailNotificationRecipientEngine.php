<?php
/**
 * Abstract engine which retrieves a list of the email notification recipients.
 * 
 * @package plugins.emailNotification
 * @subpackage Scheduler
 */
abstract class KEmailNotificationRecipientEngine
{
	/**
	 * Job data for the email notification recipients
	 * @var KalturaEmailNotificationRecipientJobData
	 */
	protected $recipientJobData;
	
	public function __construct(KalturaEmailNotificationRecipientJobData $recipientJobData)
	{
		$this->recipientJobData = $recipientJobData;
		
	}
	
	/**
	 * Function retrieves instance of recipient job data
	 * @param KalturaEmailNotificationRecipientJobData $recipientJobData
	 * @param KalturaClient $kClient
	 * @return KEmailNotificationRecipientEngine
	 */
	public static function getEmailNotificationRecipientEngine(KalturaEmailNotificationRecipientJobData $recipientJobData)
	{
		return KalturaPluginManager::loadObject('KEmailNotificationRecipientEngine', $recipientJobData->providerType, array($recipientJobData));
	}

	
	/**
	 * Function returns an array of the recipients who should receive the email notification regarding the category
	 * @param array $contentParameters
	 */
	abstract function getRecipients (array $contentParameters);
}