<?php
interface IDistributionEngine
{
	/**
	 * @param KSchedularTaskConfig $taskConfig
	 */
	public function configure(KSchedularTaskConfig $taskConfig);
	
	/**
	 * @param KalturaClient $kalturaClient
	 */
	public function setClient(KalturaClient $kalturaClient);
}