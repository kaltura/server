<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaConversionProfileAssetParamsFilter extends KalturaConversionProfileAssetParamsBaseFilter
{
	/**
	 * @var KalturaConversionProfileFilter
	 */
	public $conversionProfileIdFilter;
	
	/**
	 * @var KalturaAssetParamsFilter
	 */
	public $assetParamsIdFilter;
	
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(!$object_to_fill)
			$object_to_fill = new assetParamsConversionProfileFilter();
			
		$conversionProfileCriteria = new Criteria();
		$setConversionProfile = false;
		
		if($this->conversionProfileIdEqual)
		{
			$setConversionProfile = true;
			$conversionProfileCriteria->add(conversionProfile2Peer::ID, $this->conversionProfileIdEqual);
		}
		if($this->conversionProfileIdIn)
		{
			$setConversionProfile = true;
			$conversionProfileCriteria->add(conversionProfile2Peer::ID, explode(',', $this->conversionProfileIdIn), Criteria::IN);
		}
		if($this->conversionProfileIdFilter)
		{
			$setConversionProfile = true;
			$conversionProfileIdFilter = new conversionProfile2Filter();
			$this->conversionProfileIdFilter->toObject($conversionProfileIdFilter);
			$conversionProfileIdFilter->attachToCriteria($conversionProfileCriteria);
		}
		if($setConversionProfile)
		{
			$this->conversionProfileIdEqual = null;
			$this->conversionProfileIdFilter = null;
			$conversionProfileIdIn = conversionProfile2Peer::getIds($conversionProfileCriteria);
			if(count($conversionProfileIdIn))
				$this->conversionProfileIdIn = implode(',', $conversionProfileIdIn);
			else
				$this->conversionProfileIdIn = -1; // none existing conversion profile
		}
		
		
		$assetParamsCriteria = new Criteria();
		$setAssetParams = false;
		
		if($this->assetParamsIdEqual)
		{
			$setAssetParams = true;
			$assetParamsCriteria->add(assetParamsPeer::ID, $this->assetParamsIdEqual);
		}
		if($this->assetParamsIdIn)
		{
			$setAssetParams = true;
			$assetParamsCriteria->add(assetParamsPeer::ID, explode(',', $this->assetParamsIdIn), Criteria::IN);
		}
		if($this->assetParamsIdFilter)
		{
			$setAssetParams = true;
			$assetParamsIdFilter = new assetParamsFilter();
			$this->assetParamsIdFilter->toObject($assetParamsIdFilter);
			$assetParamsIdFilter->attachToCriteria($assetParamsCriteria);
		}
		if($setAssetParams)
		{
			$this->assetParamsIdEqual = null;
			$this->assetParamsIdFilter = null;
			assetParamsPeer::resetInstanceCriteriaFilter();
			$assetParamsIdIn = assetParamsPeer::getIds($assetParamsCriteria);
			if(count($assetParamsIdIn))
				$this->assetParamsIdIn = implode(',', $assetParamsIdIn);
			else
				$this->assetParamsIdIn = -1; // none existing flavor
		}
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
