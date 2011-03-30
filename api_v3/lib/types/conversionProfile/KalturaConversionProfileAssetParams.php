<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaConversionProfileAssetParams extends KalturaObject implements IFilterable 
{
	/**
	 * The id of the conversion profile
	 * 
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $conversionProfileId;
	
	/**
	 * The id of the asset params
	 * 
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $assetParamsId;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;

	/**
	 * The ingestion origin of the asset params
	 *  
	 * @var KalturaFlavorReadyBehaviorType
	 * @filter eq,in
	 */
	public $readyBehavior;

	/**
	 * The ingestion origin of the asset params
	 *  
	 * @var KalturaAssetParamsOrigin
	 * @filter eq,in
	 */
	public $origin;
	
	private static $map_between_objects = array
	(
		'conversionProfileId',
		'assetParamsId',
		'partnerId',
		'readyBehavior',
		'origin',
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	}
}