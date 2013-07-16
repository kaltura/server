<?php
/**
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */
interface IDistributionEngine
{
	/**
	 * @param KSchedularTaskConfig $taskConfig
	 */
	public function configure();
	
	/**
	 * @param KalturaClient $kalturaClient
	 */
	public function setClient();
}