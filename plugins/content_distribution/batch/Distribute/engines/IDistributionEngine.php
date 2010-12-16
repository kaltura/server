<?php
interface IDistributionEngine
{
	/**
	 * @param KSchedularTaskConfig $taskConfig
	 */
	abstract public function configure(KSchedularTaskConfig $taskConfig);
	
	/**
	 * @param KalturaClient $kalturaClient
	 */
	abstract protected function setClient(KalturaClient $kalturaClient);
}