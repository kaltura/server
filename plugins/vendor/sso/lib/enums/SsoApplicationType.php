<?php
/**
 * @package plugins.sso
 * @subpackage lib.enum
 */
class SsoApplicationType implements IKalturaPluginEnum, ApplicationType
{
	const SSO_APPLICATION_KMC = 'SSO_APPLICATION_KMC';
	const SSO_APPLICATION_KMS = 'SSO_APPLICATION_KMS';

	/**
	 * @return array
	 */
	public static function getAdditionalValues()
	{
		return array(
			'SSO_APPLICATION_KMC' => self::SSO_APPLICATION_KMC,
			'SSO_APPLICATION_KMS' => self::SSO_APPLICATION_KMS,
		);
	}

	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array(
			self::SSO_APPLICATION_KMC => 'SSO Application Type KMC',
			self::SSO_APPLICATION_KMS => 'SSO Application Type KMS',
		);
	}
}