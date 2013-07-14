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
		if(
			$coreType != BulkUploadXmlPlugin::getSchemaTypeCoreValue(XmlSchemaType::BULK_UPLOAD_XML)
			&&
			$coreType != BulkUploadXmlPlugin::getSchemaTypeCoreValue(XmlSchemaType::BULK_UPLOAD_RESULT_XML)
		)
			return null;
	
		$xsd = '
		
	<!-- ' . self::getPluginName() . ' -->
	
	<xs:complexType name="T_distribution">
		<xs:sequence>
			<xs:choice minOccurs="1" maxOccurs="1">
				<xs:element name="distributionProvider" minOccurs="1" maxOccurs="1" type="KalturaDistributionProviderType">
					<xs:annotation>
						<xs:documentation>The provider (Distribution Partner) that the entry is distributed to</xs:documentation>
					</xs:annotation>
				</xs:element>
				<xs:element name="distributionProfileId" minOccurs="1" maxOccurs="1" type="xs:int">
					<xs:annotation>
						<xs:documentation>The identifier of the distribution profile to use for entry distribution</xs:documentation>
					</xs:annotation>
				</xs:element>
				<xs:element name="distributionProfile" minOccurs="1" maxOccurs="1">
					<xs:annotation>
						<xs:documentation>The system name of the distribution profile to use for entry distribution</xs:documentation>
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
						The date and time that the entry becomes available on the remote site.<br/>
						If not specified, the entry scheduling date and time are used.
					</xs:documentation>
				</xs:annotation>
			</xs:element>
			<xs:element name="sunset" minOccurs="0" maxOccurs="1" type="xs:dateTime">
				<xs:annotation>
					<xs:documentation>
						The date and time that the entry becomes unavailable on the remote site.<br/>
						If not specified, the entry scheduling date and time are used.
					</xs:documentation>
				</xs:annotation>
			</xs:element>
			<xs:element name="flavorAssetIds" minOccurs="0" maxOccurs="1" type="xs:string">
				<xs:annotation>
					<xs:documentation>
						Comma-separated list of existing flavor asset IDs for the distribution destination.<br/>
						Used only for an existing entry.
					</xs:documentation>
				</xs:annotation>
			</xs:element>
			<xs:element name="thumbAssetIds" minOccurs="0" maxOccurs="1" type="xs:string">
				<xs:annotation>
					<xs:documentation>
						Comma-separated list of existing thumbnail asset IDs for the distribution destination.<br/>
						Used only for an existing entry.
					</xs:documentation>
				</xs:annotation>
			</xs:element>
		
			<xs:element ref="distribution-extension" minOccurs="0" maxOccurs="unbounded" />
			
		</xs:sequence>
		
		<xs:attribute name="entryDistributionId" use="optional" type="xs:int">
			<xs:annotation>
				<xs:documentation>The identifier of an entry distribution object that an update/delete action applies to</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="submitWhenReady" use="optional" type="xs:boolean" default="false">
			<xs:annotation>
				<xs:documentation>Indicates that the entry should be submitted when it is possible</xs:documentation>
			</xs:annotation>
		</xs:attribute>		
	</xs:complexType>

	<xs:complexType name="T_distributions">
		<xs:sequence>
			<xs:element ref="distribution" maxOccurs="unbounded" minOccurs="0">
				<xs:annotation>
					<xs:documentation>All distributions</xs:documentation>
				</xs:annotation>
			</xs:element>
		</xs:sequence>
	</xs:complexType>
	
	<xs:element name="distributions" type="T_distributions" substitutionGroup="item-extension">
		<xs:annotation>
			<xs:documentation>All custom metadata elemets</xs:documentation>
			<xs:appinfo>
				<example>
					<distributions>
						<distribution>
							<distributionProfile>MY_DISTRIBUTION_PROFILE</distributionProfile>
							<sunrise>2011-10-26T21:32:52</sunrise>
							<sunset>2011-12-26T21:32:52</sunset>
						</distribution>
					</distributions>
				</example>
			</xs:appinfo>
		</xs:annotation>
	</xs:element>
	
	<xs:element name="distribution" type="T_distribution">
		<xs:annotation>
			<xs:documentation>Details related to a content distribution submission</xs:documentation>
			<xs:appinfo>
				<example>
					<distribution>
						<distributionProfile>MY_DISTRIBUTION_PROFILE</distributionProfile>
						<sunrise>2011-10-26T21:32:52</sunrise>
						<sunset>2011-12-26T21:32:52</sunset>
					</distribution>
				</example>
			</xs:appinfo>
		</xs:annotation>
	</xs:element>
	
	<xs:element name="distribution-extension" />
		';
		
		return $xsd;
	}
}
