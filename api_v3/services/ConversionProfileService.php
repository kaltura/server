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
		$this->applyPartnerFilterForClass('asset');
		$this->applyPartnerFilterForClass('assetParamsOutput');
		$this->applyPartnerFilterForClass('conversionProfile2');
		$this->applyPartnerFilterForClass('assetParams');
	}
	
	/* (non-PHPdoc)
	 * @see KalturaBaseService::partnerGroup()
	 */
	protected function partnerGroup($peer = null)
	{
		if($this->actionName == 'add' || $this->actionName == 'update')
		{
			assetParamsPeer::setIsDefaultInDefaultCriteria(false);
			return $this->partnerGroup . ',0';
		}
		
		if(kCurrentContext::$ks_partner_id > PartnerPeer::GLOBAL_PARTNER && in_array($this->actionName, array('list', 'get')))
		{
			return $this->partnerGroup . ',0';
		}
		
		return parent::partnerGroup();
	}
	
	/**
	 * Set Conversion Profile to be the partner default
	 * 
	 * @action setAsDefault
	 * @param bigint $id
	 * @return KalturaConversionProfile
	 * 
	 * @throws KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND
	 */
	public function setAsDefaultAction($id)
	{
		$conversionProfileDb = conversionProfile2Peer::retrieveByPK($id);
		if (!$conversionProfileDb || $conversionProfileDb->getPartnerId() != $this->getPartnerId())
			throw new KalturaAPIException(KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND, $id);
			
		$partner = $this->getPartner();
		
		if($conversionProfileDb->getType() == ConversionProfileType::MEDIA)
			$partner->setDefaultConversionProfileId($id);
		
		if($conversionProfileDb->getType() == ConversionProfileType::LIVE_STREAM)
			$partner->setDefaultLiveConversionProfileId($id);
			
		$partner->save();
		PartnerPeer::removePartnerFromCache($partner->getId());
		
		$conversionProfile = new KalturaConversionProfile();
		$conversionProfile->fromObject($conversionProfileDb, $this->getResponseProfile());
		$conversionProfile->loadFlavorParamsIds($conversionProfileDb);
		
		return $conversionProfile;
	}
	
	/**
	 * Get the partner's default conversion profile
	 * 
	 * @param KalturaConversionProfileType $type
	 * @action getDefault
	 * @return KalturaConversionProfile
	 */
	public function getDefaultAction($type = null)
	{
		if(is_null($type) || $type == KalturaConversionProfileType::MEDIA)
			$defaultProfileId = $this->getPartner()->getDefaultConversionProfileId();
		elseif($type == KalturaConversionProfileType::LIVE_STREAM)
			$defaultProfileId = $this->getPartner()->getDefaultLiveConversionProfileId();
			
		return $this->getAction($defaultProfileId);
	}
	
	/**
	 * Add new Conversion Profile
	 * 
	 * @action add
	 * @param KalturaConversionProfile $conversionProfile
	 * @return KalturaConversionProfile
	 * 
	 * @throws KalturaErrors::ASSET_PARAMS_INVALID_TYPE
	 */
	public function addAction(KalturaConversionProfile $conversionProfile)
	{
		$conversionProfileDb = $conversionProfile->toInsertableObject(new conversionProfile2());

		$conversionProfileDb->setInputTagsMap(flavorParams::TAG_WEB . ',' . flavorParams::TAG_SLWEB);
		$conversionProfileDb->setPartnerId($this->getPartnerId());
		
		if($conversionProfile->xslTransformation)
			$conversionProfileDb->incrementXslVersion();
		
		if($conversionProfile->mediaInfoXslTransformation)
			$conversionProfileDb->incrementMediaInfoXslVersion();
			
		$conversionProfileDb->save();
		
		$flavorParamsArray = $conversionProfile->getFlavorParamsAsArray();
		if ( ! empty( $flavorParamsArray ) )
		{
			$this->addFlavorParamsRelation($conversionProfileDb, $flavorParamsArray);
		}
		
		if($conversionProfile->xslTransformation)
		{
			$xsl = html_entity_decode($conversionProfile->xslTransformation);
			$key = $conversionProfileDb->getSyncKey(conversionProfile2::FILE_SYNC_MRSS_XSL);
			kFileSyncUtils::file_put_contents($key, $xsl);
		}
		
		if($conversionProfile->mediaInfoXslTransformation)
		{
			$xsl = html_entity_decode($conversionProfile->mediaInfoXslTransformation);
			$key = $conversionProfileDb->getSyncKey(conversionProfile2::FILE_SYNC_MEDIAINFO_XSL);
			kFileSyncUtils::file_put_contents($key, $xsl);
		}
		
		$conversionProfile->fromObject($conversionProfileDb, $this->getResponseProfile());
		
		// load flavor params id with the same connection (master connection) that was used for insert
		$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_MASTER);
		$conversionProfile->loadFlavorParamsIds($conversionProfileDb, $con);
		return $conversionProfile;
	}
	
	/**
	 * Get Conversion Profile by ID
	 * 
	 * @action get
	 * @param bigint $id
	 * @return KalturaConversionProfile
	 * 
	 * @throws KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND
	 */
	public function getAction($id)
	{
		$conversionProfileDb = conversionProfile2Peer::retrieveByPK($id);
		if (!$conversionProfileDb)
			throw new KalturaAPIException(KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND, $id);
			
		$conversionProfile = new KalturaConversionProfile();
		$conversionProfile->fromObject($conversionProfileDb, $this->getResponseProfile());
		$conversionProfile->loadFlavorParamsIds($conversionProfileDb);
		
		return $conversionProfile;
	}
	
	/**
	 * Update Conversion Profile by ID
	 * 
	 * @action update
	 * @param bigint $id
	 * @param KalturaConversionProfile $conversionProfile
	 * @return KalturaConversionProfile
	 * 
	 * @throws KalturaErrors::ASSET_PARAMS_INVALID_TYPE
	 * @throws KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND
	 */
	public function updateAction($id, KalturaConversionProfile $conversionProfile)
	{
		$conversionProfileDb = conversionProfile2Peer::retrieveByPK($id);
		if (!$conversionProfileDb || $conversionProfileDb->getPartnerId() == PartnerPeer::GLOBAL_PARTNER)
			throw new KalturaAPIException(KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND, $id);
			
		$conversionProfile->toUpdatableObject($conversionProfileDb);
		$conversionProfileDb->setCreationMode(conversionProfile2::CONVERSION_PROFILE_2_CREATION_MODE_KMC);
		
		if($conversionProfile->xslTransformation)
			$conversionProfileDb->incrementXslVersion();
		
		if($conversionProfile->mediaInfoXslTransformation)
			$conversionProfileDb->incrementMediaInfoXslVersion();
			
		$conversionProfileDb->save();
		
		if ($conversionProfile->flavorParamsIds !== null) 
		{
			$this->deleteFlavorParamsRelation($conversionProfileDb, $conversionProfile->flavorParamsIds);
			$flavorParamsArray = $conversionProfile->getFlavorParamsAsArray();
			if ( ! empty( $flavorParamsArray ) )
			{
				$this->addFlavorParamsRelation($conversionProfileDb, $flavorParamsArray);
			}
		}
		
		if($conversionProfile->xslTransformation)
		{
			$xsl = html_entity_decode($conversionProfile->xslTransformation);
			$key = $conversionProfileDb->getSyncKey(conversionProfile2::FILE_SYNC_MRSS_XSL);
			kFileSyncUtils::file_put_contents($key, $xsl);
		}
		
		if($conversionProfile->mediaInfoXslTransformation)
		{
			$xsl = html_entity_decode($conversionProfile->mediaInfoXslTransformation);
			$key = $conversionProfileDb->getSyncKey(conversionProfile2::FILE_SYNC_MEDIAINFO_XSL);
			kFileSyncUtils::file_put_contents($key, $xsl);
		}
		
		$conversionProfile->fromObject($conversionProfileDb, $this->getResponseProfile());
		// load flavor params id with the same connection (master connection) that was used for insert
		$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_MASTER);
		$conversionProfile->loadFlavorParamsIds($conversionProfileDb, $con);
		
		return $conversionProfile;
	}
	
	/**
	 * Delete Conversion Profile by ID
	 * 
	 * @action delete
	 * @param bigint $id
	 * 
	 * @throws KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND
	 * @throws KalturaErrors::CANNOT_DELETE_DEFAULT_CONVERSION_PROFILE
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
			
		if(!$pager)
			$pager = new KalturaFilterPager();
			
		return $filter->getListResponse($pager, $this->getResponseProfile());  
	}
	
	/**
	 * Adds the relation of flavorParams <> conversionProfile2
	 * 
	 * @param conversionProfile2 $conversionProfileDb
	 * @param $flavorParamsIds
	 * 
	 * @throws KalturaErrors::ASSET_PARAMS_INVALID_TYPE
	 */
	protected function addFlavorParamsRelation(conversionProfile2 $conversionProfileDb, $flavorParamsIds)
	{
		$existingIds = flavorParamsConversionProfilePeer::getFlavorIdsByProfileId($conversionProfileDb->getId());
		
		$assetParamsObjects = assetParamsPeer::retrieveByPKs($flavorParamsIds);
		foreach($assetParamsObjects as $assetParams)
		{
			/* @var $assetParams assetParams */
			if(in_array($assetParams->getId(), $existingIds))
				continue;
				
			$fpc = new flavorParamsConversionProfile();
			$fpc->setConversionProfileId($conversionProfileDb->getId());
			$fpc->setFlavorParamsId($assetParams->getId());
			$fpc->setReadyBehavior($assetParams->getReadyBehavior());
			$fpc->setSystemName($assetParams->getSystemName());
			$fpc->setForceNoneComplied(false);
			
			if($assetParams->hasTag(assetParams::TAG_SOURCE) || $assetParams->hasTag(assetParams::TAG_INGEST))
				$fpc->setOrigin(assetParamsOrigin::INGEST);
			else
				$fpc->setOrigin(assetParamsOrigin::CONVERT);
			
			$fpc->save();
		}
	}
	
	/**
	 * Delete the relation of flavorParams <> conversionProfile2
	 * 
	 * @param conversionProfile2 $conversionProfileDb
	 * @param string|array $notInFlavorIds comma separated ID[s] that should not be deleted
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
			
		$flavorParamsConversionProfiles = flavorParamsConversionProfilePeer::doSelect($c);
		
		foreach($flavorParamsConversionProfiles as $flavorParamsConversionProfile)
		{
			/* @var $flavorParamsConversionProfile flavorParamsConversionProfile */ 
			$flavorParamsConversionProfile->delete();
		}
	}
}
