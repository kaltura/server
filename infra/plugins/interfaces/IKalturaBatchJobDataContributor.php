<?php
/**
 * Interface which allows plugin to add its own configuration to a batch job.
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaBatchJobDataContributor extends IKalturaBase
{
	/**
	 * Contribute to convert job data 
	 * @param kJobData $jobData
	 * @returns kJobData
	 */ 
	public static function contributeToJobData ($jobType, $jobSubType, kJobData $jobData);
}