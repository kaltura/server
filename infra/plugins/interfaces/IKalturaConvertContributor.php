<?php
/**
 * Interface which allows plugin to add its own configuration to conversion process.
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaConvertContributor extends IKalturaBase
{
	/**
	 * Contribute to convert job data 
	 * @param kConvertJobData $jobData
	 * @returns kConvertJobData
	 */ 
	public static function contributeToConvertJobData ($enumValue, kConvertJobData $jobData);
}