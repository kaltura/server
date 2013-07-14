<?php
/**
 * @package api
 * @subpackage enum
 */
class VirusScanEntryStatus implements IKalturaPluginEnum, entryStatus
{
	const INFECTED = 'Infected';
	const SCAN_FAILURE = 'ScanFailure';
	
	public static function getAdditionalValues()
	{
		return array(
			'INFECTED' => self::INFECTED,
		    'SCAN_FAILURE' => self::SCAN_FAILURE,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array();
	}
}
