<?php
/**
 * @package plugins.document
 * @subpackage model.enum
 */
class DocumentAssetType implements IKalturaPluginEnum, assetType
{
	const DOCUMENT = 'Document';
	const SWF = 'SWF';
	const PDF = 'PDF';
	const IMAGE = 'Image';
	
	public static function getAdditionalValues()
	{
		return array(
			'DOCUMENT' => self::DOCUMENT,
			'SWF' => self::SWF,
			'PDF' => self::PDF,
			'IMAGE' => self::IMAGE,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array();
	}
}
