<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaConversionProfileAssetParams extends KalturaObject implements IRelatedFilterable 
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
	
	/**
	 * Starts conversion even if the decision layer reduced the configuration to comply with the source
	 * @var KalturaNullableBoolean
	 */
	public $forceNoneComplied;
	
	/**
	 * 
	 * Specifies how to treat the flavor after conversion is finished
	 * @var KalturaAssetParamsDeletePolicy
	 */
	public $deletePolicy;
	
	/**
	 * @var KalturaNullableBoolean
	 */
	public $isEncrypted;

	/**
	 * @var float
	 */
	public $contentAwareness;
	
	/**
	 * @var int
	 */
	public $chunkedEncodeMode;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $twoPass;

        /**
         * @var string
         */
        public $tags;

	private static $map_between_objects = array
	(
		'conversionProfileId',
		'assetParamsId' => 'flavorParamsId',
		'readyBehavior',
		'origin',
		'systemName',
		'forceNoneComplied',
		'deletePolicy',
		'isEncrypted',
		'contentAwareness',
		'chunkedEncodeMode',
		'twoPass',
		'tags',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	/* (non-PHPdoc)
	 * @see IFilterable::getExtraFilters()
	 */
	public function getExtraFilters()
	{
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IFilterable::getFilterDocs()
	 */
	public function getFilterDocs()
	{
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate($sourceObject, $propertiesToSkip)
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		/* @var $sourceObject flavorParamsConversionProfile */
		$assetParams = $sourceObject->getassetParams();
		if(!$assetParams)
			throw new KalturaAPIException(KalturaErrors::ASSET_ID_NOT_FOUND, $sourceObject->getFlavorParamsId());
			
		if($assetParams instanceof liveParams && $this->origin == KalturaAssetParamsOrigin::CONVERT_WHEN_MISSING)
			throw new KalturaAPIException(KalturaErrors::LIVE_PARAMS_ORIGIN_NOT_SUPPORTED, $sourceObject->getFlavorParamsId(), $assetParams->getType(), $this->origin);
	}
}
