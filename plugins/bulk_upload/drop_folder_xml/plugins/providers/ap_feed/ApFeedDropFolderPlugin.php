<?php
/**
 * @package plugins.ApFeedDropFolder
 */
class ApFeedDropFolderPlugin extends KalturaPlugin implements IKalturaPlugin, IKalturaPending, IKalturaObjectLoader, IKalturaEnumerator, IKalturaApplicationTranslations
{
	const PLUGIN_NAME = 'ApFeedDropFolder';
	
	const DROP_FOLDER_PLUGIN_NAME = 'dropFolder';
	
	const FEED_DROP_FOLDER_PLUGIN_NAME = 'FeedDropFolder';
	/**
	 * @return array
	 */
	public static function getTranslations($locale)
	{
		$array = array();
		
		$langFilePath = __DIR__ . "/config/lang/$locale.php";
		if(!file_exists($langFilePath))
		{
			$default = 'en';
			$langFilePath = __DIR__ . "/config/lang/$default.php";
		}
		
		KalturaLog::info("Loading file [$langFilePath]");
		$array = include($langFilePath);
		
		return array($locale => $array);
	}
	
	/**
	 * Returns a list of enumeration class names that implement the baseEnumName interface.
	 * @param string $baseEnumName the base implemented enum interface, set to null to retrieve all plugin enums
	 * @return array<string> A string listing the enum class names that extend baseEnumName
	 */
	public static function getEnums($baseEnumName = null)
	{
		if (!$baseEnumName)
		{
			return array('ApFeedDropFolderType');
		}
		if ($baseEnumName == 'DropFolderType')
		{
			return array('ApFeedDropFolderType');
		}
		
		return array();
	}
	
	/**
	 * Returns an object that is known only to the plugin, and extends the baseClass.
	 *
	 * @param string $baseClass The base class of the loaded object
	 * @param string $enumValue The enumeration value of the loaded object
	 * @param array $constructorArgs The constructor arguments of the loaded object
	 * @return object
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		switch ($baseClass)
		{
			case 'KDropFolderEngine':
				if ($enumValue == KalturaDropFolderType::AP_FEED)
				{
					return new KApFeedDropFolderEngine();
				}
				break;
			case ('KalturaDropFolderFile'):
				if ($enumValue == self::getDropFolderTypeCoreValue(ApFeedDropFolderType::AP_FEED) )
				{
					return new KalturaFeedDropFolderFile();
				}
				break;
			case ('KalturaDropFolder'):
				if ($enumValue == self::getDropFolderTypeCoreValue(ApFeedDropFolderType::AP_FEED) )
				{
					return new KalturaApFeedDropFolder();
				}
				break;
			case 'kDropFolderXmlFileHandler':
				if ($enumValue == self::getDropFolderTypeCoreValue(ApFeedDropFolderType::AP_FEED))
				{
					return new kDropFolderFeedXmlFileHandler();
				}
				break;
			case 'Form_DropFolderConfigureExtend_SubForm':
				if ($enumValue == Kaltura_Client_DropFolder_Enum_DropFolderType::AP_FEED)
				{
					return new Form_ApFeedDropFolderConfigureExtend_SubForm();
				}
				break;
			case 'Kaltura_Client_DropFolder_Type_DropFolder':
				if ($enumValue == Kaltura_Client_DropFolder_Enum_DropFolderType::AP_FEED)
				{
					return new Kaltura_Client_ApFeedDropFolder_Type_ApFeedDropFolder();
				}
				break;
		}
	}
	
	/**
	 * Retrieves a class name that is defined by the plugin and is known only to the plugin, and extends the baseClass.
	 *
	 * @param string $baseClass The base class of the searched class
	 * @param string $enumValue The enumeration value of the searched class
	 * @return string The name of the searched object's class
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		switch ($baseClass) {
			case 'DropFolderFile':
				if ($enumValue == self::getDropFolderTypeCoreValue(ApFeedDropFolderType::AP_FEED))
					return 'FeedDropFolderFile';
				break;
			
			case 'DropFolder':
				if ($enumValue == self::getDropFolderTypeCoreValue(ApFeedDropFolderType::AP_FEED))
					return 'ApFeedDropFolder';
				break;
			
		}
	}
	
	/**
	 * Returns a Kaltura dependency object that defines the relationship between two plugins.
	 *
	 * @return array<KalturaDependency> The Kaltura dependency object
	 */
	public static function dependsOn()
	{
		$dropFolderDependency = new KalturaDependency(self::DROP_FOLDER_PLUGIN_NAME);
		$feedDropFolderDependency = new KalturaDependency(self::FEED_DROP_FOLDER_PLUGIN_NAME);
		
		return array($dropFolderDependency, $feedDropFolderDependency);
	}
	
	/**
	 * @return string the name of the plugin
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
	
	public static function getDropFolderTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('DropFolderType', $value);
	}
}
