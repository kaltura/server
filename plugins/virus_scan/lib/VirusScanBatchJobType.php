<?php
/**
 * @package api
 * @subpackage enum
 */
class VirusScanBatchJobType implements IKalturaPluginEnum, BatchJobType
{
	const VIRUS_SCAN = 'VirusScan';
	
	public static function getAdditionalValues()
	{
		return array(
			'VIRUS_SCAN' => self::VIRUS_SCAN
		);
	}
}
