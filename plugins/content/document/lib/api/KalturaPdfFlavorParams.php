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
		return $object;
	}
	
	/**
	 * @var bool
	 */
	public $readonly;
	
	private static $map_between_objects = array
	(
		'readonly',
	);
	
	// attributes that defined in flavorParams and not in PdfFlavorParams
	private static $skip_attributes = array
	(
		"videoConstantBitrate",
		"videoBitrateTolerance",
	);
	
	public function getMapBetweenObjects()
	{
		$map = array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
		foreach(self::$skip_attributes as $skip_attribute)
		{
			if(isset($map[$skip_attribute]))
				unset($map[$skip_attribute]);
				
			$key = array_search($skip_attribute, $map);
			if($key !== false)
				unset($map[$key]);
		}
		return $map;
	}
}