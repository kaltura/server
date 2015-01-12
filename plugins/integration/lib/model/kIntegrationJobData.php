<?php
/**
 * @package plugins.integration
 * @subpackage model.data
 */
class kIntegrationJobData extends kJobData
{
	/**
	 * @var kIntegrationJobProviderData
	 */
	private $providerData;
	
	/**
	 * @var IntegrationProviderType
	 */
	private $providerType;
	
	/**
	 * @return IntegrationProviderType
	 */
	public function getProviderType()
	{
		return $this->providerType;
	}

	/**
	 * @param IntegrationProviderType $providerType
	 */
	public function setProviderType($providerType)
	{
		$this->providerType = $providerType;
	}

	/**
	 * @return kIntegrationJobProviderData
	 */
	public function getProviderData()
	{
		return $this->providerData;
	}

	/**
	 * @param kIntegrationJobProviderData $providerData
	 */
	public function setProviderData(kIntegrationJobProviderData $providerData)
	{
		$this->providerData = $providerData;
	}
}