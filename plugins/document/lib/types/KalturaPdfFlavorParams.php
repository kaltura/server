<?php
/**
 * @package plugins.document
 * @subpackage api.objects
 */
class KalturaPdfFlavorParams extends KalturaFlavorParams 
{
	public function toObject($object = null, $skip = array())
	{
		if(is_null($object))
			$object = new PdfFlavorParams();
		
		parent::toObject($object, $skip);
		$object->setType(DocumentPlugin::getAssetTypeCoreValue(DocumentAssetType::PDF));
	}
	
	/**
	 * @var bool
	 */
	public $readonly;
	
	private static $map_between_objects = array
	(
		'readonly',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}