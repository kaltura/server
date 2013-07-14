<?php
/**
 * Core class for a provider for the recipients email notification
 *
 * @package plugins.emailNotification
 * @subpackage model
 **/
abstract class kEmailNotificationRecipientProvider
{
	
	/**
	 * This function is called when the recipient provider needs to be narrowed down using the current context
	 * @param kContext $context
	 * @return kEmailNotificationRecipientJobData
	 */
	abstract public function getScopedProviderJobData (kScope $scope = null);
}