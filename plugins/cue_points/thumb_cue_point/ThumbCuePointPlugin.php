<?php
/**
 * @package plugins.thumbCuePoint
 */
class ThumbCuePointPlugin extends BaseCuePointPlugin implements IKalturaCuePoint, IKalturaTypeExtender, IKalturaEventConsumers, IKalturaCuePointXmlParser
{
	const PLUGIN_NAME = 'thumbCuePoint';
	const CUE_POINT_VERSION_MAJOR = 1;
	const CUE_POINT_VERSION_MINOR = 0;
	const CUE_POINT_VERSION_BUILD = 0;
	const CUE_POINT_NAME = 'cuePoint';
	const THUMB_CUE_POINT_MANAGER_CLASS = 'kThumbCuePointManager';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
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
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('timedThumbAssetType', 'ThumbCuePointType', 'BaseEntryThumbCuePointCloneOptions');
	
		if($baseEnumName == 'assetType')
			return array('timedThumbAssetType');
			
		if($baseEnumName == 'CuePointType')
			return array('ThumbCuePointType');

		if($baseEnumName == 'BaseEntryCloneOptions')
			return array('BaseEntryThumbCuePointCloneOptions');
			
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaEventConsumers::getEventConsumers()
	 */
	public static function getEventConsumers()
	{
		return array(
			self::THUMB_CUE_POINT_MANAGER_CLASS,
		);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn()
	{
		$cuePointVersion = new KalturaVersion(
			self::CUE_POINT_VERSION_MAJOR,
			self::CUE_POINT_VERSION_MINOR,
			self::CUE_POINT_VERSION_BUILD);
			
		$dependency = new KalturaDependency(self::CUE_POINT_NAME, $cuePointVersion);
		return array($dependency);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaTypeExtender::getExtendedTypes()
	 */
	public static function getExtendedTypes($baseClass, $enumValue)
	{
		if($baseClass == assetPeer::OM_CLASS && $enumValue == assetType::THUMBNAIL)
		{
			return array(
				ThumbCuePointPlugin::getAssetTypeCoreValue(timedThumbAssetType::TIMED_THUMB_ASSET)
			);
		}
		
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'KalturaThumbAsset' && $enumValue == self::getAssetTypeCoreValue(timedThumbAssetType::TIMED_THUMB_ASSET))
			return new KalturaTimedThumbAsset();
			
		if($baseClass == 'KalturaCuePoint' && $enumValue == self::getCuePointTypeCoreValue(ThumbCuePointType::THUMB))
			return new KalturaThumbCuePoint();
		
		return null;
	}

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'asset' && $enumValue == self::getAssetTypeCoreValue(timedThumbAssetType::TIMED_THUMB_ASSET))
			return 'timedThumbAsset';
			
		if($baseClass == 'CuePoint' && $enumValue == self::getCuePointTypeCoreValue(ThumbCuePointType::THUMB))
			return 'ThumbCuePoint';
		
		return null;
	}
	
