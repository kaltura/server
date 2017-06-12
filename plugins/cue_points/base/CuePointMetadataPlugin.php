<?php
/**
 * Enable custom metadata on ad cue point objects
 * @package plugins.cuePoint
 */
class CuePointMetadataPlugin extends KalturaPlugin implements IKalturaPending, IKalturaSchemaContributor, IKalturaSearchDataContributor, IKalturaElasticSearchDataContributor
{
	const PLUGIN_NAME = 'cuePointMetadata';
	const METADATA_PLUGIN_NAME = 'metadata';
	const METADATA_PLUGIN_VERSION_MAJOR = 2;
	const METADATA_PLUGIN_VERSION_MINOR = 0;
	const METADATA_PLUGIN_VERSION_BUILD = 0;
	
	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn()
	{
		$metadataVersion = new KalturaVersion(self::METADATA_PLUGIN_VERSION_MAJOR, self::METADATA_PLUGIN_VERSION_MINOR, self::METADATA_PLUGIN_VERSION_BUILD);
		
		$metadataDependency = new KalturaDependency(self::METADATA_PLUGIN_NAME, $metadataVersion);
		$cuePointDependency = new KalturaDependency(CuePointPlugin::getPluginName());
		
		return array($metadataDependency, $cuePointDependency);
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
			$coreType == BulkUploadXmlPlugin::getSchemaTypeCoreValue(XmlSchemaType::BULK_UPLOAD_XML)
			||
			$coreType == BulkUploadXmlPlugin::getSchemaTypeCoreValue(XmlSchemaType::BULK_UPLOAD_RESULT_XML)
		)
			return '
		
	<!-- ' . self::getPluginName() . ' -->
	
	<xs:element name="scene-customData" type="T_customData" substitutionGroup="scene-extension">
		<xs:annotation>
			<xs:documentation>XML for custom metadata</xs:documentation>
			<xs:appinfo>
				<example>
					<scene-ad-cue-point entryId="{entry id}">
						<sceneStartTime>00:00:05</sceneStartTime>
						<sceneTitle>my ad title</sceneTitle>
						<sourceUrl>http://source.to.my/ad.xml</sourceUrl>
						<adType>1</adType>
						<protocolType>1</protocolType>
						<scene-customData metadataProfile="MY_AD_METADATA_PROFILE_SYSTEM_NAME">
							<metadata>
								<adData>my ad custom data</adData>
							</metadata>
						</scene-customData>
					</scene-ad-cue-point>
				</example>
			</xs:appinfo>
		</xs:annotation>
	</xs:element>
			';
		
		if($coreType == CuePointPlugin::getSchemaTypeCoreValue(CuePointSchemaType::INGEST_API))
			return '
		
	<!-- ' . self::getPluginName() . ' -->
	
	<xs:complexType name="T_customData">
		<xs:sequence>
			<xs:any namespace="##local" processContents="skip" minOccurs="1" maxOccurs="1">
				<xs:annotation>
					<xs:documentation>Custom metadata XML according to schema profile</xs:documentation>
				</xs:annotation>		
			</xs:any>			
		</xs:sequence>
		
		<xs:attribute name="metadataId" use="optional" type="xs:int">
			<xs:annotation>
				<xs:documentation>Id of the custom metadata object</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="metadataProfile" use="optional" type="xs:string">
			<xs:annotation>
				<xs:documentation>Custom metadata schema profile system name</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="metadataProfileId" use="optional" type="xs:int">
			<xs:annotation>
				<xs:documentation>Custom metadata schema profile id</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		
	</xs:complexType>
	
