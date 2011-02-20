<?php
/**
 * @package plugins.document
 * @subpackage api.enum
 */
class KalturaDocumentType extends KalturaEnum implements IKalturaPluginEnum
{
	const DOCUMENT = 11;
	const SWF = 12;
	const PDF = 13;
	
	public static function getAdditionalValues()
	{
		return array(
			'DOCUMENT' => self::DOCUMENT,
			'SWF' => self::SWF,
			'PDF' => self::PDF,			
		);
	}
}
?>