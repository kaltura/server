<?php
/**
 * Enable annotation ingestion from XML bulk upload
 * @package plugins.annotation
 */
class AnnotationBulkUploadXmlPlugin extends KalturaPlugin implements IKalturaPending, IKalturaSchemaContributor
{
	const PLUGIN_NAME = 'annotationBulkUploadXml';
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
		$annotationDependency = new KalturaDependency(AnnotationPlugin::getPluginName());
		
		return array($bulkUploadXmlDependency, $annotationDependency);
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
	<xs:complexType name="T_scene">
		<xs:complexContent>
			<xs:extension base="cuePoint:T_scene">
				<xs:sequence>
					<xs:element name="sceneEndTime" minOccurs="1" maxOccurs="1" type="xs:time" />
					<xs:element name="sceneText" minOccurs="0" maxOccurs="1" type="xs:string" />
					<xs:element name="parentId" minOccurs="0" maxOccurs="1" type="xs:string" />
				</xs:sequence>
			</xs:extension>
		</xs:complexContent>
	</xs:complexType>
	
	<xs:element name="scene" type="T_scene" substitutionGroup="cuePoint:scene" />
		';
		
		return $xsd;
	}
}