	<xs:element name="scene-customData" type="T_customData" substitutionGroup="scene-extension">
		<xs:annotation>
			<xs:documentation>XML for custom metadata</xs:documentation>
			<xs:appinfo>
				<example>
					<scene-ad-cue-point entryId="{entry id}">
						<sceneStartTime>00:00:05</sceneStartTime>
						<sceneTitle>my ad title</sceneTitle>
						<sourceUrl>http://source.to.my/ad.xml</sourceUrl>
						<adType>1</adType>
						<protocolType>1</protocolType>
						<scene-customData metadataProfile="MY_AD_METADATA_PROFILE_SYSTEM_NAME">
							<metadata>
								<adData>my ad custom data</adData>
							</metadata>
						</scene-customData>
					</scene-ad-cue-point>
				</example>
			</xs:appinfo>
		</xs:annotation>
	</xs:element>
			';
		
		if($coreType == CuePointPlugin::getSchemaTypeCoreValue(CuePointSchemaType::SERVE_API))
			return '
		
	<!-- ' . self::getPluginName() . ' -->
	
	<xs:complexType name="T_customData">
		<xs:sequence>
			<xs:any namespace="##local" processContents="skip" minOccurs="1" maxOccurs="1">
				<xs:annotation>
					<xs:documentation>Custom metadata XML according to schema profile</xs:documentation>
				</xs:annotation>		
			</xs:any>			
		</xs:sequence>
		
		<xs:attribute name="metadataId" use="required" type="xs:int">
			<xs:annotation>
				<xs:documentation>Id of the custom metadata object</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="metadataVersion" use="required" type="xs:int">
			<xs:annotation>
				<xs:documentation>Custom metadata version</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="metadataProfile" use="optional" type="xs:string">
			<xs:annotation>
				<xs:documentation>Custom metadata schema profile system name</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="metadataProfileId" use="required" type="xs:int">
			<xs:annotation>
				<xs:documentation>Custom metadata schema profile id</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="metadataProfileName" use="optional" type="xs:string">
			<xs:annotation>
				<xs:documentation>Custom metadata schema profile name</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="metadataProfileVersion" use="required" type="xs:int">
			<xs:annotation>
				<xs:documentation>Custom metadata schema profile version</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		
	</xs:complexType>
	
	<xs:element name="scene-customData" type="T_customData" substitutionGroup="scene-extension">
		<xs:annotation>
			<xs:documentation>XML for custom metadata</xs:documentation>
			<xs:appinfo>
				<example>
					<scene-ad-cue-point entryId="{entry id}">
						<sceneStartTime>00:00:05</sceneStartTime>
						<sceneTitle>my ad title</sceneTitle>
						<sourceUrl>http://source.to.my/ad.xml</sourceUrl>
						<adType>1</adType>
						<protocolType>1</protocolType>
						<scene-customData	metadataId="{metadata id}" 
										metadataVersion="1" 
										metadataProfile="MY_METADATA_PROFILE_SYSTEM_NAME}"  
										metadataProfileId="{metadata profile id}"  
										metadataProfileName="my metadata profile" 
										metadataProfileVersion="1" 
						>
							<metadata>
								<adData>my ad custom data</adData>
							</metadata>
						</scene-customData>
					</scene-ad-cue-point>
				</example>
			</xs:appinfo>
		</xs:annotation>
	</xs:element>
			';
		
