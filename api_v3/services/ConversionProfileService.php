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
		parent::applyPartnerFilterForClass(flavorParamsOutputPeer::getInstance());
		parent::applyPartnerFilterForClass(new conversionProfile2Peer());
		
		$partnerGroup = null;
		if(
			$actionName == 'add' ||
			$actionName == 'update'
			)
			$partnerGroup = $this->partnerGroup . ',0';
			
		parent::applyPartnerFilterForClass(flavorParamsPeer::getInstance(), $partnerGroup);
	}
	
	/**
	 * Set Conversion Profile to be the partner default
	 * 
	 * @action setAsDefault
	 * @param int $id
	 * @return KalturaConversionProfile
	 */
	public function setAsDefaultAction($id)
	{
		$conversionProfileDb = conversionProfile2Peer::retrieveByPK($id);
		if (!$conversionProfileDb || $conversionProfileDb->getPartnerId() != $this->getPartnerId())
			throw new KalturaAPIException(KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND, $id);
			
		$partner = $this->getPartner();
		$partner->setDefaultConversionProfileId($id);
		$partner->save();
		PartnerPeer::removePartnerFromCache($partner->getId());
		
		$conversionProfile = new KalturaConversionProfile();
		$conversionProfile->fromObject($conversionProfileDb);
		$conversionProfile->loadFlavorParamsIds($conversionProfileDb);
		
		return $conversionProfile;
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
		
		if($conversionProfile->xslTransformation)
			$conversionProfileDb->incrementXslVersion();
			
		$conversionProfileDb->save();
		
		$this->addFlavorParamsRelation($conversionProfileDb, $conversionProfile->getFlavorParamsAsArray());
		
		if($conversionProfile->xslTransformation)
		{
			$xsl = html_entity_decode($conversionProfile->xslTransformation);
			$key = $conversionProfileDb->getSyncKey(conversionProfile2::FILE_SYNC_MRSS_XSL);
			kFileSyncUtils::file_put_contents($key, $xsl);
		}
		
		$conversionProfile->fromObject($conversionProfileDb);
		
		// load flavor params id with the same connection (master connection) that was used for insert
		$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_MASTER);
		$conversionProfile->loadFlavorParamsIds($conversionProfileDb, $con);
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
	 * Lists asset parmas of conversion profile by ID
	 * 
	 * @action listAssetParams
	 * @param int $conversionProfileId
	 * @return KalturaConversionProfileAssetParamsListResponse
	 */
	public function listAssetParamsAction($conversionProfileId)
	{
		$dbList = flavorParamsConversionProfilePeer::retrieveByConversionProfile($conversionProfileId);
		
		$list = KalturaConversionProfileAssetParamsArray::fromDbArray($dbList);
		$response = new KalturaConversionProfileAssetParamsListResponse();
		$response->objects = $list;
		$response->totalCount = count($list);
		return $response; 
	}
	
	/**
	 * Update asset parmas of conversion profile by ID
	 * 
	 * @action updateAssetParams
	 * @param int $conversionProfileId
	 * @param int $assetParamsId
	 * @param KalturaConversionProfileAssetParams $conversionProfileAssetParams
	 * @return KalturaConversionProfileAssetParams
	 */
	public function updateAssetParamsAction($conversionProfileId, $assetParamsId, KalturaConversionProfileAssetParams $conversionProfileAssetParams)
	{
		$flavorParamsConversionProfile = flavorParamsConversionProfilePeer::retrieveByFlavorParamsAndConversionProfile($assetParamsId, $conversionProfileId);
		if(!$flavorParamsConversionProfile)
			throw new KalturaAPIException(KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND, $conversionProfileId, $assetParamsId);
			
		$conversionProfileAssetParams->toUpdatableObject($flavorParamsConversionProfile);
		$flavorParamsConversionProfile->save();
			
		$conversionProfileAssetParams->fromObject($flavorParamsConversionProfile);
		return $conversionProfileAssetParams;
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
		
		if($conversionProfile->xslTransformation)
			$conversionProfileDb->incrementXslVersion();
			
		$conversionProfileDb->save();
		
		if ($conversionProfile->flavorParamsIds !== null) 
		{
			$this->deleteFlavorParamsRelation($conversionProfileDb, $conversionProfile->flavorParamsIds);
			$this->addFlavorParamsRelation($conversionProfileDb, $conversionProfile->getFlavorParamsAsArray());
		}
		
		if($conversionProfile->xslTransformation)
		{
			$xsl = html_entity_decode($conversionProfile->xslTransformation);
			$key = $conversionProfileDb->getSyncKey(conversionProfile2::FILE_SYNC_MRSS_XSL);
			kFileSyncUtils::file_put_contents($key, $xsl);
		}
		
		$conversionProfile->fromObject($conversionProfileDb);
		// load flavor params id with the same connection (master connection) that was used for insert
		$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_MASTER);
		$conversionProfile->loadFlavorParamsIds($conversionProfileDb, $con);
		
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
		$existingIds = flavorParamsConversionProfilePeer::getFlavorIdsByProfileId($conversionProfileDb->getId());
		
		assetParamsPeer::resetInstanceCriteriaFilter();
		$assetParamsObjects = assetParamsPeer::retrieveByPKs($flavorParamsIds);
		foreach($assetParamsObjects as $assetParams)
		{
			if(in_array($assetParams->getId(), $existingIds))
				continue;
				
			$fpc = new flavorParamsConversionProfile();
			$fpc->setConversionProfileId($conversionProfileDb->getId());
			$fpc->setFlavorParamsId($assetParams->getId());
			$fpc->setReadyBehavior($assetParams->getReadyBehavior());
			$fpc->setSystemName($assetParams->getSystemName());
			$fpc->save();
		}
	}
	
	/**
	 * Delete the relation of flavorParams <> conversionProfile2
	 * 
	 * @param conversionProfile2 $conversionProfileDb
	 * @param string|array $notInFlavorIds comma sepeartaed id that should not be deleted
	 */
	protected function deleteFlavorParamsRelation(conversionProfile2 $conversionProfileDb, $notInFlavorIds = null)
	{
		$c = new Criteria();
		$c->add(flavorParamsConversionProfilePeer::CONVERSION_PROFILE_ID, $conversionProfileDb->getId());
		if($notInFlavorIds)
		{
			if(!is_array($notInFlavorIds))
				$notInFlavorIds = explode(',', $notInFlavorIds);
				
			$c->add(flavorParamsConversionProfilePeer::FLAVOR_PARAMS_ID, $notInFlavorIds, Criteria::NOT_IN);
		}
			
		flavorParamsConversionProfilePeer::doDelete($c);
	}
}