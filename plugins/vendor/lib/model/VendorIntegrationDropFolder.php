<?php

/*
 * @package plugins.vendor
 * @subpackage model
 */
abstract class VendorIntegrationDropFolder extends RemoteDropFolder
{
	const INTEGRATION_SETTING_ID = 'integration_setting_id';

	public function getIntegrationId ()
	{
		return $this->getFromCustomData(self::INTEGRATION_SETTING_ID);
	}

	public function setIntegrationId ($integrationId)
	{
		$this->putInCustomData(self::INTEGRATION_SETTING_ID, $integrationId);
	}

}