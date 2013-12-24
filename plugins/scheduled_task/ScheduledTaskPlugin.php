<?php
/**
 * @package plugins.scheduledTask
 */
class ScheduledTaskPlugin extends KalturaPlugin implements IKalturaVersion, IKalturaPermissions, IKalturaServices, IKalturaConfigurator, IKalturaEnumerator
{
	const PLUGIN_NAME = 'scheduledTask';
	const PLUGIN_VERSION_MAJOR = 1;
	const PLUGIN_VERSION_MINOR = 0;
	const PLUGIN_VERSION_BUILD = 0;

	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}

	/* (non-PHPdoc)
	 * @see IKalturaVersion::getVersion()
	 */
	public static function getVersion()
	{
		return new KalturaVersion(
			self::PLUGIN_VERSION_MAJOR,
			self::PLUGIN_VERSION_MINOR,
			self::PLUGIN_VERSION_BUILD
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
 	*/
	public static function isAllowedPartner($partnerId)
	{
		if($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID || $partnerId == Partner::BATCH_PARTNER_ID)
			return true;

		$partner = PartnerPeer::retrieveByPK($partnerId);
		if ($partner)
			return $partner->getPluginEnabled(self::PLUGIN_NAME);

		return false;
	}

	/* (non-PHPdoc)
 	* @see IKalturaServices::getServicesMap()
 	*/
	public static function getServicesMap()
	{
		return array(
			'scheduledTaskProfile' => 'ScheduledTaskProfileService',
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaConfigurator::getConfig()
	 */
	public static function getConfig($configName)
	{
		if($configName == 'generator')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/generator.ini');

		if($configName == 'testme')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/testme.ini');

		return null;
	}

	/**
	 * Returns a list of enumeration class names that implement the baseEnumName interface.
	 * @param string $baseEnumName the base implemented enum interface, set to null to retrieve all plugin enums
	 * @return array<string> A string listing the enum class names that extend baseEnumName
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('ScheduledTaskBatchType');

		if($baseEnumName == 'BatchJobType')
			return array('ScheduledTaskBatchType');

		return array();
	}


	/**
	 * @param $valueName
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
}