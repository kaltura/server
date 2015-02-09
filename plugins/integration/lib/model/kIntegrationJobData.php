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
	 * @var kIntegrationJobTriggerData
	 */
	private $triggerData;
	
	/**
	 * @var IntegrationTriggerType
	 */
	private $triggerType;
	
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
	
	/**
	 * @return IntegrationTriggerType
	 */
	public function getTriggerType()
	{
		return $this->triggerType;
	}

	/**
	 * @param IntegrationTriggerType $triggerType
	 */
	public function setTriggerType($triggerType)
	{
		$this->triggerType = $triggerType;
	}

	/**
	 * @return kIntegrationJobTriggerData
	 */
	public function getTriggerData()
	{
		return $this->triggerData;
	}

	/**
	 * @param kIntegrationJobTriggerData $triggerData
	 */
	public function setTriggerData(kIntegrationJobTriggerData $triggerData)
	{
		$this->triggerData = $triggerData;
	}
}