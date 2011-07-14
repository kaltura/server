<?php
/**
 * Enable time based cue point objects management on entry objects
 * @package plugins.cuePoint
 */
class CuePointPlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions, IKalturaEventConsumers, IKalturaMemoryCleaner, IKalturaVersion, IKalturaConfigurator, IKalturaEnumerator, IKalturaSchemaContributor
{
	const PLUGIN_NAME = 'cuePoint';
	const PLUGIN_VERSION_MAJOR = 1;
	const PLUGIN_VERSION_MINOR = 0;
	const PLUGIN_VERSION_BUILD = 0;
	const CUE_POINT_MANAGER = 'kCuePointManager';
	
	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
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
		$partner = PartnerPeer::retrieveByPK($partnerId);
		return $partner->getPluginEnabled(self::PLUGIN_NAME);
	}

	/* (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
	 */
	public static function getServicesMap()
	{
		$map = array(
			'cuePoint' => 'CuePointService',
		);
		return $map;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaEventConsumers::getEventConsumers()
	 */
	public static function getEventConsumers()
	{
		return array(
			self::CUE_POINT_MANAGER,
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaMemoryCleaner::cleanMemory()
	 */
	public static function cleanMemory()
	{
	    CuePointPeer::clearInstancePool();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('CuePointSchemaType');
		
		if($baseEnumName == 'SchemaType')
			return array('CuePointSchemaType');
			
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::contributeToSchema()
	 */
	public static function contributeToSchema($type)
	{
		$coreType = kPluginableEnumsManager::apiToCore('SchemaType', $type);
		if(
			$coreType != SchemaType::SYNDICATION
			&&
			$coreType != self::getSchemaTypeCoreValue(CuePointSchemaType::SERVE_API)
			&&
			$coreType != self::getSchemaTypeCoreValue(CuePointSchemaType::INGEST_API)
		)
			return null;
			
		
		$xsd = '
		
	<!-- ' . self::getPluginName() . ' -->
	
	<xs:complexType name="T_scenes">
		<xs:sequence>
			<xs:element ref="scene" minOccurs="1" maxOccurs="unbounded" />
		</xs:sequence>
	</xs:complexType>	
		';
		
		switch($type)
		{
			case SchemaType::SYNDICATION:
			case self::getSchemaTypeCoreValue(CuePointSchemaType::SERVE_API):
				
				$xsd .= '
	<xs:complexType name="T_scene">
		<xs:sequence>
			<xs:element name="sceneStartTime" minOccurs="1" maxOccurs="1" type="xs:time" />
			<xs:element name="createdAt" minOccurs="1" maxOccurs="1" type="xs:dateTime" />
			<xs:element name="updatedAt" minOccurs="1" maxOccurs="1" type="xs:dateTime" />
			<xs:element name="userId" minOccurs="0" maxOccurs="1" type="xs:string" />
			<xs:element name="tags" minOccurs="1" maxOccurs="1" type="core:tags" />
	
			<xs:element ref="scene-extension" minOccurs="0" maxOccurs="unbounded" />
		</xs:sequence>
		
		<xs:attribute name="sceneId" use="required" type="xs:int" />
		<xs:attribute name="systemName" use="optional" type="xs:string" />
		
	</xs:complexType>
				';
				break;
				
			case self::getSchemaTypeCoreValue(CuePointSchemaType::INGEST_API):
				$xsd .= '
	<xs:complexType name="T_scene">
		<xs:sequence>
			<xs:element name="sceneStartTime" minOccurs="1" maxOccurs="1" type="xs:time" />
			<xs:element name="tags" minOccurs="1" maxOccurs="1" type="core:tags" />
	
			<xs:element ref="scene-extension" minOccurs="0" maxOccurs="unbounded" />
		</xs:sequence>
		
		<xs:attribute name="sceneId" use="required" type="xs:int" />
		<xs:attribute name="systemName" use="optional" type="xs:string" />
		
	</xs:complexType>
				';
				break;
		}
		
		$xsd .= '
	<xs:element name="scenes" type="T_scenes" substitutionGroup="item-extension" />
	<xs:element name="scene" type="T_scene" />
	<xs:element name="scene-extension" />
		';
		
		return $xsd;
	}

	/* (non-PHPdoc)
	 * @see IKalturaConfigurator::getConfig()
	 */
	public static function getConfig($configName)
	{
		if($configName == 'generator')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/generator.ini');
			
		return null;
	}
		
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getSchemaTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('SchemaType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
}
