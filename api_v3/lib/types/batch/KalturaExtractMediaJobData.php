<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaExtractMediaJobData extends KalturaConvartableJobData
{
	/**
	 * @var string
	 */
	public $flavorAssetId;
	
	/**
	 * @var bool
	 */
	public $calculateComplexity;
	
	/**
	 * @var bool
	 */
	public $extractId3Tags;
	
	/**
	 * The data output file
	 * @var string
	 */
	public $destDataFilePath;

	/**
	 * @var int
	 */
	public $detectGOP;

	private static $map_between_objects = array
	(
		"flavorAssetId",
		"calculateComplexity",
		"extractId3Tags",
		"destDataFilePath",
		"detectGOP",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kExtractMediaJobData();
			
		return parent::toObject($dbData);
	}
	
	/**
	 * @param string $subType
	 * @return int
	 */
	public function toSubType($subType)
	{
		return kPluginableEnumsManager::apiToCore('mediaParserType', $subType);
	}
	
	/**
	 * @param int $subType
	 * @return string
	 */
	public function fromSubType($subType)
	{
		return kPluginableEnumsManager::coreToApi('mediaParserType', $subType);
	}
}

