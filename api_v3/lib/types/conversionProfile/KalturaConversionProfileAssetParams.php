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

	/**
	 * Asset params system name
	 *  
	 * @var string
	 * @filter eq,in
	 */
	public $systemName;
	
	private static $map_between_objects = array
	(
		'conversionProfileId',
		'assetParamsId' => 'flavorParamsId',
		'readyBehavior',
		'origin',
		'systemName',
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