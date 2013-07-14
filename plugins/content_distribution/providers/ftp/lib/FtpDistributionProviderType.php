<?php
/**
 * @package plugins.ftpDistribution
 * @subpackage lib
 */
class FtpDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
{
	const FTP = 'FTP';
	const FTP_SCHEDULED = 'FTP_SCHEDULED';
	
	public static function getAdditionalValues()
	{
		return array(
			'FTP' => self::FTP,
			'FTP_SCHEDULED' => self::FTP_SCHEDULED,
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
