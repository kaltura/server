<?php

/**
 * Add & Manage Flavor Params
 *
 * @service flavorParams
 * @package api
 * @subpackage services
 */
class FlavorParamsService extends KalturaBaseService
{
	
	const PROPERTY_MIN_LENGTH = 1; 
	
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
		if( $this->actionName == 'get') {
			assetParamsPeer::setIsDefaultInDefaultCriteria(false);
			return $this->partnerGroup . ',0';
		} else if ($this->actionName == 'list') {
			return $this->partnerGroup . ',0';
		}
			
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
	 * Add new Flavor Params
	 * 
	 * @action add
	 * @param KalturaFlavorParams $flavorParams
	 * @return KalturaFlavorParams
	 */
	public function addAction(KalturaFlavorParams $flavorParams)
	{
		$flavorParams->validatePropertyMinLength("name", self::PROPERTY_MIN_LENGTH);
		
		$flavorParamsDb = $flavorParams->toObject();
		
		$flavorParamsDb->setPartnerId($this->getPartnerId());
		$flavorParamsDb->save();
		
		$flavorParams = KalturaFlavorParamsFactory::getFlavorParamsInstance($flavorParamsDb->getType());
		$flavorParams->fromObject($flavorParamsDb);
		return $flavorParams;
	}
	
	/**
	 * Get Flavor Params by ID
	 * 
	 * @action get
	 * @param int $id
	 * @return KalturaFlavorParams
	 */
	public function getAction($id)
	{
		$flavorParamsDb = assetParamsPeer::retrieveByPK($id);
		
		if (!$flavorParamsDb)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_ID_NOT_FOUND, $id);
			
		$flavorParams = KalturaFlavorParamsFactory::getFlavorParamsInstance($flavorParamsDb->getType());
		$flavorParams->fromObject($flavorParamsDb);
		
		return $flavorParams;
	}
	
	/**
	 * Update Flavor Params by ID
	 * 
	 * @action update
	 * @param int $id
	 * @param KalturaFlavorParams $flavorParams
	 * @return KalturaFlavorParams
	 */
	public function updateAction($id, KalturaFlavorParams $flavorParams)
	{
		if ($flavorParams->name !== null)
			$flavorParams->validatePropertyMinLength("name", self::PROPERTY_MIN_LENGTH);
			
		$flavorParamsDb = assetParamsPeer::retrieveByPK($id);
		if (!$flavorParamsDb)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_ID_NOT_FOUND, $id);
			
		$flavorParams->toUpdatableObject($flavorParamsDb);
		$flavorParamsDb->save();
			
		$flavorParams = KalturaFlavorParamsFactory::getFlavorParamsInstance($flavorParamsDb->getType());
		$flavorParams->fromObject($flavorParamsDb);
		return $flavorParams;
	}
	
	/**
	 * Delete Flavor Params by ID
	 * 
	 * @action delete
	 * @param int $id
	 */
	public function deleteAction($id)
	{
		$flavorParamsDb = assetParamsPeer::retrieveByPK($id);
		if (!$flavorParamsDb)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_ID_NOT_FOUND, $id);
			
		$flavorParamsDb->setDeletedAt(time());
		$flavorParamsDb->save();
	}
	
	/**
	 * List Flavor Params by filter with paging support (By default - all system default params will be listed too)
	 * 
	 * @action list
	 * @param KalturaFlavorParamsFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaFlavorParamsListResponse
	 */
	public function listAction(KalturaFlavorParamsFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaFlavorParamsFilter();

		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$flavorParamsFilter = new assetParamsFilter();
		
		$filter->toObject($flavorParamsFilter);
		
		$c = new Criteria();
		$flavorParamsFilter->attachToCriteria($c);
		
		$pager->attachToCriteria($c);
		
		$flavorTypes = assetParamsPeer::retrieveAllFlavorParamsTypes();
		$c->add(assetParamsPeer::TYPE, $flavorTypes, Criteria::IN);
		
		$dbList = assetParamsPeer::doSelect($c);
		
		$c->setLimit(null);
		$totalCount = assetParamsPeer::doCount($c);

		$list = KalturaFlavorParamsArray::fromDbArray($dbList);
		$response = new KalturaFlavorParamsListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;    
	}
	
	/**
	 * Get Flavor Params by Conversion Profile ID
	 * 
	 * @action getByConversionProfileId
	 * @param int $conversionProfileId
	 * @return KalturaFlavorParamsArray
	 */
	public function getByConversionProfileIdAction($conversionProfileId)
	{
		$conversionProfileDb = conversionProfile2Peer::retrieveByPK($conversionProfileId);
		if (!$conversionProfileDb)
			throw new KalturaAPIException(KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND, $conversionProfileId);
			
		$flavorParamsConversionProfilesDb = $conversionProfileDb->getflavorParamsConversionProfilesJoinflavorParams();
		$flavorParamsDb = array();
		foreach($flavorParamsConversionProfilesDb as $item)
		{
			/* @var $item flavorParamsConversionProfile */
			$flavorParamsDb[] = $item->getassetParams();
		}
		
		$flavorParams = KalturaFlavorParamsArray::fromDbArray($flavorParamsDb);
		
		return $flavorParams; 
	}
}