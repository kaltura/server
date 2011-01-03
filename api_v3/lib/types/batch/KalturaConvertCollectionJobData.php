<?php
/**
 * @package api
 * @subpackage objects
 */

/**
 */
class KalturaConvertCollectionJobData extends KalturaConvartableJobData
{
	/**
	 * @var string
	 */
	public $destDirLocalPath;
	
	/**
	 * @var string
	 */
	public $destDirRemoteUrl;
	
	/**
	 * @var string
	 */
	public $destFileName;
	
	/**
	 * @var string
	 */
	public $inputXmlLocalPath;
	
	/**
	 * @var string
	 */
	public $inputXmlRemoteUrl;
	
	/**
	 * @var string
	 */
	public $commandLinesStr;
	
	
	/**
	 * @var KalturaConvertCollectionFlavorDataArray
	 */
	public $flavors;
    
	private static $map_between_objects = array
	(
		"destDirLocalPath" ,
		"destDirRemoteUrl" ,
		"destFileName" ,
		"inputXmlLocalPath" ,
		"inputXmlRemoteUrl" ,
		"commandLinesStr" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function fromObject ( $source_object  )
	{
		parent::fromObject($source_object);
		$this->flavors = KalturaConvertCollectionFlavorDataArray::fromConvertCollectionFlavorDataArray($source_object->getFlavors());
	}
	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kConvertCollectionJobData();
			
		$dbData = parent::toObject($dbData, $props_to_skip);
		
		if(!is_null($this->flavors))
		{
			$flavors = array();
			foreach($this->flavors as $flavor)
				$flavors[] = $flavor->toObject();
			
			$dbData->setFlavors($flavors);
		}
		
		return $dbData;
	}
	
	/**
	 * @param string $subType
	 * @return int
	 */
	public function toSubType($subType)
	{
		return kPluginableEnumsManager::apiToCore('conversionEngineType', $subType);
	}
	
	/**
	 * @param int $subType
	 * @return string
	 */
	public function fromSubType($subType)
	{
		return kPluginableEnumsManager::coreToApi('conversionEngineType', $subType);
	}
}
