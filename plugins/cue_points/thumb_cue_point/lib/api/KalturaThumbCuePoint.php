<?php
/**
 * @package plugins.thumbCuePoint
 * @subpackage api.objects
 */
class KalturaThumbCuePoint extends KalturaCuePoint
{
	/**
	 * @var string
	 * @insertonly
	 */
	public $assetId;
	
	/**
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 */
	public $description;
	
	/**
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 */
	public $title;

	public function __construct()
	{
		$this->cuePointType = ThumbCuePointPlugin::getApiValue(ThumbCuePointType::THUMB);
	}
	
	private static $map_between_objects = array
	(
		"assetId",
		"title" => "name",
		"description" => "text",
	);
	
	/* (non-PHPdoc)
	 * @see KalturaCuePoint::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toInsertableObject()
	 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill))
			$object_to_fill = new ThumbCuePoint();
			
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		if($this->assetId !== null)	
			$this->validateTimedThumbAssetId();
		
		parent::validateForInsert($propertiesToSkip);
	}
	
	public function validateTimedThumbAssetId()
	{
		$timedThumb = assetPeer::retrieveByPK($this->assetId);
		
		if(!$timedThumb)
			throw new KalturaAPIException(KalturaErrors::ASSET_ID_NOT_FOUND, $this->assetId);
		
		if($timedThumb->getType() != kPluginableEnumsManager::apiToCore('assetType', KalturaAssetType::TIMED_THUMB_ASSET))
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_ID_IS_NOT_TIMED_THUMB_TYPE, $this->assetId);
	}
}