<?php
/**
 * @package plugins.externalMedia
 * @subpackage model.enum
 */
class EsearchMediaEntryExportObjectType implements IKalturaPluginEnum, ExportObjectType
{
	const ESEARCH_MEDIA = 'esearchMedia';
	
	public static function getAdditionalValues()
	{
		return array(
			'ESEARCH_MEDIA' => self::ESEARCH_MEDIA,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array(
			ElasticSearchPlugin::getApiValue(self::ESEARCH_MEDIA) => 'Esearch Media',
		);
	}
}