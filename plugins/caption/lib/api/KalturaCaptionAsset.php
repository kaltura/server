<?php
/**
 * @package plugins.caption
 * @subpackage api.objects
 */
class KalturaCaptionAsset extends KalturaAsset  
{
	/**
	 * The Caption Params used to create this Caption Asset
	 * 
	 * @var int
	 * @insertonly
	 */
	public $captionParamsId;
	
	/**
	 * The language of the caption asset content
	 * 
	 * @var KalturaLanguage
	 */
	public $language;
	
	/**
	 * The language of the caption asset content
	 * 
	 * @var KalturaLanguageCode
	 * @readonly
	 */
	public $languageCode;
	
	/**
	 * Is default caption asset of the entry
	 * 
	 * @var KalturaNullableBoolean
	 */
	public $isDefault;
	
	/**
	 * Friendly label
	 * 
	 * @var string
	 */
	public $label;
	
	/**
	 * The caption format
	 * 
	 * @var KalturaCaptionType
	 * @filter eq,in
	 * @insertonly
	 */
	public $format;
	
	private static $map_between_objects = array
	(
		"captionParamsId" => "flavorParamsId",
		"language",
		"isDefault" => "default",
		"label",
		"format" => "containerFormat",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function fromObject($source_object)
	{
		$ret = parent::fromObject($source_object);
				
		$languageReflector = KalturaTypeReflectorCacher::get('KalturaLanguage');
		$languageCodeReflector = KalturaTypeReflectorCacher::get('KalturaLanguageCode');
		if($languageReflector && $languageCodeReflector)
		{
			$languageCode = $languageReflector->getConstantName($this->language);
			if($languageCode)
				$this->languageCode = $languageCodeReflector->getConstantValue($languageCode);
		}
			
		return $ret;
	}
}
