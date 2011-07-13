<?php
/**
 * @package plugins.bulkUploadXml
 * @subpackage lib
 */
class CuePointSchemaType implements IKalturaPluginEnum, SchemaType
{
	const SERVE_API = 'serveAPI';
	const INGEST_API = 'ingestAPI';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'SERVE_API' => self::SERVE_API,
			'INGEST_API' => self::INGEST_API,
		);
	}
}
