<?php
class KontikiStorageProfile extends StorageProfile
{
	const KONTIKI_SERVICE_TOKEN = 'kontiki_service_token';
	
    /**
     * @return string
     */
	public function getServiceToken ()
	{
		return $this->getFromCustomData(self::KONTIKI_SERVICE_TOKEN);
	}
	
	/**
	 * @var string $v
	 */
	public function setServiceToken ($v)
	{
		$this->putInCustomData(self::KONTIKI_SERVICE_TOKEN, $v);
	}
	
}
