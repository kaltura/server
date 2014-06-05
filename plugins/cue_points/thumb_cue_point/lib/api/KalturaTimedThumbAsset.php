<?php
/**
 * @package plugins.thumbCuePoint
 * @subpackage api.objects
 */
class KalturaTimedThumbAsset extends KalturaThumbAsset  
{
	/**
	 * Associated thumb cue point ID
	 * @var string
	 * @insertonly
	 */
	public $cuePointId;

	
	private static $map_between_objects = array
	(
		"cuePointId",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new timedThumbAsset();
		
		return parent::toInsertableObject ($object_to_fill, $props_to_skip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validateCuePointAssociation();
		
		parent::validateForInsert($propertiesToSkip);
	}
	
	public function validateCuePointAssociation()
	{
		$this->validatePropertyNotNull("cuePointId");
		
		$dbCuePoint = CuePointPeer::retrieveByPK($this->cuePointId);
		if (!$dbCuePoint)
			throw new KalturaAPIException(KalturaCuePointErrors::CUE_POINT_ID_NOT_FOUND, $this->cuePointId);
			
		if(!($dbCuePoint instanceof ThumbCuePoint))
			throw new KalturaAPIException(KalturaCuePointErrors::CUE_POINT_PROVIDED_NOT_OF_TYPE_THUMB_CUE_POINT, $this->cuePointId);
			
		if($dbCuePoint->getAssetId() != null)
			throw new KalturaAPIException(KalturaCuePointErrors::CUE_POINT_ALREADY_ASSOCIATED_WITH_ASSET, $dbCuePoint->getAssetId());
	}
}