		return null;
	}
	
	public static function parseXml($objectType, SimpleXMLElement $scene, $partnerId, CuePoint $cuePoint)
	{
		$metadataElements = $scene->xpath('scene-customData');
		if(! count($metadataElements))
			return $cuePoint;
		
		foreach($metadataElements as $metadataElement)
		{
			$metadata = null;
			$metadataProfile = null;
			
			if(isset($metadataElement['metadataId']))
				$metadata = MetadataPeer::retrieveByPK($metadataElement['metadataId']);
			
			if($metadata)
			{
				$metadataProfile = $metadata->getMetadataProfile();
			}
			else
			{
				if(isset($metadataElement['metadataProfileId']))
					$metadataProfile = MetadataProfilePeer::retrieveByPK($metadataElement['metadataProfileId']);
				elseif(isset($metadataElement['metadataProfile']))
					$metadataProfile = MetadataProfilePeer::retrieveBySystemName($metadataElement['metadataProfile']);
				
				if($metadataProfile)
					$metadata = MetadataPeer::retrieveByObject($metadataProfile->getId(), $objectType, $cuePoint->getId());
			}
			
			if(! $metadataProfile)
				continue;
			
			if(! $metadata)
			{
				$metadata = new Metadata();
				$metadata->setPartnerId($partnerId);
				$metadata->setMetadataProfileId($metadataProfile->getId());
				$metadata->setMetadataProfileVersion($metadataProfile->getVersion());
				$metadata->setObjectType($objectType);
				$metadata->setObjectId($cuePoint->getId());
				$metadata->setStatus(KalturaMetadataStatus::VALID);
				
				foreach($metadataElement->children() as $metadataContent)
				{
					$xmlData = $metadataContent->asXML();
					$errorMessage = '';
					if(kMetadataManager::validateMetadata($metadataProfile->getId(), $xmlData, $errorMessage))
					{
						$metadata->save();
						
						$key = $metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
						kFileSyncUtils::file_put_contents($key, $xmlData);
						
						kEventsManager::raiseEvent(new kObjectDataChangedEvent($metadata));
					}
					
					break;
				}
			}
		}
		
		return $cuePoint;
	}
	
	/**
	 * @param SimpleXMLElement $parent
	 * @param SimpleXMLElement $append
	 */
	public static function appendXML(SimpleXMLElement $parent, SimpleXMLElement $append)
	{
		if(!$append)
			return;
			
		if(strlen(trim("$append")) == 0)
		{
			$xml = $parent->addChild($append->getName());
			foreach($append->children() as $child)
			{
				self::appendXML($xml, $child);
			}
		}
		else
		{
			$xml = $parent->addChild($append->getName(), "$append");
		}
		
		foreach($append->attributes() as $n => $v)
		{
			$xml->addAttribute($n, $v);
		}
	}
	
	public static function generateCuePointXml(SimpleXMLElement $scene, $objectType, $cuePointId)
	{
		$metadatas = MetadataPeer::retrieveAllByObject($objectType, $cuePointId);
		
		foreach($metadatas as $metadata)
		{
			/* @var $metadata Metadata */
			$metadataElement = $scene->addChild('scene-customData');
			$metadataElement->addAttribute('metadataId', $metadata->getId());
			$metadataElement->addAttribute('metadataVersion', $metadata->getVersion());
			$metadataElement->addAttribute('metadataProfileId', $metadata->getMetadataProfileId());
			$metadataElement->addAttribute('metadataProfileVersion', $metadata->getMetadataProfileVersion());
			
			$key = $metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
			$xml = kFileSyncUtils::file_get_contents($key, true, false);
			if($xml)
			{
				$xmlElement = new SimpleXMLElement($xml);
				self::appendXML($metadataElement, $xmlElement);
			}
			
			$metadataProfile = $metadata->getMetadataProfile();
			if(! $metadataProfile)
				continue;
			
			if($metadataProfile->getSystemName())
				$metadataElement->addAttribute('metadataProfile', $metadataProfile->getSystemName());
			if($metadataProfile->getName())
				$metadataElement->addAttribute('metadataProfileName', $metadataProfile->getName());
		}
		
		return $scene;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSearchDataContributor::getSearchData()
	 */
	public static function getSearchData(BaseObject $object)
	{
		if($object instanceof CuePoint)
		{
			if($object instanceof IMetadataObject && MetadataPlugin::isAllowedPartner($object->getPartnerId()))
			{
				return kMetadataManager::getSearchValuesByObject($object->getMetadataObjectType(), $object->getId());
			}
		}
					
		return null;
	}	

	public static function getElasticSearchData(BaseObject $object)
	{
		if($object instanceof CuePoint)
		{
			if($object instanceof IMetadataObject && MetadataPlugin::isAllowedPartner($object->getPartnerId()))
			{
				return kMetadataManager::getElasticSearchValuesByObject($object->getMetadataObjectType(), $object->getId());
			}
		}

		return null;
	}
}
