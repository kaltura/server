<?php
/**
 * Enable ad cue point objects management on entry objects
 * @package plugins.adCuePoint
 */
class AdCuePointPlugin extends KalturaPlugin implements IKalturaCuePoint
{
	const PLUGIN_NAME = 'adCuePoint';
	const CUE_POINT_VERSION_MAJOR = 1;
	const CUE_POINT_VERSION_MINOR = 0;
	const CUE_POINT_VERSION_BUILD = 0;
	const CUE_POINT_NAME = 'cuePoint';
	
	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
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
			return array('AdCuePointType');
	
		if($baseEnumName == 'CuePointType')
			return array('AdCuePointType');
			
		return array();
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
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'KalturaCuePoint' && $enumValue == self::getCuePointTypeCoreValue(AdCuePointType::AD))
			return new KalturaAdCuePoint();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'CuePoint' && $enumValue == self::getCuePointTypeCoreValue(AdCuePointType::AD))
			return 'AdCuePoint';
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
			$coreType != CuePointPlugin::getSchemaTypeCoreValue(CuePointSchemaType::SERVE_API)
			&&
			$coreType != CuePointPlugin::getSchemaTypeCoreValue(CuePointSchemaType::INGEST_API)
		)
			return null;
			
		$xsd = '		
		
	<!-- ' . self::getPluginName() . ' -->
		
	<xs:complexType name="T_scene_adCuePoint">
		<xs:complexContent>
			<xs:extension base="T_scene">
				<xs:sequence>
					<xs:element name="sceneEndTime" minOccurs="0" maxOccurs="1" type="xs:time">
						<xs:annotation>
							<xs:documentation>Cue point end time</xs:documentation>
						</xs:annotation>
					</xs:element>
					<xs:element name="sceneTitle" minOccurs="0" maxOccurs="1">
						<xs:annotation>
							<xs:documentation>Textual title</xs:documentation>
						</xs:annotation>
						<xs:simpleType>
							<xs:restriction base="xs:string">
								<xs:maxLength value="250"/>
							</xs:restriction>
						</xs:simpleType>
					</xs:element>
					<xs:element name="sourceUrl" minOccurs="0" maxOccurs="1" type="xs:string">
						<xs:annotation>
							<xs:documentation>The URL of the ad XML</xs:documentation>
						</xs:annotation>
					</xs:element>
					<xs:element name="adType" minOccurs="1" maxOccurs="1" type="KalturaAdType">
						<xs:annotation>
							<xs:documentation>Indicates the ad type</xs:documentation>
						</xs:annotation>
					</xs:element>
					<xs:element name="protocolType" minOccurs="1" maxOccurs="1" type="KalturaAdProtocolType">
						<xs:annotation>
							<xs:documentation>Indicates the ad protocol type</xs:documentation>
						</xs:annotation>
					</xs:element>
					
					<xs:element ref="scene-extension" minOccurs="0" maxOccurs="unbounded" />
				</xs:sequence>
			</xs:extension>
		</xs:complexContent>
	</xs:complexType>
	
	<xs:element name="scene-ad-cue-point" type="T_scene_adCuePoint" substitutionGroup="scene">
		<xs:annotation>
			<xs:documentation>Single ad cue point element</xs:documentation>
			<xs:appinfo>
				<example>
					<scene-ad-cue-point sceneId="{scene id}" entryId="{entry id}" systemName="MY_AD_CUE_POINT_SYSTEM_NAME">
						<sceneStartTime>00:00:05</sceneStartTime>
						<tags>
							<tag>sample</tag>
							<tag>my_tag</tag>
						</tags>
						<sceneTitle>my ad title</sceneTitle>
						<sourceUrl>http://source.to.my/ad.xml</sourceUrl>
						<adType>1</adType>
						<protocolType>1</protocolType>
					</scene-ad-cue-point>
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
	
	/* (non-PHPdoc)
	 * @see IKalturaCuePoint::getApiValue()
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
		if($scene->getName() != 'scene-ad-cue-point')
			return $cuePoint;
			
		if(!$cuePoint)
			$cuePoint = kCuePointManager::parseXml($scene, $partnerId, new AdCuePoint());
			
		if(!($cuePoint instanceof AdCuePoint))
			return null;
		
		if(isset($scene->sceneEndTime))
			$cuePoint->setEndTime(kXml::timeToInteger($scene->sceneEndTime));
		if(isset($scene->sceneTitle))
			$cuePoint->setName($scene->sceneTitle);
		if(isset($scene->sourceUrl))
			$cuePoint->setSourceUrl($scene->sourceUrl);
			
		$cuePoint->setAdType($scene->adType);
		$cuePoint->setSubType($scene->protocolType);
		
		return $cuePoint;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaCuePointXmlParser::generateXml()
	 */
	public static function generateXml(CuePoint $cuePoint, SimpleXMLElement $scenes, SimpleXMLElement $scene = null)
	{
		if(!($cuePoint instanceof AdCuePoint))
			return $scene;
			
		if(!$scene)
			$scene = kCuePointManager::generateCuePointXml($cuePoint, $scenes->addChild('scene-ad-cue-point'));
		
		if($cuePoint->getEndTime())
			$scene->addChild('sceneEndTime', kXml::integerToTime($cuePoint->getEndTime()));
		if($cuePoint->getName())
			$scene->addChild('sceneTitle', kMrssManager::stringToSafeXml($cuePoint->getName()));
		if($cuePoint->getSourceUrl())
			$scene->addChild('sourceUrl', htmlspecialchars($cuePoint->getSourceUrl()));

		$scene->addChild('adType', $cuePoint->getAdType());
		$scene->addChild('protocolType', $cuePoint->getSubType());
			
		return $scene;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaCuePointXmlParser::syndicate()
	 */
	public static function syndicate(CuePoint $cuePoint, SimpleXMLElement $scenes, SimpleXMLElement $scene = null)
	{
		if(!($cuePoint instanceof AdCuePoint))
			return $scene;
			
		if(!$scene)
			$scene = kCuePointManager::syndicateCuePointXml($cuePoint, $scenes->addChild('scene-ad-cue-point'));
		
		if($cuePoint->getEndTime())
			$scene->addChild('sceneEndTime', kXml::integerToTime($cuePoint->getEndTime()));
		if($cuePoint->getName())
			$scene->addChild('sceneTitle', kMrssManager::stringToSafeXml($cuePoint->getName()));
		if($cuePoint->getSourceUrl())
			$scene->addChild('sourceUrl', htmlspecialchars($cuePoint->getSourceUrl()));

		$scene->addChild('adType', $cuePoint->getAdType());
		$scene->addChild('protocolType', $cuePoint->getSubType());
			
		return $scene;
	}
}
