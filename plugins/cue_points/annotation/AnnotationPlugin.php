<?php
/**
 * Enable annotation cue point objects management on entry objects
 * @package plugins.annotation
 */
class AnnotationPlugin extends KalturaPlugin implements IKalturaServices, IKalturaCuePoint
{
	const PLUGIN_NAME = 'annotation';
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
	 * @see IKalturaServices::getServicesMap()
	 */
	public static function getServicesMap()
	{
		$map = array(
			'annotation' => 'AnnotationService',
		);
		return $map;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('AnnotationCuePointType');
	
		if($baseEnumName == 'CuePointType')
			return array('AnnotationCuePointType');
			
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
		if($baseClass == 'KalturaCuePoint' && $enumValue == self::getCuePointTypeCoreValue(AnnotationCuePointType::ANNOTATION))
			return new KalturaAnnotation();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'CuePoint' && $enumValue == self::getCuePointTypeCoreValue(AnnotationCuePointType::ANNOTATION))
			return 'Annotation';
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::contributeToSchema()
	 */
	public static function contributeToSchema($type)
	{
		$coreType = kPluginableEnumsManager::apiToCore('SchemaType', $type);
		
		if(
			$coreType == SchemaType::SYNDICATION
			||
			$coreType == CuePointPlugin::getSchemaTypeCoreValue(CuePointSchemaType::SERVE_API)
		)
			return '
		
	<!-- ' . self::getPluginName() . ' -->
	
	<xs:complexType name="T_scene_annotation">
		<xs:complexContent>
			<xs:extension base="T_scene">
				<xs:sequence>
					<xs:element name="sceneEndTime" minOccurs="1" maxOccurs="1" type="xs:time" />
					<xs:element name="sceneText" minOccurs="0" maxOccurs="1" type="xs:string" />
					<xs:element name="parent" minOccurs="0" maxOccurs="1" type="xs:string" />
					<xs:element name="parentId" minOccurs="0" maxOccurs="1" type="xs:string" />
					
					<xs:element ref="scene-extension" minOccurs="0" maxOccurs="unbounded" />
				</xs:sequence>
			</xs:extension>
		</xs:complexContent>
	</xs:complexType>
	
	<xs:element name="scene-annotation" type="T_scene_annotation" substitutionGroup="scene" />
		';
		
		if($coreType == CuePointPlugin::getSchemaTypeCoreValue(CuePointSchemaType::INGEST_API))
			return '
		
	<!-- ' . self::getPluginName() . ' -->
	
	<xs:complexType name="T_scene_annotation">
		<xs:complexContent>
			<xs:extension base="T_scene">
				<xs:sequence>
					<xs:element name="sceneEndTime" minOccurs="1" maxOccurs="1" type="xs:time" />
					<xs:element name="sceneText" minOccurs="0" maxOccurs="1" type="xs:string" />
					<xs:choice minOccurs="0" maxOccurs="1">
						<xs:element name="parent" minOccurs="1" maxOccurs="1" type="xs:string" />
						<xs:element name="parentId" minOccurs="1" maxOccurs="1" type="xs:string" />
					</xs:choice>
					
					<xs:element ref="scene-extension" minOccurs="0" maxOccurs="unbounded" />
				</xs:sequence>
			</xs:extension>
		</xs:complexContent>
	</xs:complexType>
	
	<xs:element name="scene-annotation" type="T_scene_annotation" substitutionGroup="scene" />
		';
		
		return null;
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
	 * @see IKalturaCuePointXmlParser::getApiValue()
	 */
	public static function parseXml(SimpleXMLElement $scene, $partnerId, CuePoint $cuePoint = null)
	{
		if($scene->getName() != 'scene-annotation')
			return $cuePoint;
			
		if(!$cuePoint)
			$cuePoint = kCuePointManager::parseXml($scene, $partnerId, new Annotation());
			
		if(!($cuePoint instanceof Annotation))
			return null;
		
		$cuePoint->setEndTime(kXml::timeToInteger($scene->sceneEndTime));
		if(isset($scene->sceneText))
			$cuePoint->setText($scene->sceneText);
			
		$parentCuePoint = null;
		if(isset($scene->parentId))
			$parentCuePoint = CuePointPeer::retrieveByPK($scene->parentId);
		elseif(isset($scene->parent))
			$parentCuePoint = CuePointPeer::retrieveBySystemName($scene->parent);
		if($parentCuePoint)
			$cuePoint->setParentId($parentCuePoint->getId());
		
		return $cuePoint;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaCuePointXmlParser::generateXml()
	 */
	public static function generateXml(CuePoint $cuePoint, SimpleXMLElement $scenes, SimpleXMLElement $scene = null)
	{
		if(!($cuePoint instanceof Annotation))
			return $scene;
			
		if(!$scene)
			$scene = kCuePointManager::generateCuePointXml($cuePoint, $scenes->addChild('scene-annotation'));
			
		$scene->addChild('sceneEndTime', kXml::integerToTime($cuePoint->getEndTime()));
		if($cuePoint->getText())
			$scene->addChild('sceneText', kMrssManager::stringToSafeXml($cuePoint->getText()));
		if($cuePoint->getParentId())
		{
			$parentCuePoint = CuePointPeer::retrieveByPK($cuePoint->getParentId());
			if($parentCuePoint)
			{
				if($parentCuePoint->getSystemName())
					$scene->addChild('parent', kMrssManager::stringToSafeXml($parentCuePoint->getSystemName()));
				$scene->addChild('parentId', $parentCuePoint->getId());
			}
		}
			
		return $scene;
	}
}
