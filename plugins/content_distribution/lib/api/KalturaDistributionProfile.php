<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 * @abstract
 */
abstract class KalturaDistributionProfile extends KalturaObject implements IFilterable
{
	/**
	 * Auto generated unique id
	 * 
	 * @readonly
	 * @var int
	 * @filter eq,in
	 */
	public $id;

	/**
	 * Profile creation date as Unix timestamp (In seconds)
	 * 
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;

	/**
	 * Profile last update date as Unix timestamp (In seconds)
	 * 
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;

	/**
	 * @readonly
	 * @var int
	 */
	public $partnerId;

	/**
	 * @insertonly
	 * @var KalturaDistributionProviderType
	 */
	public $providerType;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var KalturaDistributionProfileStatus
	 * @filter eq,in
	 */
	public $status;

	/**
	 * @var KalturaDistributionProfileActionStatus
	 */
	public $submitEnabled;

	/**
	 * @var KalturaDistributionProfileActionStatus
	 */
	public $updateEnabled;

	/**
	 * @var KalturaDistributionProfileActionStatus
	 */
	public $deleteEnabled;

	/**
	 * @var KalturaDistributionProfileActionStatus
	 */
	public $reportEnabled;

	/**
	 * Comma separated flavor params ids that should be auto converted
	 * @var string
	 */
	public $autoCreateFlavors;

	/**
	 * Comma separated thumbnail params ids that should be auto generated
	 * @var string
	 */
	public $autoCreateThumb;

	/**
	 * Comma separated flavor params ids that should be submitted if ready
	 * @var string
	 */
	public $optionalFlavorParamsIds;

	/**
	 * Comma separated flavor params ids that required to be ready before submission
	 * @var string
	 */
	public $requiredFlavorParamsIds;

	/**
	 * Thumbnail dimensions that should be submitted if ready
	 * @var KalturaDistributionThumbDimensionsArray
	 */
	public $optionalThumbDimensions;

	/**
	 * Thumbnail dimensions that required to be readt before submission
	 * @var KalturaDistributionThumbDimensionsArray
	 */
	public $requiredThumbDimensions;
	
	/**
	 * Asset Distribution Rules for assets that should be submitted if ready
	 * @var KalturaAssetDistributionRulesArray
	 */
	public $optionalAssetDistributionRules;
	
	/**
	 * Assets Asset Distribution Rules for assets that are required to be ready before submission
	 * @var KalturaAssetDistributionRulesArray
	 */
	public $requiredAssetDistributionRules;
		
	/**
	 * If entry distribution sunrise not specified that will be the default since entry creation time, in seconds
	 * @var int
	 */
	public $sunriseDefaultOffset;
	
	/**
	 * If entry distribution sunset not specified that will be the default since entry creation time, in seconds
	 * @var int
	 */
	public $sunsetDefaultOffset;
	
	/**
	 * The best external storage to be used to download the asset files from
	 * @var int
	 */
	public $recommendedStorageProfileForDownload;
	
	/**
	 * The best Kaltura data center to be used to download the asset files to
	 * @var int
	 */
	public $recommendedDcForDownload;
	
	/**
	 * The best Kaltura data center to be used to execute the distribution job
	 * @var int
	 */
	public $recommendedDcForExecute;
	
	/**
	 * Updates should not be done on events on these asset types' context
	 * @var KalturaAssetTypeInfoArray
	 */
	public $assetTypesNotAllowed;

	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	 (
		'id',
		'createdAt',
		'updatedAt',
		'partnerId',
		'providerType',
		'name',
		'status',
		'submitEnabled',
		'updateEnabled',
		'deleteEnabled',
		'reportEnabled',
		'autoCreateFlavors',
		'autoCreateThumb',
		'optionalFlavorParamsIds',
		'requiredFlavorParamsIds',
		'sunriseDefaultOffset',
		'sunsetDefaultOffset',
		'recommendedStorageProfileForDownload',
		'recommendedDcForDownload',
		'recommendedDcForExecute',
		'assetTypesNotAllowed',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			return null;
			
		parent::toObject($dbObject, $skip);
		
		if($this->optionalThumbDimensions)
		{
			$optionalThumbDimensionsArray = array();
			foreach($this->optionalThumbDimensions as $optionalThumbDimensions)
				$optionalThumbDimensionsArray[] = $optionalThumbDimensions->toObject();
		
			$dbObject->setOptionalThumbDimensionsObjects($optionalThumbDimensionsArray);
		}
			
		if($this->requiredThumbDimensions)
		{
			$requiredThumbDimensionsArray = array();
			foreach($this->requiredThumbDimensions as $requiredThumbDimensions)
				$requiredThumbDimensionsArray[] = $requiredThumbDimensions->toObject();
				
			$dbObject->setRequiredThumbDimensionsObjects($requiredThumbDimensionsArray);
		}
		
		if($this->optionalAssetDistributionRules)
		{
			$optionalAssetDistributionRulesArray = array();
			foreach($this->optionalAssetDistributionRules as $optionalAssetDistributionRule)
			{
				$optionalAssetDistributionRulesArray[] = $optionalAssetDistributionRule->toObject();
			}
		
			$dbObject->setOptionalAssetDistributionRules($optionalAssetDistributionRulesArray);
		}
		
			
		if($this->requiredAssetDistributionRules)
		{
			$requiredAssetDistributionRulesArray = array();
			foreach($this->requiredAssetDistributionRules as $requiredAssetDistributionRule)
			{
				$requiredAssetDistributionRulesArray[] = $requiredAssetDistributionRule->toObject();
			}
		
			$dbObject->setRequiredAssetDistributionRules($requiredAssetDistributionRulesArray);
		}

		return $dbObject;
	}
	
	protected function doFromObject($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		if(!$sourceObject)
			return;
			
		parent::doFromObject($sourceObject, $responseProfile);

		$this->assetTypesNotAllowed = KalturaAssetTypeInfoArray::fromDbArray($sourceObject->getAssetTypesNotAllowed());

		if($this->shouldGet('optionalThumbDimensions', $responseProfile))
			$this->optionalThumbDimensions = KalturaDistributionThumbDimensionsArray::fromDbArray($sourceObject->getOptionalThumbDimensionsObjects());
		if($this->shouldGet('requiredThumbDimensions', $responseProfile))
			$this->requiredThumbDimensions = KalturaDistributionThumbDimensionsArray::fromDbArray($sourceObject->getRequiredThumbDimensionsObjects());
			
		if($this->shouldGet('optionalAssetDistributionRules', $responseProfile))
			$this->optionalAssetDistributionRules = KalturaAssetDistributionRulesArray::fromDbArray($sourceObject->getOptionalAssetDistributionRules());
		if($this->shouldGet('requiredAssetDistributionRules', $responseProfile))
			$this->requiredAssetDistributionRules = KalturaAssetDistributionRulesArray::fromDbArray($sourceObject->getRequiredAssetDistributionRules());
	}
	
	public function getExtraFilters()
	{
		return array(
		);
	}
	
	public function getFilterDocs()
	{
		return array(
		);
	}
}
