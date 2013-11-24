<?php
/**
 * Distributes kaltura entries to remote destination  
 *
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */
class KAsyncDistributeSubmitCloser extends KAsyncDistributeCloser
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::DISTRIBUTION_SUBMIT;
	}
	
	/* (non-PHPdoc)
	 * @see KAsyncDistribute::getDistributionEngine()
	 */
	protected function getDistributionEngine($providerType, KalturaDistributionJobData $data)
	{
		return DistributionEngine::getEngine('IDistributionEngineCloseSubmit', $providerType, $data);
	}
	
	/* (non-PHPdoc)
	 * @see KAsyncDistribute::execute()
	 */
	protected function execute(KalturaDistributionJobData $data)
	{
		return $this->engine->closeSubmit($data);
	}
}
