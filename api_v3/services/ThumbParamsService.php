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
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		$this->applyPartnerFilterForClass('conversionProfile2');
		$this->applyPartnerFilterForClass('asset');
		$this->applyPartnerFilterForClass('assetParamsOutput');
		$this->applyPartnerFilterForClass('assetParams');
	}
	
	/* (non-PHPdoc)
	 * @see KalturaBaseService::partnerGroup()
	 */
	protected function partnerGroup($peer = null)
	{
		if(
			$this->actionName == 'get' ||
			$this->actionName == 'list'
			)
			return $this->partnerGroup . ',0';
			
		return $this->partnerGroup;
	}
	
	protected function globalPartnerAllowed($actionName)
	{
		if ($actionName === 'get') {
			return true;
		}
		if ($actionName === 'list') {
			return true;
		}
		return parent::globalPartnerAllowed($actionName);
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
		$thumbParamsDb = new thumbParams();
		$thumbParams->toInsertableObject($thumbParamsDb);
		
		$thumbParamsDb->setPartnerId($this->getPartnerId());
		$thumbParamsDb->save();
		
		$thumbParams->fromObject($thumbParamsDb, $this->getResponseProfile());
		return $thumbParams;
	}
	
	/**
	 * Get Thumb Params by ID
	 * 
	 * @action get
	 * @param bigint $id
	 * @return KalturaThumbParams
	 */
	public function getAction($id)
	{
		$thumbParamsDb = assetParamsPeer::retrieveByPK($id);
		
		if (!$thumbParamsDb)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_ID_NOT_FOUND, $id);
			
		$thumbParams = KalturaFlavorParamsFactory::getFlavorParamsInstance($thumbParamsDb->getType());
		$thumbParams->fromObject($thumbParamsDb, $this->getResponseProfile());
		
		return $thumbParams;
	}
	
	/**
	 * Update Thumb Params by ID
	 * 
	 * @action update
	 * @param bigint $id
	 * @param KalturaThumbParams $thumbParams
	 * @return KalturaThumbParams
	 */
	public function updateAction($id, KalturaThumbParams $thumbParams)
	{
		$thumbParamsDb = assetParamsPeer::retrieveByPK($id);
		if (!$thumbParamsDb)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_ID_NOT_FOUND, $id);
			
		$thumbParams->toUpdatableObject($thumbParamsDb);
		$thumbParamsDb->save();
			
		$thumbParams->fromObject($thumbParamsDb, $this->getResponseProfile());
		return $thumbParams;
	}
	
	/**
	 * Delete Thumb Params by ID
	 * 
	 * @action delete
	 * @param bigint $id
	 */
	public function deleteAction($id)
	{
		$thumbParamsDb = assetParamsPeer::retrieveByPK($id);
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
			
		if(!$pager)
		{
			$pager = new KalturaFilterPager();
		}

		$types = KalturaPluginManager::getExtendedTypes(assetParamsPeer::OM_CLASS, assetType::THUMBNAIL);
		return $filter->getTypeListResponse($pager, $this->getResponseProfile(), $types);
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
			
		$thumbParamsConversionProfilesDb = $conversionProfileDb->getflavorParamsConversionProfilesJoinflavorParams();
		$thumbParamsDb = array();
		foreach($thumbParamsConversionProfilesDb as $item)
		{
			/* @var $item flavorParamsConversionProfile */
			$thumbParamsDb[] = $item->getassetParams();
		}
		
		$thumbParams = KalturaThumbParamsArray::fromDbArray($thumbParamsDb, $this->getResponseProfile());
		
		return $thumbParams; 
	}
}
