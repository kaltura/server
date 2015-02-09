<?php
/**
 * @package plugins.document
 * @subpackage api.objects
 */
class KalturaPdfFlavorParamsOutput extends KalturaFlavorParamsOutput 
{
	public function toObject($object = null, $skip = array())
	{
		if(is_null($object))
			$object = new PdfFlavorParamsOutput();
		
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
	
	// attributes that defined in flavorParams and not in PdfFlavorParamsOutput
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
	
	public function fromObject($dbPdfFlavorParamsOutput, IResponseProfile $responseProfile = null)
	{
		parent::fromObject($dbPdfFlavorParamsOutput, $responseProfile);
		
		if($dbPdfFlavorParamsOutput->getReadonly() == true){
			$this->readonly = 1;
		}else{
			$this->readonly = 0;
		}
	}
}