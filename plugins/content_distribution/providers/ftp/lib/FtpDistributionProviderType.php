<?php
/**
 * @package plugins.ftpDistribution
 * @subpackage lib
 */
class FtpDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
{
	const FTP = 'FTP';
	
	public static function getAdditionalValues()
	{
		return array(
			'FTP' => self::FTP,
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
