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
	 * Engine's client
	 * @var KalturaClient
	 */
	protected $client;
	
	
	
	/**
	 * Job data for the email notification recipients
	 * @var KalturaEmailNotificationRecipientJobData
	 */
	protected $recipientJobData;
	
	public function __construct(KalturaEmailNotificationRecipientJobData $recipientJobData, $kClient)
	{
		$this->client = $kClient;
		$this->recipientJobData = $recipientJobData;
		
	}
	
	/**
	 * Function retrieves instance of recipient job data
	 * @param KalturaEmailNotificationRecipientJobData $recipientJobData
	 * @param KalturaClient $kClient
	 * @return KEmailNotificationRecipientEngine
	 */
	public static function getEmailNotificationRecipientEngine(KalturaEmailNotificationRecipientJobData $recipientJobData, $kClient)
	{
		return KalturaPluginManager::loadObject('KEmailNotificationRecipientEngine', $recipientJobData->providerType, array($recipientJobData, $kClient));
	}

	
	/**
	 * Function returns an array of the recipients who should receive the email notification regarding the category
	 * @param array $contentParameters
	 */
	abstract function getRecipients (array $contentParameters);
}