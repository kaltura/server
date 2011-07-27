<?php
/**
 * Enable custom metadata on ad cue point objects
 * @package plugins.cuePoint
 */
class CuePointMetadataPlugin extends KalturaPlugin implements IKalturaPending, IKalturaSchemaContributor
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
		$metadataVersion = new KalturaVersion(
			self::METADATA_PLUGIN_VERSION_MAJOR,
			self::METADATA_PLUGIN_VERSION_MINOR,
			self::METADATA_PLUGIN_VERSION_BUILD);
			
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
		)
		return '
		
	<!-- ' . self::getPluginName() . ' -->
	
	<xs:element name="scene-customData" type="T_customData" substitutionGroup="scene-extension" />
			';
		
		if($coreType == CuePointPlugin::getSchemaTypeCoreValue(CuePointSchemaType::INGEST_API))
		return '
		
	<!-- ' . self::getPluginName() . ' -->
	
	<xs:complexType name="T_customData">
		<xs:sequence>
			<xs:any namespace="##local" processContents="skip"/>			
		</xs:sequence>
		
		<xs:attribute name="metadataId" use="optional" type="xs:int"/>
		<xs:attribute name="metadataProfile" use="optional" type="xs:string"/>
		<xs:attribute name="metadataProfileId" use="optional" type="xs:int"/>
		
	</xs:complexType>
	
	<xs:element name="scene-customData" type="T_customData" substitutionGroup="scene-extension" />
			';
		
		if($coreType == CuePointPlugin::getSchemaTypeCoreValue(CuePointSchemaType::SERVE_API))
		return '
		
	<!-- ' . self::getPluginName() . ' -->
	
	<xs:complexType name="T_customData">
		<xs:sequence>
			<xs:any namespace="##local" processContents="skip"/>			
		</xs:sequence>
		
		<xs:attribute name="metadataId" use="required" type="xs:int"/>
		<xs:attribute name="metadataVersion" use="required" type="xs:int"/>
		<xs:attribute name="metadataProfile" use="optional" type="xs:string"/>
		<xs:attribute name="metadataProfileId" use="required" type="xs:int"/>
		<xs:attribute name="metadataProfileName" use="optional" type="xs:int"/>
		<xs:attribute name="metadataProfileVersion" use="required" type="xs:int"/>
		
	</xs:complexType>
	
	<xs:element name="scene-customData" type="T_customData" substitutionGroup="scene-extension" />
			';
		
		return null;
	}
	
	public static function parseXml($objectType, SimpleXMLElement $scene, $partnerId, CuePoint $cuePoint)
	{
		$metadataElements = $scene->xpath('scene-customData');
		if(!count($metadataElements))
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
			
			if(!$metadataProfile)
				continue;
		
			if(!$metadata)
			{
				$metadata = new Metadata();
				$metadata->setPartnerId($partnerId);
				$metadata->setMetadataProfileId($metadataProfile->getId());
				$metadata->setMetadataProfileVersion($metadataProfile->getVersion());
				$metadata->setObjectType($objectType);
				$metadata->setObjectId($cuePoint->getId());
				$metadata->setStatus(KalturaMetadataStatus::INVALID);
				$metadata->save();
				
				foreach($metadataElement->children() as $metadataContent)
				{
					$xmlData = $metadataContent->asXML();
					$key = $metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
					kFileSyncUtils::file_put_contents($key, $xmlData);
					
					$errorMessage = '';
					$status = kMetadataManager::validateMetadata($metadata, $errorMessage);
					if($status == KalturaMetadataStatus::VALID)
						kEventsManager::raiseEvent(new kObjectDataChangedEvent($metadata));
						
					return $cuePoint;
				}
			}
		}
		
		return $cuePoint;
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
				$xmlElementName = $xmlElement->getName();
				$metadataElement->$xmlElementName = $xmlElement;
			}
			
			$metadataProfile = $metadata->getMetadataProfile();
			if(!$metadataProfile)
				continue;
			
			if($metadataProfile->getSystemName())
				$metadataElement->addAttribute('metadataProfile', $metadataProfile->getSystemName());
			if($metadataProfile->getName())
				$metadataElement->addAttribute('metadataProfileName', $metadataProfile->getName());
		}

		return $scene;
	}
}
