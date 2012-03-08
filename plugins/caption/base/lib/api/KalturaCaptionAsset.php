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
	
	/**
	 * The status of the asset
	 * 
	 * @var KalturaCaptionAssetStatus
	 * @readonly 
	 * @filter eq,in,notin
	 */
	public $status;
	
	private static $map_between_objects = array
	(
		"captionParamsId" => "flavorParamsId",
		"language",
		"isDefault" => "default",
		"label",
		"format" => "containerFormat",
		"status",
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

	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if (!is_null($this->captionParamsId))
		{
			$dbAssetParams = assetParamsPeer::retrieveByPK($this->captionParamsId);
			if ($dbAssetParams)
			{
				$object_to_fill->setFromAssetParams($dbAssetParams);
			}
		}
		
		return parent::toInsertableObject ($object_to_fill, $props_to_skip);
	}
	
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('format');
	}
}
