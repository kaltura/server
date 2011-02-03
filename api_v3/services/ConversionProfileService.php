<?php

/**
 * Add & Manage Conversion Profiles
 *
 * @service conversionProfile
 * @package api
 * @subpackage services
 */
class ConversionProfileService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		parent::applyPartnerFilterForClass(flavorAssetPeer::getInstance());
		parent::applyPartnerFilterForClass(flavorParamsPeer::getInstance());
		parent::applyPartnerFilterForClass(flavorParamsOutputPeer::getInstance());
		parent::applyPartnerFilterForClass(new conversionProfile2Peer());
	}
	
	/**
	 * Add new Conversion Profile
	 * 
	 * @action add
	 * @param KalturaConversionProfile $conversionProfile
	 * @return KalturaConversionProfile
	 */
	public function addAction(KalturaConversionProfile $conversionProfile)
	{
		$conversionProfile->validatePropertyMinLength("name", 1);
		$conversionProfile->validateFlavorParamsIds();
		
		$conversionProfileDb = new conversionProfile2();
		$conversionProfile->toObject($conversionProfileDb);

		$conversionProfileDb->setInputTagsMap(flavorParams::TAG_WEB . ',' . flavorParams::TAG_SLWEB);
		$conversionProfileDb->setPartnerId($this->getPartnerId());
		$conversionProfileDb->save();
		
		$this->addFlavorParamsRelation($conversionProfileDb, $conversionProfile->getFlavorParamsAsArray());
		
		$conversionProfile->fromObject($conversionProfileDb);
		$conversionProfile->loadFlavorParamsIds($conversionProfileDb);
		return $conversionProfile;
	}
	
	/**
	 * Get Conversion Profile by ID
	 * 
	 * @action get
	 * @param int $id
	 * @return KalturaConversionProfile
	 */
	public function getAction($id)
	{
		$conversionProfileDb = conversionProfile2Peer::retrieveByPK($id);
		if (!$conversionProfileDb)
			throw new KalturaAPIException(KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND, $id);
			
		$conversionProfile = new KalturaConversionProfile();
		$conversionProfile->fromObject($conversionProfileDb);
		$conversionProfile->loadFlavorParamsIds($conversionProfileDb);
		
		return $conversionProfile;
	}
	
	/**
	 * Update Conversion Profile by ID
	 * 
	 * @action update
	 * @param int $id
	 * @param KalturaConversionProfile $conversionProfile
	 * @return KalturaConversionProfile
	 */
	public function updateAction($id, KalturaConversionProfile $conversionProfile)
	{
		if ($conversionProfile->name !== null)
			$conversionProfile->validatePropertyMinLength("name", 1);
		
		if ($conversionProfile->flavorParamsIds !== null) 
			$conversionProfile->validateFlavorParamsIds();
		
		$conversionProfileDb = conversionProfile2Peer::retrieveByPK($id);
		if (!$conversionProfileDb)
			throw new KalturaAPIException(KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND, $id);
			
		$conversionProfile->toUpdatableObject($conversionProfileDb);
		$conversionProfileDb->setCreationMode(conversionProfile2::CONVERSION_PROFILE_2_CREATION_MODE_KMC);
		$conversionProfileDb->save();
		
		if ($conversionProfile->flavorParamsIds !== null) 
		{
			$this->deleteFlavorParamsRelation($conversionProfileDb);
			$this->addFlavorParamsRelation($conversionProfileDb, $conversionProfile->getFlavorParamsAsArray());
		}
		
		$conversionProfile->fromObject($conversionProfileDb);
		$conversionProfile->loadFlavorParamsIds($conversionProfileDb);
		
		return $conversionProfile;
	}
	
	/**
	 * Delete Conversion Profile by ID
	 * 
	 * @action delete
	 * @param int $id
	 */
	public function deleteAction($id)
	{
		$conversionProfileDb = conversionProfile2Peer::retrieveByPK($id);
		if (!$conversionProfileDb)
			throw new KalturaAPIException(KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND, $id);
			
		if ($conversionProfileDb->getIsDefault() === true)
			throw new KalturaAPIException(KalturaErrors::CANNOT_DELETE_DEFAULT_CONVERSION_PROFILE);
			
		$this->deleteFlavorParamsRelation($conversionProfileDb);
		
		$conversionProfileDb->setDeletedAt(time());
		$conversionProfileDb->save();
	}
	
	/**
	 * List Conversion Profiles by filter with paging support
	 * 
	 * @action list
	 * @param KalturaConversionProfileFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaConversionProfileListResponse
	 */
	public function listAction(KalturaConversionProfileFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaConversionProfileFilter();

		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$conversionProfile2Filter = new conversionProfile2Filter();
		
		$filter->toObject($conversionProfile2Filter);

		$c = new Criteria();
		$conversionProfile2Filter->attachToCriteria($c);
		
		$totalCount = conversionProfile2Peer::doCount($c);
		
		$pager->attachToCriteria($c);
		$dbList = conversionProfile2Peer::doSelect($c);
		
		$list = KalturaConversionProfileArray::fromDbArray($dbList);
		$list->loadFlavorParamsIds();
		$response = new KalturaConversionProfileListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;    
	}
	
	/**
	 * Adds the relation of flavorParams <> conversionProfile2
	 * 
	 * @param conversionProfile2 $conversionProfileDb
	 * @param $flavorParamsIds
	 */
	protected function addFlavorParamsRelation(conversionProfile2 $conversionProfileDb, $flavorParamsIds)
	{
		foreach($flavorParamsIds as $flavorParamsId)
		{
			$fpc = new flavorParamsConversionProfile();
			$fpc->setConversionProfileId($conversionProfileDb->getId());
			$fpc->setFlavorParamsId($flavorParamsId);
			$fpc->save();
		}
	}
	
	/**
	 * Delete the relation of flavorParams <> conversionProfile2
	 * 
	 * @param conversionProfile2 $conversionProfileDb
	 */
	protected function deleteFlavorParamsRelation(conversionProfile2 $conversionProfileDb)
	{
		$c = new Criteria();
		$c->add(flavorParamsConversionProfilePeer::CONVERSION_PROFILE_ID, $conversionProfileDb->getId());
		flavorParamsConversionProfilePeer::doDelete($c);
	}
}