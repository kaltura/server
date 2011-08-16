<?php
/**
 * Enable entry content distribution ingestion from XML bulk upload
 * @package plugins.contentDistribution
 */
class ContentDistributionBulkUploadXmlPlugin extends KalturaPlugin implements IKalturaPending, IKalturaSchemaContributor
{
	const PLUGIN_NAME = 'contentDistributionBulkUploadXml';
	const BULK_UPLOAD_XML_PLUGIN_NAME = 'bulkUploadXml';

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
		$bulkUploadXmlDependency = new KalturaDependency(self::BULK_UPLOAD_XML_PLUGIN_NAME);
		$contentDistributionDependency = new KalturaDependency(ContentDistributionPlugin::getPluginName());
		
		return array($bulkUploadXmlDependency, $contentDistributionDependency);
	}

	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::contributeToSchema()
	 */
	public static function contributeToSchema($type)
	{
		$coreType = kPluginableEnumsManager::apiToCore('SchemaType', $type);
		if($coreType != BulkUploadXmlPlugin::getSchemaTypeCoreValue(XmlSchemaType::BULK_UPLOAD_XML))
			return null;
	
		$xsd = '
		
	<!-- ' . self::getPluginName() . ' -->
	
	<xs:complexType name="T_distribution">
		<xs:sequence>
			<xs:choice minOccurs="1" maxOccurs="1">
				<xs:element name="distributionProvider" minOccurs="1" maxOccurs="1" type="KalturaDistributionProviderType">
					<xs:annotation>
						<xs:documentation>The provider to distribute the entry to</xs:documentation>
					</xs:annotation>
				</xs:element>				
				<xs:element name="distributionProfileId" minOccurs="1" maxOccurs="1" type="xs:int">
					<xs:annotation>
						<xs:documentation>ID of the distribution profile to distribute the entry to</xs:documentation>
					</xs:annotation>
				</xs:element>
				<xs:element name="distributionProfile" minOccurs="1" maxOccurs="1" type="xs:string">
					<xs:annotation>
						<xs:documentation>System name of the distribution profile to distribute the entry to</xs:documentation>
					</xs:annotation>
					<xs:simpleType>
						<xs:restriction base="xs:string">
							<xs:maxLength value="120"/>
						</xs:restriction>
					</xs:simpleType>
				</xs:element>
			</xs:choice>
			<xs:element name="sunrise" minOccurs="0" maxOccurs="1" type="xs:dateTime">
				<xs:annotation>
					<xs:documentation>
						The date and time that the entry will become available<br/>
						on the remote site<br/> 
						Taken from the entry if not specified
					</xs:documentation>
				</xs:annotation>
			</xs:element>
			<xs:element name="sunset" minOccurs="0" maxOccurs="1" type="xs:dateTime">
				<xs:annotation>
					<xs:documentation>
						The date and time that the entry will become unavailable<br/>
						on the remote site<br/>
						Taken from the entry if not specified
					</xs:documentation>
				</xs:annotation>
			</xs:element>
			<xs:element name="flavorAssetIds" minOccurs="0" maxOccurs="1" type="xs:string">
				<xs:annotation>
					<xs:documentation>
						Comma seperated list of existing flavor asset ids<br/>
						to be used in this distribution destination<br/>
						Could be used only on existing entry
					</xs:documentation>
				</xs:annotation>
			</xs:element>
			<xs:element name="thumbAssetIds" minOccurs="0" maxOccurs="1" type="xs:string">
				<xs:annotation>
					<xs:documentation>
						Comma seperated list of existing thumbnail asset ids<br/>
						to be used in this distribution destination<br/>
						Could be used only on existing entry
					</xs:documentation>
				</xs:annotation>
			</xs:element>
		
			<xs:element ref="distribution-extension" minOccurs="0" maxOccurs="unbounded" />
			
		</xs:sequence>
		
		<xs:attribute name="entryDistributionId" use="required" type="xs:int">
			<xs:annotation>
				<xs:documentation>ID of entry distribution to apply update/delete action on</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="submitWhenReady" use="optional" type="xs:boolean" default="false">
			<xs:annotation>
				<xs:documentation>Indicates that the entry should be submitted once it is possible</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		
	</xs:complexType>
	
	<xs:element name="distribution" type="T_distribution" substitutionGroup="item-extension">
		<xs:annotation>
			<xs:documentation>Content distribution submission</xs:documentation>
		</xs:annotation>
	</xs:element>
	
	<xs:element name="distribution-extension" />
		';
		
		return $xsd;
	}
}
