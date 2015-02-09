<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaConvartableJobData extends KalturaJobData
{
	/**
	 * @var string
	 * @deprecated
	 */
	public $srcFileSyncLocalPath;
	
	/**
	 * The translated path as used by the scheduler
	 * @var string
	 * @deprecated
	 */
	public $actualSrcFileSyncLocalPath;
	
	/**
	 * @var string
	 * @deprecated
	 */
	public $srcFileSyncRemoteUrl;

	/**
	 * 
	 * @var KalturaSourceFileSyncDescriptorArray
	 */
	public $srcFileSyncs;
	
	/**
	 * @var int
	 */
	public $engineVersion;
	
	/**
	 * @var int
	 */
	public $flavorParamsOutputId;
	
	/**
	 * @var KalturaFlavorParamsOutput
	 */
	public $flavorParamsOutput;
	
	/**
	 * @var int
	 */
	public $mediaInfoId;
	
	/**
	 * @var int
	 */
	public $currentOperationSet;
	
	/**
	 * @var int
	 */
	public $currentOperationIndex;
	
	/**
	 * @var KalturaKeyValueArray
	 */
	public $pluginData;
	
	private static $map_between_objects = array
	(
		"srcFileSyncs",
		"engineVersion" ,
		"mediaInfoId" ,
		"flavorParamsOutputId" ,
		"currentOperationSet" ,
		"currentOperationIndex" ,
		"pluginData",
	);


	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	    
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject(  $dbConvartableJobData = null, $props_to_skip = array()) 
	{
		if(is_null($dbConvartableJobData))
			$dbConvartableJobData = new kConvartableJobData();
			
		return parent::toObject($dbConvartableJobData, $props_to_skip);
	}
	    
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject($srcObj)
	 */
	public function fromObject($srcObj, IResponseProfile $responseProfile = null) 
	{
		/* @var $srcObj kConvartableJobData */
		$srcObj->migrateOldSerializedData();
			
		return parent::fromObject($srcObj, $responseProfile);
	}
}
