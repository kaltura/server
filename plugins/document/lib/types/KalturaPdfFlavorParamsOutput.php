<?php
/**
 * @package plugins.document
 * @subpackage api.objects
 */
class KalturaPdfFlavorParamsOutput extends KalturaFlavorParamsOutput 
{
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
	
	public function fromObject($dbPdfFlavorParamsOutput)
	{
		parent::fromObject($dbPdfFlavorParamsOutput);
		
		if($dbPdfFlavorParamsOutput->getReadonly() == true){
			$this->readonly = 1;
		}else{
			$this->readonly = 0;
		}
	}
}