<?php

/**
 * Retrieve information and invoke actions on Flavor Asset
 *
 * @service flavorAsset
 * @package api
 * @subpackage services
 */
class FlavorAssetService extends KalturaBaseService
{
	public function initService($partnerId, $puserId, $ksStr, $serviceName, $action)
	{
		parent::initService($partnerId, $puserId, $ksStr, $serviceName, $action);
		parent::applyPartnerFilterForClass(new flavorParamsPeer());
		parent::applyPartnerFilterForClass(new conversionProfile2Peer());
		parent::applyPartnerFilterForClass(new flavorAssetPeer());
	}
	
	/**
	 * Get Flavor Asset by ID
	 * 
	 * @action get
	 * @param string $id
	 * @return KalturaFlavorAsset
	 */
	public function getAction($id)
	{
		$flavorAssetDb = flavorAssetPeer::retrieveById($id);
		if (!$flavorAssetDb)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $id);
			
		$flavorAsset = new KalturaFlavorAsset();
		$flavorAsset->fromObject($flavorAssetDb);
		return $flavorAsset;
	}
	
	/**
	 * Get Flavor Assets for Entry
	 * 
	 * @action getByEntryId
	 * @param string $entryId
	 * @return KalturaFlavorAssetArray
	 */
	public function getByEntryIdAction($entryId)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		// get the flavor assets for this entry
		$c = new Criteria();
		$c->add(flavorAssetPeer::ENTRY_ID, $entryId);
		$flavorAssetsDb = flavorAssetPeer::doSelect($c);
		$flavorAssets = KalturaFlavorAssetArray::fromDbArray($flavorAssetsDb);
		return $flavorAssets;
	}
	
	/**
	 * Get web playable Flavor Assets for Entry
	 * 
	 * @action getWebPlayableByEntryId
	 * @param string $entryId
	 * @return KalturaFlavorAssetArray
	 */
	public function getWebPlayableByEntryIdAction($entryId)
	{
		// entry could be "display_in_search = 2" - in that case we want to pull it although KN is off in services.ct for this action
		$c = new Criteria();
		$c->addAnd(entryPeer::ID, $entryId);
		$criterionPartnerOrKn = $c->getNewCriterion(entryPeer::PARTNER_ID, $this->getPartnerId());
		$criterionPartnerOrKn->addOr($c->getNewCriterion(entryPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK));
		$c->addAnd($criterionPartnerOrKn);
		// there could only be one entry because the query is by primary key.
		// so using doSelectOne is safe.
		$dbEntry = entryPeer::doSelectOne($c);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		
		// if the entry does not belong to the partner but "display_in_search = 2"
		// we want to turn off the criteria over the flavorAssetPeer
		if($dbEntry->getPartnerId() != $this->getPartnerId() &&
		   $dbEntry->getDisplayInSearch() == mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK)
		{
			flavorAssetPeer::setDefaultCriteriaFilter(null);
		}
		$flavorAssetsDb = flavorAssetPeer::retrieveReadyWebByEntryId($entryId);
		if (count($flavorAssetsDb) == 0)
			throw new KalturaAPIException(KalturaErrors::NO_FLAVORS_FOUND);
		
		// re-set default criteria to avoid fetching "private" flavors in laetr queries.
		// this should be also set in baseService, but we better do it anyway.
		flavorAssetPeer::setDefaultCriteriaFilter();
			
		$flavorAssets = KalturaFlavorAssetArray::fromDbArray($flavorAssetsDb);
		
		return $flavorAssets;
	}
	
	/**
	 * Add and convert new Flavor Asset for Entry with specific Flavor Params
	 * 
	 * @action convert
	 * @param string $entryId
	 * @param int $flavorParamsId
	 */
	public function convertAction($entryId, $flavorParamsId)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		flavorParamsPeer::allowAccessToSystemDefaultParamsAndPartnerX($this->getPartnerId()); // the flavor params can be from partner X too
		$flavorParamsDb = flavorParamsPeer::retrieveByPK($flavorParamsId);
		flavorParamsPeer::setDefaultCriteriaFilter();
		if (!$flavorParamsDb)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_ID_NOT_FOUND, $flavorParamsId);
				
		$validStatuses = array(
			entry::ENTRY_STATUS_ERROR_CONVERTING,
			entry::ENTRY_STATUS_PRECONVERT,
			entry::ENTRY_STATUS_READY,
		);
		
		if (!in_array($dbEntry->getStatus(), $validStatuses))
			throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_STATUS);
			
		$originalFlavorAsset = flavorAssetPeer::retrieveOriginalByEntryId($entryId);
		if (is_null($originalFlavorAsset) || $originalFlavorAsset->getStatus() != flavorAsset::FLAVOR_ASSET_STATUS_READY)
			throw new KalturaAPIException(KalturaErrors::ORIGINAL_FLAVOR_ASSET_IS_MISSING);

		$err = "";
		kBusinessPreConvertDL::decideAddEntryFlavor(null, $dbEntry->getId(), $flavorParamsId, $err);
	}
	
	/**
	 * Reconvert Flavor Asset by ID
	 * 
	 * @action reconvert
	 * @param string $id Flavor Asset ID
	 */
	public function reconvertAction($id)
	{
		$flavorAssetDb = flavorAssetPeer::retrieveById($id);
		if (!$flavorAssetDb)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $id);
			
		if ($flavorAssetDb->getIsOriginal())
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_RECONVERT_ORIGINAL);
			
		$flavorParamsId = $flavorAssetDb->getFlavorParamsId();
		$entryId = $flavorAssetDb->getEntryId();
		
		return $this->convertAction($entryId, $flavorParamsId);
	} 
	
	/**
	 * Delete Flavor Asset by ID
	 * 
	 * @action delete
	 * @param string $id
	 */
	public function deleteAction($id)
	{
		$flavorAssetDb = flavorAssetPeer::retrieveById($id);
		if (!$flavorAssetDb)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $id);
			
		$flavorAssetDb->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_DELETED);
		$flavorAssetDb->setDeletedAt(time());
		$flavorAssetDb->save();
		
		$entry = $flavorAssetDb->getEntry();
		if ($entry)
		{
			$entry->removeFlavorParamsId($flavorAssetDb->getFlavorParamsId());
			$entry->save();
		}
	}
	
	/**
	 * Get download URL for the Flavor Asset
	 * 
	 * @action getDownloadUrl
	 * @param string $id
	 * @return string
	 */
	public function getDownloadUrlAction($id)
	{
		$flavorAssetDb = flavorAssetPeer::retrieveById($id);
		if (!$flavorAssetDb)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $id);

		if ($flavorAssetDb->getStatus() != flavorAsset::FLAVOR_ASSET_STATUS_READY)
			throw new KalturaAPIEXception(KalturaErrors::FLAVOR_ASSET_IS_NOT_READY);

		return $flavorAssetDb->getDownloadUrl();
	}
	
	/**
	 * Get Flavor Asset with the relevant Flavor Params (Flavor Params can exist without Flavor Asset & vice versa)
	 * 
	 * @action getFlavorAssetsWithParams
	 * @param string $entryId
	 * @return KalturaFlavorAssetWithParamsArray
	 */
	public function getFlavorAssetsWithParamsAction($entryId)
	{
		flavorParamsPeer::allowAccessToSystemDefaultParamsAndPartnerX($this->getPartnerId()); // the flavor params can be from partner 0 too
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		
		// get all the flavor params of partner 0 and the current partner (note that partner 0 is defined as partner group in service.ct)
		$flavorParamsDb = flavorParamsPeer::doSelect(new Criteria());
		
		// get the flavor assets for this entry
		$c = new Criteria();
		$c->add(flavorAssetPeer::ENTRY_ID, $entryId);
		$c->add(flavorAssetPeer::STATUS, array(flavorAsset::FLAVOR_ASSET_STATUS_DELETED, flavorAsset::FLAVOR_ASSET_STATUS_TEMP), Criteria::NOT_IN);
		$flavorAssetsDb = flavorAssetPeer::doSelect($c);
		
		// find what flavot params are required
		$requiredFlavorParams = array();
		foreach($flavorAssetsDb as $item)
			$requiredFlavorParams[$item->getFlavorParamsId()] = true;
		
		// now merge the results, first organize the flavor params in an array with the id as the key
		$flavorParamsArray = array();
		foreach($flavorParamsDb as $item)
		{
			$flavorParams = $item->getId();
			$flavorParamsArray[$flavorParams] = $item;
			
			if(isset($requiredFlavorParams[$flavorParams]))
				unset($requiredFlavorParams[$flavorParams]);
		}

		// adding missing required flavors params to the list
		if(count($requiredFlavorParams))
		{
			$flavorParamsDb = flavorParamsPeer::retrieveByPKsNoFilter(array_keys($requiredFlavorParams));
			foreach($flavorParamsDb as $item)
				$flavorParamsArray[$item->getId()] = $item;
		}
		
		$usedFlavorParams = array();
		
		// loop over the flavor assets and add them, if it has flavor params add them too
		$flavorAssetWithParamsArray = new KalturaFlavorAssetWithParamsArray();
		foreach($flavorAssetsDb as $flavorAssetDb)
		{
			$flavorParamsId = $flavorAssetDb->getFlavorParamsId();
			$flavorAssetWithParams = new KalturaFlavorAssetWithParams();
			$flavorAssetWithParams->entryId = $entryId;
			$flavorAsset = new KalturaFlavorAsset();
			$flavorAsset->fromObject($flavorAssetDb);
			$flavorAssetWithParams->flavorAsset = $flavorAsset;
			if (isset($flavorParamsArray[$flavorParamsId]))
			{
				$flavorParamsDb = $flavorParamsArray[$flavorParamsId];
				$flavorParams = KalturaFlavorParamsFactory::getFlavorParamsInstanceByFormat($flavorParamsDb->getFormat());
				$flavorParams->fromObject($flavorParamsDb);
				$flavorAssetWithParams->flavorParams = $flavorParams;

				// we want to log which flavor params are in use, there could be more
				// than one flavor asset using same params
				$usedFlavorParams[$flavorParamsId] = $flavorParamsId;
			}
//			else if ($flavorAssetDb->getIsOriginal())
//			{
//				// create a dummy flavor params
//				$flavorParams = new KalturaFlavorParams();
//				$flavorParams->name = "Original source";
//				$flavorAssetWithParams->flavorParams = $flavorParams;
//			}
			
			$flavorAssetWithParamsArray[] = $flavorAssetWithParams;
		}
		
		// copy the remaining params
		foreach($flavorParamsArray as $flavorParamsId => $flavorParamsDb)
		{
			if(isset($usedFlavorParams[$flavorParamsId]))
			{
				// flavor params already exists for a flavor asset, not need
				// to list it one more time
				continue;
			}
			$flavorParams = KalturaFlavorParamsFactory::getFlavorParamsInstanceByFormat($flavorParamsDb->getFormat());
			$flavorParams->fromObject($flavorParamsDb);
			
			$flavorAssetWithParams = new KalturaFlavorAssetWithParams();
			$flavorAssetWithParams->entryId = $entryId;
			$flavorAssetWithParams->flavorParams = $flavorParams;
			$flavorAssetWithParamsArray[] = $flavorAssetWithParams;
		}
		
		return $flavorAssetWithParamsArray;
	}
}