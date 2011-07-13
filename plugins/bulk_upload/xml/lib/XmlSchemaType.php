<?php
/**
 * @package plugins.bulkUploadXml
 * @subpackage lib
 */
class XmlSchemaType implements IKalturaPluginEnum, SchemaType
{
	const BULK_UPLOAD_XML = 'bulkUploadXML';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'BULK_UPLOAD_XML' => self::BULK_UPLOAD_XML,
		);
	}
}
