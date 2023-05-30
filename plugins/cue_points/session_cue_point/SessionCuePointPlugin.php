<?php
/**
 * Enable session cue point objects management on entry objects
 * @package plugins.sessionCuePoint
 */
class SessionCuePointPlugin extends BaseCuePointPlugin implements IKalturaCuePoint, IKalturaCuePointXmlParser
{
	const PLUGIN_NAME = 'sessionCuePoint';
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
		if (is_null($baseEnumName))
		{
			return array('SessionCuePointType', 'BaseEntrySessionCuePointCloneOptions');
		}
		if ($baseEnumName == 'CuePointType')
		{
			return array('SessionCuePointType');
		}
		if ($baseEnumName == 'BaseEntryCloneOptions')
		{
			return array('BaseEntrySessionCuePointCloneOptions');
		}
		
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
			self::CUE_POINT_VERSION_BUILD
		);
		
		$dependency = new KalturaDependency(self::CUE_POINT_NAME, $cuePointVersion);
		return array($dependency);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if ($baseClass == 'KalturaCuePoint' && $enumValue == self::getCuePointTypeCoreValue(SessionCuePointType::SESSION))
		{
			return new KalturaSessionCuePoint();
		}
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if ($baseClass == 'CuePoint' && $enumValue == self::getCuePointTypeCoreValue(SessionCuePointType::SESSION))
		{
			return 'SessionCuePoint';
		}
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::contributeToSchema()
	 */
	public static function contributeToSchema($type)
	{
		$coreType = kPluginableEnumsManager::apiToCore('SchemaType', $type);
		if ($coreType != SchemaType::SYNDICATION
			&& $coreType != CuePointPlugin::getSchemaTypeCoreValue(CuePointSchemaType::SERVE_API)
			&& $coreType != CuePointPlugin::getSchemaTypeCoreValue(CuePointSchemaType::INGEST_API)
		)
		{
			return null;
		}
		
		$xsd = '
		
	<!-- ' . self::getPluginName() . ' -->
	
	<xs:complexType name="T_scene_sessionCuePoint">
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
					
					<xs:element ref="scene-extension" minOccurs="0" maxOccurs="unbounded" />
				</xs:sequence>
			</xs:extension>
		</xs:complexContent>
	</xs:complexType>
	
	<xs:element name="scene-session-cue-point" type="T_scene_sessionCuePoint" substitutionGroup="scene">
		<xs:annotation>
			<xs:documentation>Single session cue point element</xs:documentation>
			<xs:appinfo>
				<example>
					<scene-session-cue-point sceneId="{scene id}" entryId="{entry id}" systemName="MY_SESSION_CUE_POINT_SYSTEM_NAME">
						<sceneStartTime>00:00:05</sceneStartTime>
						<tags>
							<tag>sample</tag>
							<tag>my_tag</tag>
						</tags>
						<sceneTitle>my session title</sceneTitle>
						<sourceUrl>http://source.to.my/session.xml</sourceUrl>
					</scene-session-cue-point>
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
	public static function getBaseEntryCloneOptionsCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('BaseEntryCloneOptions', $value);
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
		if ($scene->getName() != 'scene-session-cue-point')
		{
			return $cuePoint;
		}
		if (!$cuePoint)
		{
			$cuePoint = kCuePointManager::parseXml($scene, $partnerId, new SessionCuePoint());
		}
		if (!($cuePoint instanceof SessionCuePoint))
		{
			return null;
		}
		
		if (isset($scene->sceneEndTime))
		{
			$cuePoint->setEndTime(kXml::timeToInteger($scene->sceneEndTime));
		}
		if (isset($scene->sceneTitle))
		{
			$cuePoint->setName($scene->sceneTitle);
		}
		
		return $cuePoint;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaCuePointXmlParser::generateXml()
	 */
	public static function generateXml(CuePoint $cuePoint, SimpleXMLElement $scenes, SimpleXMLElement $scene = null)
	{
		if (!($cuePoint instanceof SessionCuePoint))
		{
			return $scene;
		}
		
		if (!$scene)
		{
			$scene = kCuePointManager::generateCuePointXml($cuePoint, $scenes->addChild('scene-session-cue-point'));
		}
		
		if ($cuePoint->getEndTime())
		{
			$scene->addChild('sceneEndTime', kXml::integerToTime($cuePoint->getEndTime()));
		}
		if ($cuePoint->getName())
		{
			$scene->addChild('sceneTitle', kMrssManager::stringToSafeXml($cuePoint->getName()));
		}
		
		return $scene;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaCuePointXmlParser::syndicate()
	 */
	public static function syndicate(CuePoint $cuePoint, SimpleXMLElement $scenes, SimpleXMLElement $scene = null)
	{
		if (!($cuePoint instanceof SessionCuePoint))
		{
			return $scene;
		}
		
		if (!$scene)
		{
			$scene = kCuePointManager::syndicateCuePointXml($cuePoint, $scenes->addChild('scene-session-cue-point'));
		}
		
		if ($cuePoint->getEndTime())
		{
			$scene->addChild('sceneEndTime', kXml::integerToTime($cuePoint->getEndTime()));
		}
		if ($cuePoint->getName())
		{
			$scene->addChild('sceneTitle', kMrssManager::stringToSafeXml($cuePoint->getName()));
		}
		
		return $scene;
	}
	
	public static function getTypesToIndexOnEntry()
	{
		return array();
	}
	
	public static function shouldCloneByProperty(entry $entry)
	{
		return $entry->shouldCloneByProperty(self::getBaseEntryCloneOptionsCoreValue(BaseEntrySessionCuePointCloneOptions::SESSION_CUE_POINTS), false);
	}
	
	public static function getTypesToElasticIndexOnEntry()
	{
		return array();
	}
}