/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::contributeToSchema()
	 */
	public static function contributeToSchema($type)
	{
		//TBD add thumb asset support to xsd
		$coreType = kPluginableEnumsManager::apiToCore('SchemaType', $type);
		if(
			$coreType != SchemaType::SYNDICATION
			&&
			$coreType != CuePointPlugin::getSchemaTypeCoreValue(CuePointSchemaType::SERVE_API)
			&&
			$coreType != CuePointPlugin::getSchemaTypeCoreValue(CuePointSchemaType::INGEST_API)
		)
			return null;
			
		$xsd = '
		
	<!-- ' . self::getPluginName() . ' -->
	
	<xs:complexType name="T_scene_thumbCuePoint">
		<xs:complexContent>
			<xs:extension base="T_scene">
				<xs:sequence>
					<xs:element name="title" minOccurs="1" maxOccurs="1" type="xs:string"> </xs:element>
					<xs:element name="description" minOccurs="1" maxOccurs="1" type="xs:string"> </xs:element>
					<xs:element name="subType" minOccurs="0" maxOccurs="1" type="KalturaThumbCuePointSubType">
						<xs:annotation>
							<xs:documentation>Indicates the thumb cue point sub type 1 = Slide 2 = Chapter</xs:documentation>
						</xs:annotation>
					</xs:element>
					<xs:element ref="scene-extension" minOccurs="0" maxOccurs="unbounded" />
				</xs:sequence>
			</xs:extension>
		</xs:complexContent>
	</xs:complexType>
	
	<xs:element name="scene-thumb-cue-point" type="T_scene_thumbCuePoint" substitutionGroup="scene">
		<xs:annotation>
			<xs:documentation>Single thumb cue point element</xs:documentation>
			<xs:appinfo>
				<example>
					<scene-thumb-cue-point sceneId="{scene id}" entryId="{entry id}">
						<sceneStartTime>00:00:05.3</sceneStartTime>
						<tags>
							<tag>my_tag</tag>
						</tags>
					</scene-thumb-cue-point>
				</example>
			</xs:appinfo>
		</xs:annotation>
	</xs:element>
	
		';	
		return $xsd;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaCuePoint::getCuePointTypeCoreValue()
	 */
	public static function getCuePointTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('CuePointType', $value);
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getAssetTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('assetType', $value);
	}

	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getBaseEntryCloneOptionsCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('BaseEntryCloneOptions', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaCuePointXmlParser::parseXml()
	 */
	public static function parseXml(SimpleXMLElement $scene, $partnerId, CuePoint $cuePoint = null)
	{
		if($scene->getName() != 'scene-thumb-cue-point')
			return $cuePoint;
			
		if(!$cuePoint)
			$cuePoint = kCuePointManager::parseXml($scene, $partnerId, new ThumbCuePoint());
			
		if(!($cuePoint instanceof ThumbCuePoint))
			return null;
		
		return $cuePoint;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaCuePointXmlParser::generateXml()
	 */
	public static function generateXml(CuePoint $cuePoint, SimpleXMLElement $scenes, SimpleXMLElement $scene = null)
	{
		if(!($cuePoint instanceof ThumbCuePoint))
			return $scene;
			
		if(!$scene)
			$scene = kCuePointManager::generateCuePointXml($cuePoint, $scenes->addChild('scene-thumb-cue-point'));
		
		if($cuePoint->getEndTime())
			$scene->addChild('sceneEndTime', kXml::integerToTime($cuePoint->getEndTime()));
	
		$scene->addChild('title', kMrssManager::stringToSafeXml($cuePoint->getName()));
		$scene->addChild('description', kMrssManager::stringToSafeXml($cuePoint->getText()));
		$scene->addChild('subType', $cuePoint->getSubType());
		$scene->addChild('thumbAssetId', $cuePoint->getAssetId());
		
		return $scene;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaCuePointXmlParser::syndicate()
	 */
	public static function syndicate(CuePoint $cuePoint, SimpleXMLElement $scenes, SimpleXMLElement $scene = null)
	{
		if(!($cuePoint instanceof ThumbCuePoint))
			return $scene;
			
		if(!$scene)
			$scene = kCuePointManager::syndicateCuePointXml($cuePoint, $scenes->addChild('scene-thumb-cue-point'));
		
		if($cuePoint->getEndTime())
			$scene->addChild('sceneEndTime', kXml::integerToTime($cuePoint->getEndTime()));
	
		$scene->addChild('title', kMrssManager::stringToSafeXml($cuePoint->getName()));
		$scene->addChild('description', kMrssManager::stringToSafeXml($cuePoint->getText()));
		$scene->addChild('subType', $cuePoint->getSubType());
		$scene->addChild('thumbAssetId', $cuePoint->getAssetId());
			
		return $scene;
	}
	
	public static function getTypesToIndexOnEntry()
	{
		return array(self::getCuePointTypeCoreValue(ThumbCuePointType::THUMB));
	}

	public static function shouldCloneByProperty(entry $entry)
	{
		return $entry->shouldCloneByProperty(self::getBaseEntryCloneOptionsCoreValue( BaseEntryThumbCuePointCloneOptions::THUMB_CUE_POINTS), false);
	}

	public static function getTypesToElasticIndexOnEntry()
	{
		return array(self::getCuePointTypeCoreValue(ThumbCuePointType::THUMB));
	}

	public static function getSubTypes()
	{
		$refClass = new ReflectionClass('ThumbCuePointSubType');
		return $refClass->getConstants();
//		 return array(ThumbCuePointSubType::SLIDE, ThumbCuePointSubType::CHAPTER);
	}
}
