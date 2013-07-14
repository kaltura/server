<?php
/**
 * Abstract class representing the finalized implicit recipient list provider to be passed into the batch mechanism
 * 
 * @package plugins.emailNotification
 * @subpackage model.data 
 */
abstract class kEmailNotificationRecipientJobData
{
	/**
	 * Type of the provider
	 * @var string
	 */
	protected $providerType;
	
	/**
	 * @return the $providerType
	 */
	public function getProviderType() {
		return $this->providerType;
	}

}