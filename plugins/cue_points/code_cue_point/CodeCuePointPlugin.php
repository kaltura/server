<?php
/**
 * Enable code cue point objects management on entry objects
 * @package plugins.codeCuePoint
 */
class CodeCuePointPlugin extends KalturaPlugin implements IKalturaCuePoint
{
	const PLUGIN_NAME = 'codeCuePoint';
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
			return array('CodeCuePointType');
	
		if($baseEnumName == 'CuePointType')
			return array('CodeCuePointType');
			
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
		if($baseClass == 'KalturaCuePoint' && $enumValue == self::getCuePointTypeCoreValue(CodeCuePointType::CODE))
			return new KalturaCodeCuePoint();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'CuePoint' && $enumValue == self::getCuePointTypeCoreValue(CodeCuePointType::CODE))
			return 'CodeCuePoint';
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
	
	<xs:complexType name="T_scene_codeCuePoint">
		<xs:complexContent>
			<xs:extension base="T_scene">
				<xs:sequence>
					<xs:element name="sceneEndTime" minOccurs="0" maxOccurs="1" type="xs:time">
						<xs:annotation>
							<xs:documentation>Cue point end time</xs:documentation>
						</xs:annotation>
					</xs:element>
					<xs:element name="code" minOccurs="0" maxOccurs="1">
						<xs:annotation>
							<xs:documentation>Textual code</xs:documentation>
						</xs:annotation>
						<xs:simpleType>
							<xs:restriction base="xs:string">
								<xs:maxLength value="250"/>
							</xs:restriction>
						</xs:simpleType>
					</xs:element>
					<xs:element name="description" minOccurs="0" maxOccurs="1" type="xs:string">
						<xs:annotation>
							<xs:documentation>Free text description</xs:documentation>
						</xs:annotation>
					</xs:element>
	
					<xs:element ref="scene-extension" minOccurs="0" maxOccurs="unbounded" />
				</xs:sequence>
			</xs:extension>
		</xs:complexContent>
	</xs:complexType>
	
	<xs:element name="scene-code-cue-point" type="T_scene_codeCuePoint" substitutionGroup="scene">
		<xs:annotation>
			<xs:documentation>Single code cue point element</xs:documentation>
			<xs:appinfo>
				<example>
					<scene-code-cue-point sceneId="{scene id}" entryId="{entry id}">
						<sceneStartTime>00:00:05.3</sceneStartTime>
						<tags>
							<tag>sample</tag>
							<tag>my_tag</tag>
						</tags>
						<code>MY_CODE</code>
						<description>my code cue point description</description>
					</scene-code-cue-point>
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
		if($scene->getName() != 'scene-code-cue-point')
			return $cuePoint;
			
		if(!$cuePoint)
			$cuePoint = kCuePointManager::parseXml($scene, $partnerId, new CodeCuePoint());
			
		if(!($cuePoint instanceof CodeCuePoint))
			return null;
		
		if(isset($scene->sceneEndTime))
			$cuePoint->setEndTime(kXml::timeToInteger($scene->sceneEndTime));
		if(isset($scene->code))
			$cuePoint->setName($scene->code);
		if(isset($scene->description))
			$cuePoint->setText($scene->description);
		
		return $cuePoint;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaCuePointXmlParser::generateXml()
	 */
	public static function generateXml(CuePoint $cuePoint, SimpleXMLElement $scenes, SimpleXMLElement $scene = null)
	{
		if(!($cuePoint instanceof CodeCuePoint))
			return $scene;
			
		if(!$scene)
			$scene = kCuePointManager::generateCuePointXml($cuePoint, $scenes->addChild('scene-code-cue-point'));
		
		if($cuePoint->getEndTime())
			$scene->addChild('sceneEndTime', kXml::integerToTime($cuePoint->getEndTime()));
		$scene->addChild('code', kMrssManager::stringToSafeXml($cuePoint->getName()));
		if($cuePoint->getText())
			$scene->addChild('description', kMrssManager::stringToSafeXml($cuePoint->getText()));
			
		return $scene;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaCuePointXmlParser::syndicate()
	 */
	public static function syndicate(CuePoint $cuePoint, SimpleXMLElement $scenes, SimpleXMLElement $scene = null)
	{
		if(!($cuePoint instanceof CodeCuePoint))
			return $scene;
			
		if(!$scene)
			$scene = kCuePointManager::syndicateCuePointXml($cuePoint, $scenes->addChild('scene-code-cue-point'));
		
		if($cuePoint->getEndTime())
			$scene->addChild('sceneEndTime', kXml::integerToTime($cuePoint->getEndTime()));
		$scene->addChild('code', kMrssManager::stringToSafeXml($cuePoint->getName()));
		if($cuePoint->getText())
			$scene->addChild('description', kMrssManager::stringToSafeXml($cuePoint->getText()));
			
		return $scene;
	}
}
