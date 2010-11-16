<?php
/**
 * @package api
 * @subpackage enum
 */
class DocumentAssetType extends KalturaAssetType
{
	const DOCUMENT = 'Document';
	const SWF = 'SWF';
	const PDF = 'PDF';
	
	/**
	 * @var DocumentAssetType
	 */
	protected static $instance;

	/**
	 * @return DocumentAssetType
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new DocumentAssetType();
			
		return self::$instance;
	}
	
	public static function getAdditionalValues()
	{
		return array(
			'DOCUMENT' => self::DOCUMENT,
			'SWF' => self::SWF,
			'PDF' => self::PDF,
		);
	}
	
	public function getPluginName()
	{
		return DocumentPlugin::getPluginName();
	}
}
