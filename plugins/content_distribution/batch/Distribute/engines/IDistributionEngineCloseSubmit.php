<?php
interface IDistributionEngineCloseSubmit
{
	/**
	 * check for submission closure in case the submission is asynchronous.
	 * @return KalturaBatchJobStatus
	 */
	public function closeSubmit();
}