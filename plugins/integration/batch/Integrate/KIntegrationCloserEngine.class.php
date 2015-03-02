<?php
/**
 * @package plugins.integration
 * @subpackage Scheduler
 */
interface KIntegrationCloserEngine extends KIntegrationEngine
{	
	/**
	 * @param KalturaBatchJob $job
	 * @param KalturaIntegrationJobData $data
	 */
	public function close(KalturaBatchJob $job, KalturaIntegrationJobData &$data);
}
