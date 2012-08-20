<?php

/**
 * Add & Manage Caption Params
 *
 * @service captionParams
 * @package plugins.caption
 * @subpackage api.services
 */
class CaptionParamsService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		parent::applyPartnerFilterForClass(new conversionProfile2Peer());
		parent::applyPartnerFilterForClass(new assetPeer());
		parent::applyPartnerFilterForClass(new assetParamsOutputPeer());
		
		$partnerGroup = null;
		if(
			$actionName == 'get' ||
			$actionName == 'list'
			)
			$partnerGroup = $this->partnerGroup . ',0';
			
		parent::applyPartnerFilterForClass(new assetParamsPeer(), $partnerGroup);
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
	 * Add new Caption Params
	 * 
	 * @action add
	 * @param KalturaCaptionParams $captionParams
	 * @return KalturaCaptionParams
	 */
	public function addAction(KalturaCaptionParams $captionParams)
	{
		$captionParams->validatePropertyMinLength("name", 1);
		
		$captionParamsDb = new CaptionParams();
		$captionParams->toObject($captionParamsDb);
		
		$captionParamsDb->setPartnerId($this->getPartnerId());
		$captionParamsDb->save();
		
		$captionParams->fromObject($captionParamsDb);
		return $captionParams;
	}
	
	/**
	 * Get Caption Params by ID
	 * 
	 * @action get
	 * @param int $id
	 * @return KalturaCaptionParams
	 */
	public function getAction($id)
	{
		$captionParamsDb = assetParamsPeer::retrieveByPK($id);
		
		if (!$captionParamsDb)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_ID_NOT_FOUND, $id);
			
		$captionParams = KalturaFlavorParamsFactory::getFlavorParamsInstance($captionParamsDb->getType());
		$captionParams->fromObject($captionParamsDb);
		
		return $captionParams;
	}
	
	/**
	 * Update Caption Params by ID
	 * 
	 * @action update
	 * @param int $id
	 * @param KalturaCaptionParams $captionParams
	 * @return KalturaCaptionParams
	 */
	public function updateAction($id, KalturaCaptionParams $captionParams)
	{
		if ($captionParams->name !== null)
			$captionParams->validatePropertyMinLength("name", 1);
			
		$captionParamsDb = assetParamsPeer::retrieveByPK($id);
		if (!$captionParamsDb)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_ID_NOT_FOUND, $id);
			
		$captionParams->toUpdatableObject($captionParamsDb);
		$captionParamsDb->save();
			
		$captionParams->fromObject($captionParamsDb);
		return $captionParams;
	}
	
	/**
	 * Delete Caption Params by ID
	 * 
	 * @action delete
	 * @param int $id
	 */
	public function deleteAction($id)
	{
		$captionParamsDb = assetParamsPeer::retrieveByPK($id);
		if (!$captionParamsDb)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_ID_NOT_FOUND, $id);
			
		$captionParamsDb->setDeletedAt(time());
		$captionParamsDb->save();
	}
	
	/**
	 * List Caption Params by filter with paging support (By default - all system default params will be listed too)
	 * 
	 * @action list
	 * @param KalturaCaptionParamsFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaCaptionParamsListResponse
	 */
	public function listAction(KalturaCaptionParamsFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaCaptionParamsFilter();

		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$captionParamsFilter = new assetParamsFilter();
		
		$filter->toObject($captionParamsFilter);
		
		$c = new Criteria();
		$captionParamsFilter->attachToCriteria($c);
		$pager->attachToCriteria($c);
		
		$captionTypes = KalturaPluginManager::getExtendedTypes(assetParamsPeer::OM_CLASS, CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION));
		$c->add(assetParamsPeer::TYPE, $captionTypes, Criteria::IN);
		
		$dbList = assetParamsPeer::doSelect($c);
		
		$c->setLimit(null);
		$totalCount = assetParamsPeer::doCount($c);

		$list = KalturaCaptionParamsArray::fromDbArray($dbList);
		$response = new KalturaCaptionParamsListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;    
	}
}