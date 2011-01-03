<?php
/**
 * @package api
 * @subpackage enum
 */
class DocumentAssetType implements IKalturaPluginEnum, assetType
{
	const DOCUMENT = 'Document';
	const SWF = 'SWF';
	const PDF = 'PDF';
	
	public static function getAdditionalValues()
	{
		return array(
			'DOCUMENT' => self::DOCUMENT,
			'SWF' => self::SWF,
			'PDF' => self::PDF,
		);
	}
}
