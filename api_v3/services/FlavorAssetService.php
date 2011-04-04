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
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		parent::applyPartnerFilterForClass(new conversionProfile2Peer());
		parent::applyPartnerFilterForClass(flavorParamsOutputPeer::getInstance());
		parent::applyPartnerFilterForClass(flavorAssetPeer::getInstance());
		
		$partnerGroup = null;
		if(
			$actionName == 'get' ||
			$actionName == 'list' ||
			$actionName == 'getByEntryId' ||
			$actionName == 'getDownloadUrl' ||
			$actionName == 'getWebPlayableByEntryId' ||
			$actionName == 'getFlavorAssetsWithParams' ||
			$actionName == 'convert' ||
			$actionName == 'reconvert'
			)
			$partnerGroup = $this->partnerGroup . ',0';
			
		parent::applyPartnerFilterForClass(flavorParamsPeer::getInstance(), $partnerGroup);
	}

	// maybe a solution to bug #9798
//	/* (non-PHPdoc)
//	 * @see KalturaBaseService::kalturaNetworkAllowed()
//	 */
//	protected function kalturaNetworkAllowed($actionName)
//	{
//		if( $actionName == 'getWebPlayableByEntryId')
//			return true;
//		else
//			return false;
//	}
	

    /**
     * Add flavor asset
     *
     * @action add
     * @param string $entryId
     * @param KalturaFlavorAsset $flavorAsset
     * @param KalturaContentResource $contentResource
     * @return KalturaFlavorAsset
     * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
     * @throws KalturaErrors::FLAVOR_ASSET_ALREADY_EXISTS
	 * @throws KalturaErrors::UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY
	 * @throws KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN
	 * @throws KalturaErrors::RECORDED_WEBCAM_FILE_NOT_FOUND
	 * @throws KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND
	 * @throws KalturaErrors::STORAGE_PROFILE_ID_NOT_FOUND
     */
    function addAction($entryId, KalturaFlavorAsset $flavorAsset, KalturaContentResource $contentResource)
    {
    	$dbEntry = entryPeer::retrieveByPK($entryId);
    	if(!$dbEntry || $dbEntry->getType() != KalturaEntryType::MEDIA_CLIP || !in_array($dbEntry->getMediaType(), array(KalturaMediaType::VIDEO, KalturaMediaType::AUDIO)))
    		throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
    	
    	if($flavorAsset->flavorParamsId)
    	{
    		$dbFlavorAsset = flavorAssetPeer::retrieveByEntryIdAndFlavorParams($entryId, $flavorAsset->flavorParamsId);
    		if($dbFlavorAsset)
    			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ALREADY_EXISTS, $dbFlavorAsset->getId(), $flavorAsset->flavorParamsId);
    	}
    	
    	$dbFlavorAsset = new flavorAsset();
    	$dbFlavorAsset = $flavorAsset->toUpdatableObject($dbFlavorAsset);
    	
		$dbFlavorAsset->setEntryId($entryId);
		$dbFlavorAsset->setPartnerId($dbEntry->getPartnerId());
		$dbFlavorAsset->incrementVersion();
		$dbFlavorAsset->save();
    	
    	$this->attachContentResource($dbFlavorAsset, $contentResource);
				
    	kEventsManager::raiseEvent(new kObjectAddedEvent($dbFlavorAsset));
    	
		$dbFlavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_VALIDATING);
		$dbFlavorAsset->save();
		
		$flavorAsset = new KalturaFlavorAsset();
		$flavorAsset->fromObject($dbFlavorAsset);
		return $flavorAsset;
    }
    
	/**
	 * @param flavorAsset $flavorAsset
	 * @param string $fullPath
	 * @param bool $copyOnly
	 */
	protected function attachFile(flavorAsset $flavorAsset, $fullPath, $copyOnly = false)
	{
		$ext = pathinfo($fullPath, PATHINFO_EXTENSION);
		$flavorAsset->setFileExt($ext);
		$flavorAsset->save();
		
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		
		try {
			kFileSyncUtils::moveFromFile($fullPath, $syncKey, true, $copyOnly);
		}
		catch (Exception $e) {
			$flavorAsset->setDescription($e->getMessage());
			$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
			$flavorAsset->save();												
			throw $e;
		}
    }
    
	/**
	 * @param flavorAsset $flavorAsset
	 * @param KalturaUploadedFileTokenResource $contentResource
	 * @throws KalturaErrors::UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY
	 * @throws KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN
	 */
	protected function attachUploadedFileTokenResource(flavorAsset $flavorAsset, KalturaUploadedFileTokenResource $contentResource)
	{
    	$contentResource->validatePropertyNotNull('token');
    	
		try
		{
		    $fullPath = kUploadTokenMgr::getFullPathByUploadTokenId($contentResource->token);
		}
		catch(kCoreException $ex)
		{
			$flavorAsset->setDescription($ex->getMessage());
			$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
			$flavorAsset->save();
			
		    if ($ex->getCode() == kUploadTokenException::UPLOAD_TOKEN_INVALID_STATUS);
			    throw new KalturaAPIException(KalturaErrors::UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY);
			    
		    throw $ex;
		}
				
		if(!file_exists($fullPath))
		{
			$remoteDCHost = kUploadTokenMgr::getRemoteHostForUploadToken($contentResource->token, kDataCenterMgr::getCurrentDcId());
			if($remoteDCHost)
			{
				kFile::dumpApiRequest($remoteDCHost);
			}
			else
			{
				$flavorAsset->setDescription("Uploaded file token [$contentResource->token] dc not found");
				$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
				$flavorAsset->save();
				
				throw new KalturaAPIException(KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN);
			}
		}
		
		$this->attachFile($flavorAsset, $fullPath);
		kUploadTokenMgr::closeUploadTokenById($contentResource->token);
    }
    
	/**
	 * @param flavorAsset $flavorAsset
	 * @param KalturaWebcamTokenResource $contentResource
	 * @throws KalturaErrors::RECORDED_WEBCAM_FILE_NOT_FOUND
	 */
	protected function attachWebcamTokenResource(flavorAsset $flavorAsset, KalturaWebcamTokenResource $contentResource)
	{
    	$contentResource->validatePropertyNotNull('token');
    	
	    $content = myContentStorage::getFSContentRootPath();
	    $fullPath = "{$content}/content/webcam/{$contentResource->token}.flv";
	    
		if(!file_exists($fullPath))
		{
			$flavorAsset->setDescription("Webcam file token [$contentResource->token] original file [$fullPath] not found");
			$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
			$flavorAsset->save();
			
			throw new KalturaAPIException(KalturaErrors::RECORDED_WEBCAM_FILE_NOT_FOUND);
		}
					
		$fixedFullPath = $fullPath . '.fixed.flv';
 		KalturaLog::debug("Fix webcam full path from [$fullPath] to [$fixedFullPath]");
		myFlvStaticHandler::fixRed5WebcamFlv($fullPath, $fixedFullPath);
				
		$newFullPath = $fullPath . '.clipped.flv';
 		KalturaLog::debug("Clip webcam full path from [$fixedFullPath] to [$newFullPath]");
		myFlvStaticHandler::clipToNewFile($fixedFullPath, $newFullPath, 0, 0);
		$fullPath = $newFullPath ;
			
		if(!file_exists($fullPath))
		{
			$flavorAsset->setDescription("Webcam file token [$contentResource->token] fixed file [$fullPath] not found");
			$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
			$flavorAsset->save();
			
			throw new KalturaAPIException(KalturaErrors::RECORDED_WEBCAM_FILE_NOT_FOUND);
		}
		
		$this->attachFile($flavorAsset, $fullPath);
    }
    
	/**
	 * @param flavorAsset $flavorAsset
	 * @param flavorAsset $srcFlavorAsset
	 */
	protected function attachAsset(flavorAsset $flavorAsset, flavorAsset $srcFlavorAsset)
	{
		$sourceEntryId = $srcFlavorAsset->getEntryId();
		
        $srcSyncKey = $srcFlavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
      	
        $this->attachFileSync($flavorAsset, $srcSyncKey);
    }
    
	/**
	 * @param flavorAsset $flavorAsset
	 * @param KalturaAssetResource $contentResource
	 * @throws KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND
	 */
	protected function attachAssetResource(flavorAsset $flavorAsset, KalturaAssetResource $contentResource)
	{
    	$contentResource->validatePropertyNotNull('assetId');
    	
		$srcFlavorAsset = flavorAssetPeer::retrieveById($contentResource->assetId);
		if (!$srcFlavorAsset)
		{
			$flavorAsset->setDescription("Source asset [$contentResource->assetId] not found");
			$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
			$flavorAsset->save();
			
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $contentResource->assetId);
		}
		
		$this->attachAsset($flavorAsset, $srcFlavorAsset);
    }
    
	/**
	 * @param flavorAsset $flavorAsset
	 * @param KalturaEntryResource $contentResource
	 * @throws KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND
	 */
	protected function attachEntryResource(flavorAsset $flavorAsset, KalturaEntryResource $contentResource)
	{
    	$contentResource->validatePropertyNotNull('entryId');
    
    	$srcEntry = entryPeer::retrieveByPK($contentResource->entryId);
    	if(!$srcEntry || $srcEntry->getType() != KalturaEntryType::MEDIA_CLIP || !in_array($srcEntry->getMediaType(), array(KalturaMediaType::VIDEO, KalturaMediaType::AUDIO)))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $contentResource->entryId);
    	
    	$srcFlavorAsset = null;
    	assetPeer::resetInstanceCriteriaFilter();
    	if(is_null($contentResource->flavorParamsId))
			$srcFlavorAsset = assetPeer::retrieveOriginalByEntryId($contentResource->entryId);
		else
			$srcFlavorAsset = assetPeer::retrieveByEntryIdAndParams($contentResource->entryId, $contentResource->flavorParamsId);

		if (!$srcFlavorAsset)
		{
			$flavorAsset->setDescription("Source asset for entry [$contentResource->entryId] not found");
			$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
			$flavorAsset->save();
			
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $contentResource->assetId);
		}
		
		$this->attachAsset($flavorAsset, $srcFlavorAsset);
    }
    
	/**
	 * @param flavorAsset $flavorAsset
	 * @param string $url
	 */
	protected function attachUrl(flavorAsset $flavorAsset, $url)
	{
		kJobsManager::addImportJob(null, $flavorAsset->getEntryId(), $this->getPartnerId(), $url, $flavorAsset);
    }
    
	/**
	 * @param flavorAsset $flavorAsset
	 * @param KalturaUrlResource $contentResource
	 */
	protected function attachUrlResource(flavorAsset $flavorAsset, KalturaUrlResource $contentResource)
	{
    	$contentResource->validatePropertyNotNull('url');
    	$this->attachUrl($flavorAsset, $contentResource->url);
    }
    
	/**
	 * @param flavorAsset $flavorAsset
	 * @param KalturaSearchResultsResource $contentResource
	 */
	protected function KalturaSearchResultsResource(flavorAsset $flavorAsset, KalturaSearchResultsResource $contentResource)
	{
    	$contentResource->validatePropertyNotNull('result');
     	$contentResource->result->validatePropertyNotNull("searchSource");
     	
		if ($contentResource->result->searchSource == entry::ENTRY_MEDIA_SOURCE_KALTURA ||
			$contentResource->result->searchSource == entry::ENTRY_MEDIA_SOURCE_KALTURA_PARTNER ||
			$contentResource->result->searchSource == entry::ENTRY_MEDIA_SOURCE_KALTURA_PARTNER_KSHOW ||
			$contentResource->result->searchSource == entry::ENTRY_MEDIA_SOURCE_KALTURA_KSHOW ||
			$contentResource->result->searchSource == entry::ENTRY_MEDIA_SOURCE_KALTURA_USER_CLIPS)
		{
			$srcFlavorAsset = assetPeer::retrieveOriginalByEntryId($contentResource->result->id); 
			$this->attachAsset($flavorAsset, $srcFlavorAsset);
		}
		else
		{
			$this->attachUrl($flavorAsset, $contentResource->result->url);
		}
    }
    
	/**
	 * @param flavorAsset $flavorAsset
	 * @param KalturaLocalFileResource $contentResource
	 */
	protected function attachLocalFileResource(flavorAsset $flavorAsset, KalturaLocalFileResource $contentResource)
	{
    	$contentResource->validatePropertyNotNull('localFilePath');
		$this->attachFile($flavorAsset, $contentResource->localFilePath);
    }
    
	/**
	 * @param flavorAsset $flavorAsset
	 * @param KalturaUploadedFileResource $contentResource
	 */
	protected function attachUploadedFileResource(flavorAsset $flavorAsset, KalturaUploadedFileResource $contentResource)
	{
    	$contentResource->validatePropertyNotNull('fileData');
		$ext = pathinfo($contentResource->fileData['name'], PATHINFO_EXTENSION);
		
		$uploadPath = $contentResource->fileData['tmp_name'];
		$tempPath = myContentStorage::getFSUploadsPath() . '/' . uniqid(time()) . '.' . $ext;
		$moved = kFile::moveFile($uploadPath, $tempPath, true);
		if(!$moved)
		{
			$flavorAsset->setDescription("Could not move file from [$uploadPath] to [$tempPath]");
			$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
			$flavorAsset->save();
			
			throw new KalturaAPIException(KalturaErrors::UPLOAD_ERROR);
		}
			 
		return $this->attachFile($flavorAsset, $tempPath);
    }
    
	/**
	 * @param flavorAsset $flavorAsset
	 * @param FileSyncKey $srcSyncKey
	 */
	protected function attachFileSync(flavorAsset $flavorAsset, FileSyncKey $srcSyncKey)
	{
        $newSyncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
        kFileSyncUtils::createSyncFileLinkForKey($newSyncKey, $srcSyncKey, false);
    }
    
	/**
	 * @param flavorAsset $flavorAsset
	 * @param KalturaFileSyncResource $contentResource
	 */
	protected function attachFileSyncResource(flavorAsset $flavorAsset, KalturaFileSyncResource $contentResource)
	{
    	$contentResource->validatePropertyNotNull('fileSyncObjectType');
    	$contentResource->validatePropertyNotNull('objectSubType');
    	$contentResource->validatePropertyNotNull('objectId');
    	
    	$syncable = kFileSyncObjectManager::retrieveObject($contentResource->fileSyncObjectType, $contentResource->objectId);
    	$srcSyncKey = $syncable->getSyncKey($contentResource->objectSubType, $contentResource->version);
    	
        return $this->attachFileSync($flavorAsset, $srcSyncKey);
    }
    
	/**
	 * @param flavorAsset $flavorAsset
	 * @param KalturaRemoteStorageResource $contentResource
	 * @throws KalturaErrors::STORAGE_PROFILE_ID_NOT_FOUND
	 */
	protected function attachRemoteStorageResource(flavorAsset $flavorAsset, KalturaRemoteStorageResource $contentResource)
	{
    	$contentResource->validatePropertyNotNull('url');
    	$contentResource->validatePropertyNotNull('storageProfileId');
    
        $storageProfile = StorageProfilePeer::retrieveByPK($contentResource->storageProfileId);
        if(!$storageProfile)
        {
			$flavorAsset->setDescription("Could not move file from [$uploadPath] to [$tempPath]");
			$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
			$flavorAsset->save();
			
        	throw new KalturaAPIException(KalturaErrors::STORAGE_PROFILE_ID_NOT_FOUND, $contentResource->storageProfileId);
        }
        	
        $syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$fileSync = kFileSyncUtils::createReadyExternalSyncFileForKey($syncKey, $contentResource->url, $storageProfile);
    }
    
	/**
	 * @param flavorAsset $flavorAsset
	 * @param KalturaContentResource $contentResource
	 * @throws KalturaErrors::UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY
	 * @throws KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN
	 * @throws KalturaErrors::RECORDED_WEBCAM_FILE_NOT_FOUND
	 * @throws KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND
	 * @throws KalturaErrors::STORAGE_PROFILE_ID_NOT_FOUND
	 */
	protected function attachContentResource(flavorAsset $flavorAsset, KalturaContentResource $contentResource)
	{
    	switch(get_class($contentResource))
    	{
			case 'KalturaUploadedFileTokenResource':
				return $this->attachUploadedFileTokenResource($flavorAsset, $contentResource);
				
			case 'KalturaWebcamTokenResource':
				return $this->attachWebcamTokenResource($flavorAsset, $contentResource);
				
			case 'KalturaAssetResource':
				return $this->attachAssetResource($flavorAsset, $contentResource);
				
			case 'KalturaEntryResource':
				return $this->attachEntryResource($flavorAsset, $contentResource);
				
			case 'KalturaUrlResource':
				return $this->attachUrlResource($flavorAsset, $contentResource);
				
			case 'KalturaSearchResultsResource':
				return $this->attachSearchResultsResource($flavorAsset, $contentResource);
				
			case 'KalturaLocalFileResource':
				return $this->attachLocalFileResource($flavorAsset, $contentResource);
				
			case 'KalturaUploadedFileResource':
				return $this->attachUploadedFileResource($flavorAsset, $contentResource);
				
			case 'KalturaFileSyncResource':
				return $this->attachFileSyncResource($flavorAsset, $contentResource);
				
			case 'KalturaRemoteStorageResource':
				return $this->attachRemoteStorageResource($flavorAsset, $contentResource);
				
			case 'KalturaDropFolderFileResource':
				// TODO after DropFolderFile object creation
			default:
				$msg = "Resource of type [" . get_class($contentResource) . "] is not supported";
				KalturaLog::err($msg);
				
				$flavorAsset->setDescription($msg);
				$flavorAsset->setStatus(asset::FLAVOR_ASSET_STATUS_ERROR);
				$flavorAsset->save();
				return null;
    	}
    }
    
	protected function globalPartnerAllowed($actionName)
	{
		if ($actionName === 'getFlavorAssetsWithParams') {
			return true;
		}
		return parent::globalPartnerAllowed($actionName);
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
	 * List Flavor Assets by filter and pager
	 * 
	 * @action list
	 * @param KalturaAssetFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaFlavorAssetListResponse
	 */
	function listAction(KalturaAssetFilter $filter = null, KalturaFilterPager $pager = null)
	{
	    myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL2;
	    
		if (!$filter)
			$filter = new KalturaAssetFilter();

		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$flavorAssetFilter = new AssetFilter();
		
		$filter->toObject($flavorAssetFilter);

		$c = new Criteria();
		$flavorAssetFilter->attachToCriteria($c);
		
		$totalCount = flavorAssetPeer::doCount($c);
		
		$pager->attachToCriteria($c);
		$dbList = flavorAssetPeer::doSelect($c);
		
		$list = KalturaFlavorAssetArray::fromDbArray($dbList);
		$response = new KalturaFlavorAssetListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;    
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
			
		$flavorParamsDb = flavorParamsPeer::retrieveByPK($flavorParamsId);
		flavorParamsPeer::setDefaultCriteriaFilter();
		if (!$flavorParamsDb)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_ID_NOT_FOUND, $flavorParamsId);
				
		$validStatuses = array(
			entryStatus::ERROR_CONVERTING,
			entryStatus::PRECONVERT,
			entryStatus::READY,
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
	 * @param bool $useCdn
	 * @return string
	 */
	public function getDownloadUrlAction($id, $useCdn = false)
	{
		$flavorAssetDb = flavorAssetPeer::retrieveById($id);
		if (!$flavorAssetDb)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $id);

		if ($flavorAssetDb->getStatus() != flavorAsset::FLAVOR_ASSET_STATUS_READY)
			throw new KalturaAPIEXception(KalturaErrors::FLAVOR_ASSET_IS_NOT_READY);

		return $flavorAssetDb->getDownloadUrl($useCdn);
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
				$flavorParams = KalturaFlavorParamsFactory::getFlavorParamsInstance($flavorParamsDb->getType());
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
			$flavorParams = KalturaFlavorParamsFactory::getFlavorParamsInstance($flavorParamsDb->getType());
			$flavorParams->fromObject($flavorParamsDb);
			
			$flavorAssetWithParams = new KalturaFlavorAssetWithParams();
			$flavorAssetWithParams->entryId = $entryId;
			$flavorAssetWithParams->flavorParams = $flavorParams;
			$flavorAssetWithParamsArray[] = $flavorAssetWithParams;
		}
		
		return $flavorAssetWithParamsArray;
	}
}