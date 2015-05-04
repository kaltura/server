<?php
/**
 * @package plugins.integration
 * @subpackage Scheduler
 */
interface KIntegrationEngine
{	
	/**
	 * @param KalturaBatchJob $job
	 * @param KalturaIntegrationJobData $data
	 */
	public function dispatch(KalturaBatchJob $job, KalturaIntegrationJobData &$data);
}
