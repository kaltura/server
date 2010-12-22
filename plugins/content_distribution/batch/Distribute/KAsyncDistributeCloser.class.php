<?php
require_once("bootstrap.php");
/**
 * Closes asynchronous distribution jobs
 *
 * @package Scheduler
 * @subpackage Distribute
 */
abstract class KAsyncDistributeCloser extends KAsyncDistribute
{
	/**
	 * @return array<KalturaBatchJob>
	 */
	abstract protected function getExclusiveAlmostDoneDistributeJobs();
	
	/* (non-PHPdoc)
	 * @see KAsyncDistribute::getExclusiveDistributeJobs()
	 */
	protected function getExclusiveDistributeJobs()
	{
		return $this->getExclusiveAlmostDoneDistributeJobs();
	}
}
