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
		
		if($this->conversionProfileIdEqual)
			$conversionProfileCriteria->add(conversionProfile2Peer::ID, $this->conversionProfileIdEqual);
		if($this->conversionProfileIdIn)
			$conversionProfileCriteria->add(conversionProfile2Peer::ID, explode(',', $this->conversionProfileIdIn), Criteria::IN);
		if($this->conversionProfileIdFilter)
		{
			$conversionProfileIdFilter = new conversionProfile2Filter();
			$this->conversionProfileIdFilter->toObject($conversionProfileIdFilter);
			$conversionProfileIdFilter->attachToCriteria($conversionProfileCriteria);
		}
		$this->conversionProfileIdEqual = null;
		$this->conversionProfileIdFilter = null;
		$this->conversionProfileIdIn = conversionProfile2Peer::getIds($conversionProfileCriteria);
		
		
		$assetParamsCriteria = new Criteria();
		
		if($this->assetParamsIdEqual)
			$assetParamsCriteria->add(assetPeer::ID, $this->assetParamsIdEqual);
		if($this->assetParamsIdIn)
			$assetParamsCriteria->add(assetPeer::ID, explode(',', $this->assetParamsIdIn), Criteria::IN);
		if($this->assetParamsIdFilter)
		{
			$assetParamsIdFilter = new conversionProfile2Filter();
			$this->assetParamsIdFilter->toObject($assetParamsIdFilter);
			$assetParamsIdFilter->attachToCriteria($assetParamsCriteria);
		}
		$this->assetParamsIdEqual = null;
		$this->assetParamsIdFilter = null;
		assetParamsPeer::resetInstanceCriteriaFilter();
		$this->assetParamsIdIn = assetParamsPeer::getIds($assetParamsCriteria);
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
