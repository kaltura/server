<?php
/**
 * Distributes kaltura entries to remote destination  
 *
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */
class KAsyncDistributeUpdate extends KAsyncDistribute
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::DISTRIBUTION_UPDATE;
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	public function getJobType()
	{
		return self::getType();
	}
	
	/* (non-PHPdoc)
	 * @see KAsyncDistribute::getDistributionEngine()
	 */
	protected function getDistributionEngine($providerType, KalturaDistributionJobData $data)
	{
		return DistributionEngine::getEngine('IDistributionEngineUpdate', $providerType, $this->getClient(), $this->taskConfig, $data);
	}
	
	/* (non-PHPdoc)
	 * @see KAsyncDistribute::execute()
	 */
	protected function execute(KalturaDistributionJobData $data)
	{
		return $this->engine->update($data);
	}
}
