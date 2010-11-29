<?php

/**
 * Add & Manage Thumb Params
 *
 * @service thumbParams
 * @package api
 * @subpackage services
 */
class ThumbParamsService extends KalturaBaseService
{
	public function initService($partnerId, $puserId, $ksStr, $serviceName, $action)
	{
		parent::initService($partnerId, $puserId, $ksStr, $serviceName, $action);
		
		parent::applyPartnerFilterForClass(new conversionProfile2Peer());
		parent::applyPartnerFilterForClass(thumbAssetPeer::getInstance());
		parent::applyPartnerFilterForClass(thumbParamsOutputPeer::getInstance());
		parent::applyPartnerFilterForClass(thumbParamsPeer::getInstance()); // note that partner 0 is defined as partner group in service.ct
	}
	
	/**
	 * Add new Thumb Params
	 * 
	 * @action add
	 * @param KalturaThumbParams $thumbParams
	 * @return KalturaThumbParams
	 */
	public function addAction(KalturaThumbParams $thumbParams)
	{
		$thumbParams->validatePropertyMinLength("name", 1);
		
		$thumbParamsDb = new thumbParams();
		$thumbParams->toObject($thumbParamsDb);
		
		$thumbParamsDb->setPartnerId($this->getPartnerId());
		$thumbParamsDb->save();
		
		$thumbParams->fromObject($thumbParamsDb);
		return $thumbParams;
	}
	
	/**
	 * Get Thumb Params by ID
	 * 
	 * @action get
	 * @param int $id
	 * @return KalturaThumbParams
	 */
	public function getAction($id)
	{
		$thumbParamsDb = thumbParamsPeer::retrieveByPK($id);
		
		if (!$thumbParamsDb)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_ID_NOT_FOUND, $id);
			
		$thumbParams = KalturaThumbParamsFactory::getThumbParamsInstance($thumbParamsDb->getType());
		$thumbParams->fromObject($thumbParamsDb);
		
		return $thumbParams;
	}
	
	/**
	 * Update Thumb Params by ID
	 * 
	 * @action update
	 * @param int $id
	 * @param KalturaThumbParams $thumbParams
	 * @return KalturaThumbParams
	 */
	public function updateAction($id, KalturaThumbParams $thumbParams)
	{
		if ($thumbParams->name !== null)
			$thumbParams->validatePropertyMinLength("name", 1);
			
		$thumbParamsDb = thumbParamsPeer::retrieveByPK($id);
		if (!$thumbParamsDb)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_ID_NOT_FOUND, $id);
			
		$thumbParams->validatePropertyMinLength("name", 1);
		
		$thumbParamsDb = new thumbParams();
		$thumbParams->toObject($thumbParamsDb);
		$thumbParamsDb->save();
			
		$thumbParams->fromObject($thumbParamsDb);
		return $thumbParams;
	}
	
	/**
	 * Delete Thumb Params by ID
	 * 
	 * @action delete
	 * @param int $id
	 */
	public function deleteAction($id)
	{
		$thumbParamsDb = thumbParamsPeer::retrieveByPK($id);
		if (!$thumbParamsDb)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_ID_NOT_FOUND, $id);
			
		$thumbParamsDb->setDeletedAt(time());
		$thumbParamsDb->save();
	}
	
	/**
	 * List Thumb Params by filter with paging support (By default - all system default params will be listed too)
	 * 
	 * @action list
	 * @param KalturaThumbParamsFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaThumbParamsListResponse
	 */
	public function listAction(KalturaThumbParamsFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaThumbParamsFilter();

		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$thumbParamsFilter = new assetParamsFilter();
		
		$filter->toObject($thumbParamsFilter);
		
		$c = new Criteria();
		$thumbParamsFilter->attachToCriteria($c);
		
		$totalCount = thumbParamsPeer::doCount($c);
		$pager->attachToCriteria($c);
		$dbList = thumbParamsPeer::doSelect($c);

		$list = KalturaThumbParamsArray::fromDbArray($dbList);
		$response = new KalturaThumbParamsListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;    
	}
	
	/**
	 * Get Thumb Params by Conversion Profile ID
	 * 
	 * @action getByConversionProfileId
	 * @param int $conversionProfileId
	 * @return KalturaThumbParamsArray
	 */
	public function getByConversionProfileIdAction($conversionProfileId)
	{
		$conversionProfileDb = conversionProfile2Peer::retrieveByPK($conversionProfileId);
		if (!$conversionProfileDb)
			throw new KalturaAPIException(KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND, $conversionProfileId);
			
		$thumbParamsConversionProfilesDb = $conversionProfileDb->getthumbParamsConversionProfilesJointhumbParams();
		$thumbParamsDb = array();
		foreach($thumbParamsConversionProfilesDb as $item)
		{
			$thumbParamsDb[] = $item->getThumbParams();
		}
		
		$thumbParams = KalturaThumbParamsArray::fromDbArray($thumbParamsDb);
		
		return $thumbParams; 
	}
}