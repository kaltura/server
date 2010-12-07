<?php
abstract class DistributionEngine
{
	/**
	 * @param string $interface
	 * @param KalturaDistributionProviderType $providerType
	 * @param KSchedularTaskConfig $taskConfig
	 * @param KalturaDistributionJobData $data
	 * @return DistributionEngine
	 */
	public static function getEngine($interface, $providerType, KSchedularTaskConfig $taskConfig, KalturaDistributionJobData $data)
	{
		$engine = null;
		if($providerType == KalturaDistributionProviderType::GENERIC)
		{
			$engine = new GenericDistributionEngine();
		}
		else
		{
			$engine = KalturaPluginManager::loadObject($interface, $providerType);
		}
		
		if($engine)
			$engine->configure($taskConfig, $data);
		
		return $engine;
	}
	
	
	/**
	 * @param KSchedularTaskConfig $taskConfig
	 * @param KalturaDistributionJobData $data
	 */
	abstract public function configure(KSchedularTaskConfig $taskConfig, KalturaDistributionJobData $data);
}