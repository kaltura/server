<?php
/**
 * Entry Distribution service
 *
 * @service entryDistribution
 * @package plugins.contentDistribution
 * @subpackage api.services
 */
class EntryDistributionService extends KalturaBaseService
{
	public function initService($serviceName, $actionName)
	{
		parent::initService($serviceName, $actionName);

		myPartnerUtils::addPartnerToCriteria(new EntryDistributionPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		
		if(!ContentDistributionPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
	}
	
	/**
	 * Add new Entry Distribution
	 * 
	 * @action add
	 * @param KalturaEntryDistribution $entryDistribution
	 * @return KalturaEntryDistribution
	 * @throws ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_ALREADY_EXISTS
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	function addAction(KalturaEntryDistribution $entryDistribution)
	{
		$entryDistribution->validatePropertyNotNull("entryId");
		$entryDistribution->validatePropertyNotNull("distributionProfileId");
					
		$dbDistributionProfile = DistributionProfilePeer::retrieveByPK($entryDistribution->distributionProfileId);
		if (!$dbDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND, $entryDistribution->distributionProfileId);
		if ($dbDistributionProfile->getStatus() == DistributionProfileStatus::DISABLED)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_DISABLED, $entryDistribution->distributionProfileId);
		
		$dbEntry = entryPeer::retrieveByPK($entryDistribution->entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryDistribution->entryId);
		
		$dbEntryDistribution = EntryDistributionPeer::retrieveByEntryAndProfileId($entryDistribution->entryId, $entryDistribution->distributionProfileId);
		if($dbEntryDistribution)
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_ALREADY_EXISTS, $entryDistribution->entryId, $entryDistribution->distributionProfileId);
		
		$dbEntryDistribution = kContentDistributionManager::addEntryDistribution($dbEntry, $dbDistributionProfile);
		$entryDistribution->toInsertableObject($dbEntryDistribution);
		$dbEntryDistribution->setPartnerId($this->getPartnerId());
		$dbEntryDistribution->save();
		
		$entryDistribution = new KalturaEntryDistribution();
		$entryDistribution->fromObject($dbEntryDistribution);
		return $entryDistribution;
	}
	
	/**
	 * Get Entry Distribution by id
	 * 
	 * @action get
	 * @param int $id
	 * @return KalturaEntryDistribution
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND
	 */
	function getAction($id)
	{
		$dbEntryDistribution = EntryDistributionPeer::retrieveByPK($id);
		if (!$dbEntryDistribution)
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND, $id);
			
		$entryDistribution = new KalturaEntryDistribution();
		$entryDistribution->fromObject($dbEntryDistribution);
		return $entryDistribution;
	}
	
	/**
	 * Validates Entry Distribution by id for submission
	 * 
	 * @action validate
	 * @param int $id
	 * @return KalturaEntryDistribution
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND
	 * @throws ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND
	 * @throws ContentDistributionErrors::DISTRIBUTION_PROFILE_DISABLED
	 */
	function validateAction($id)
	{
		$dbEntryDistribution = EntryDistributionPeer::retrieveByPK($id);
		if (!$dbEntryDistribution)
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND, $id);
			
		$distributionProfileId = $dbEntryDistribution->getDistributionProfileId();
		$dbDistributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if(!$dbDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND, $distributionProfileId);
		if ($dbDistributionProfile->getStatus() == DistributionProfileStatus::DISABLED)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_DISABLED, $entryDistribution->distributionProfileId);
		
		$dbEntry = entryPeer::retrieveByPK($dbEntryDistribution->getEntryId());
		if($dbEntry)
		{
			kContentDistributionManager::assignFlavorAssets($dbEntryDistribution, $dbEntry, $dbDistributionProfile);
			kContentDistributionManager::assignThumbAssets($dbEntryDistribution, $dbEntry, $dbDistributionProfile);
		}
		
		$validationErrors = $dbDistributionProfile->validateForSubmission($dbEntryDistribution, DistributionAction::SUBMIT);
		$dbEntryDistribution->setValidationErrorsArray($validationErrors);
		$dbEntryDistribution->save();

		$entryDistribution = new KalturaEntryDistribution();
		$entryDistribution->fromObject($dbEntryDistribution);
		return $entryDistribution;
	}
	
	/**
	 * Update Entry Distribution by id
	 * 
	 * @action update
	 * @param int $id
	 * @param KalturaEntryDistribution $entryDistribution
	 * @return KalturaEntryDistribution
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND
	 */
	function updateAction($id, KalturaEntryDistribution $entryDistribution)
	{
		$dbEntryDistribution = EntryDistributionPeer::retrieveByPK($id);
		if (!$dbEntryDistribution)
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND, $id);
		
		$entryDistribution->toUpdatableObject($dbEntryDistribution);
		$dbEntryDistribution->save();
		
		$entryDistribution = new KalturaEntryDistribution();
		$entryDistribution->fromObject($dbEntryDistribution);
		return $entryDistribution;
	}
	
	/**
	 * Delete Entry Distribution by id
	 * 
	 * @action delete
	 * @param int $id
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND
	 */
	function deleteAction($id)
	{
		$dbEntryDistribution = EntryDistributionPeer::retrieveByPK($id);
		if (!$dbEntryDistribution)
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND, $id);

		$dbEntryDistribution->setStatus(EntryDistributionStatus::DELETED);
		$dbEntryDistribution->save();
	}
	
	
	/**
	 * List all distribution providers
	 * 
	 * @action list
	 * @param KalturaEntryDistributionFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaEntryDistributionListResponse
	 */
	function listAction(KalturaEntryDistributionFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaEntryDistributionFilter();
			
		$c = new Criteria();
		$entryDistributionFilter = new EntryDistributionFilter();
		$filter->toObject($entryDistributionFilter);
		
		$entryDistributionFilter->attachToCriteria($c);
		$count = EntryDistributionPeer::doCount($c);
		
		if ($pager)
			$pager->attachToCriteria($c);
		$list = EntryDistributionPeer::doSelect($c);
		
		$response = new KalturaEntryDistributionListResponse();
		$response->objects = KalturaEntryDistributionArray::fromDbArray($list);
		$response->totalCount = $count;
	
		return $response;
	}
	
	/**
	 * Submits Entry Distribution to the remote destination
	 * 
	 * @action submitAdd
	 * @param int $id
	 * @param bool $submitWhenReady
	 * @return KalturaEntryDistribution
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND
	 * @throws ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND
	 * @throws ContentDistributionErrors::DISTRIBUTION_PROFILE_DISABLED
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_STATUS
	 */
	function submitAddAction($id, $submitWhenReady = false)
	{
		$dbEntryDistribution = EntryDistributionPeer::retrieveByPK($id);
		if (!$dbEntryDistribution)
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND, $id);
		
		$validStatus = array(
			EntryDistributionStatus::ERROR_DELETING,
			EntryDistributionStatus::ERROR_SUBMITTING,
			EntryDistributionStatus::ERROR_UPDATING,
			EntryDistributionStatus::PENDING,
			EntryDistributionStatus::QUEUED,
			EntryDistributionStatus::READY,
			EntryDistributionStatus::REMOVED,
		);
		
		if(!in_array($dbEntryDistribution->getStatus(), $validStatus))
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_STATUS, $id, $dbEntryDistribution->getStatus());
		
		$distributionProfileId = $dbEntryDistribution->getDistributionProfileId();
		$dbDistributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if(!$dbDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND, $distributionProfileId);
		if ($dbDistributionProfile->getStatus() == DistributionProfileStatus::DISABLED)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_DISABLED, $entryDistribution->distributionProfileId);
		
		kContentDistributionManager::submitAddEntryDistribution($dbEntryDistribution, $dbDistributionProfile, $submitWhenReady);

		$dbEntryDistribution->reload();
		$entryDistribution = new KalturaEntryDistribution();
		$entryDistribution->fromObject($dbEntryDistribution);
		return $entryDistribution;
	}

	
	/**
	 * Submits Entry Distribution changes to the remote destination
	 * 
	 * @action submitUpdate
	 * @param int $id
	 * @return KalturaEntryDistribution
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND
	 * @throws ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND
	 * @throws ContentDistributionErrors::DISTRIBUTION_PROFILE_DISABLED
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_STATUS
	 */
	function submitUpdateAction($id)
	{
		$dbEntryDistribution = EntryDistributionPeer::retrieveByPK($id);
		if (!$dbEntryDistribution)
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND, $id);
		
		$validStatus = array(
			EntryDistributionStatus::ERROR_DELETING,
			EntryDistributionStatus::ERROR_UPDATING,
			EntryDistributionStatus::READY,
		);
		
		if(!in_array($dbEntryDistribution->getStatus(), $validStatus))
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_STATUS, $id, $dbEntryDistribution->getStatus());
		
		$distributionProfileId = $dbEntryDistribution->getDistributionProfileId();
		$dbDistributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if(!$dbDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND, $distributionProfileId);
		if ($dbDistributionProfile->getStatus() == DistributionProfileStatus::DISABLED)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_DISABLED, $entryDistribution->distributionProfileId);
		
		kContentDistributionManager::submitUpdateEntryDistribution($dbEntryDistribution, $dbDistributionProfile);

		$dbEntryDistribution->reload();
		$entryDistribution = new KalturaEntryDistribution();
		$entryDistribution->fromObject($dbEntryDistribution);
		return $entryDistribution;
	}

	
	/**
	 * Submits Entry Distribution report request
	 * 
	 * @action submitFetchReport
	 * @param int $id
	 * @return KalturaEntryDistribution
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND
	 * @throws ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND
	 * @throws ContentDistributionErrors::DISTRIBUTION_PROFILE_DISABLED
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_STATUS
	 */
	function submitFetchReportAction($id)
	{
		$dbEntryDistribution = EntryDistributionPeer::retrieveByPK($id);
		if (!$dbEntryDistribution)
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND, $id);
		
		$validStatus = array(
			EntryDistributionStatus::READY,
		);
		
		if(!in_array($dbEntryDistribution->getStatus(), $validStatus))
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_STATUS, $id, $dbEntryDistribution->getStatus());
		
		$distributionProfileId = $dbEntryDistribution->getDistributionProfileId();
		$dbDistributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if(!$dbDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND, $distributionProfileId);
		if ($dbDistributionProfile->getStatus() == DistributionProfileStatus::DISABLED)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_DISABLED, $entryDistribution->distributionProfileId);
		
		kContentDistributionManager::submitFetchEntryDistributionReport($dbEntryDistribution, $dbDistributionProfile);

		$dbEntryDistribution->reload();
		$entryDistribution = new KalturaEntryDistribution();
		$entryDistribution->fromObject($dbEntryDistribution);
		return $entryDistribution;
	}

	
	/**
	 * Deletes Entry Distribution from the remote destination
	 * 
	 * @action submitDelete
	 * @param int $id
	 * @return KalturaEntryDistribution
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND
	 * @throws ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND
	 * @throws ContentDistributionErrors::DISTRIBUTION_PROFILE_DISABLED
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_STATUS
	 */
	function submitDeleteAction($id)
	{
		$dbEntryDistribution = EntryDistributionPeer::retrieveByPK($id);
		if (!$dbEntryDistribution)
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND, $id);
		
		$validStatus = array(
			EntryDistributionStatus::ERROR_DELETING,
			EntryDistributionStatus::ERROR_UPDATING,
			EntryDistributionStatus::READY,
		);
		
		if(!in_array($dbEntryDistribution->getStatus(), $validStatus))
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_STATUS, $id, $dbEntryDistribution->getStatus());
		
		$distributionProfileId = $dbEntryDistribution->getDistributionProfileId();
		$dbDistributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if(!$dbDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND, $distributionProfileId);
		if ($dbDistributionProfile->getStatus() == DistributionProfileStatus::DISABLED)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_DISABLED, $entryDistribution->distributionProfileId);
		
		kContentDistributionManager::submitDeleteEntryDistribution($dbEntryDistribution, $dbDistributionProfile);

		$dbEntryDistribution->reload();
		$entryDistribution = new KalturaEntryDistribution();
		$entryDistribution->fromObject($dbEntryDistribution);
		return $entryDistribution;
	}

	
	/**
	 * Retries last submit action
	 * 
	 * @action retrySubmit
	 * @param int $id
	 * @return KalturaEntryDistribution
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND
	 * @throws ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND
	 * @throws ContentDistributionErrors::DISTRIBUTION_PROFILE_DISABLED
	 */
	function retrySubmitAction($id)
	{
		$dbEntryDistribution = EntryDistributionPeer::retrieveByPK($id);
		if (!$dbEntryDistribution)
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND, $id);
		
		$distributionProfileId = $dbEntryDistribution->getDistributionProfileId();
		$dbDistributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if(!$dbDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND, $distributionProfileId);
		if ($dbDistributionProfile->getStatus() == DistributionProfileStatus::DISABLED)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_DISABLED, $entryDistribution->distributionProfileId);
		
		switch($dbEntryDistribution->getStatus())
		{
			case EntryDistributionStatus::QUEUED:
			case EntryDistributionStatus::SUBMITTING: 
			case EntryDistributionStatus::ERROR_SUBMITTING:
				kContentDistributionManager::submitAddEntryDistribution($dbEntryDistribution, $dbDistributionProfile, false);
				$dbEntryDistribution->reload();
				break;
				
			case EntryDistributionStatus::UPDATING:
			case EntryDistributionStatus::ERROR_UPDATING:
				kContentDistributionManager::submitUpdateEntryDistribution($dbEntryDistribution, $dbDistributionProfile);
				$dbEntryDistribution->reload();
				break;
				
			case EntryDistributionStatus::DELETING:
			case EntryDistributionStatus::ERROR_DELETING:
				kContentDistributionManager::submitDeleteEntryDistribution($dbEntryDistribution, $dbDistributionProfile);
				$dbEntryDistribution->reload();
				break;
				
			case EntryDistributionStatus::PENDING:
			case EntryDistributionStatus::READY:
			case EntryDistributionStatus::DELETED:
				break;
		}

		$entryDistribution = new KalturaEntryDistribution();
		$entryDistribution->fromObject($dbEntryDistribution);
		return $entryDistribution;
	}

	/**
	 * Serves entry distribution sent data
	 *  
	 * @action serveSentData
	 * @serverOnly
	 * @param int $id
	 * @param KalturaDistributionAction $actionType
	 *  
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_MISSING_LOG
	 */
	public function serveSentDataAction($id, $actionType)
	{
		$dbEntryDistribution = EntryDistributionPeer::retrieveByPK($id);
		if (!$dbEntryDistribution)
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND, $id);
		
		$fileName = "{$id}_{$actionType}_sent.xml";
		$fileSubType = null;
		switch($actionType)
		{
			case KalturaDistributionAction::SUBMIT:
				$fileSubType = EntryDistribution::FILE_SYNC_ENTRY_DISTRIBUTION_SUBMIT_DATA;
				break;
			case KalturaDistributionAction::UPDATE:
				$fileSubType = EntryDistribution::FILE_SYNC_ENTRY_DISTRIBUTION_UPDATE_DATA;
				break;
			case KalturaDistributionAction::DELETE:
				$fileSubType = EntryDistribution::FILE_SYNC_ENTRY_DISTRIBUTION_DELETE_DATA;
				break;
		}
		if(!$fileSubType)
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_MISSING_LOG, $id);
		
		return $this->serveFile($dbEntryDistribution, $fileSubType, $fileName);
	}

	/**
	 * Serves entry distribution returned data
	 *  
	 * @action serveReturnedData
	 * @serverOnly
	 * @param int $id
	 * @param KalturaDistributionAction $actionType
	 *  
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_MISSING_LOG
	 */
	public function serveReturnedDataAction($id, $actionType)
	{
		$dbEntryDistribution = EntryDistributionPeer::retrieveByPK($id);
		if (!$dbEntryDistribution)
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND, $id);
		
		$fileName = "{$id}_{$actionType}_return.xml";
		$fileSubType = null;
		switch($actionType)
		{
			case KalturaDistributionAction::SUBMIT:
				$fileSubType = EntryDistribution::FILE_SYNC_ENTRY_DISTRIBUTION_SUBMIT_RESULTS;
				break;
			case KalturaDistributionAction::UPDATE:
				$fileSubType = EntryDistribution::FILE_SYNC_ENTRY_DISTRIBUTION_UPDATE_RESULTS;
				break;
			case KalturaDistributionAction::DELETE:
				$fileSubType = EntryDistribution::FILE_SYNC_ENTRY_DISTRIBUTION_DELETE_RESULTS;
				break;
		}
		if(!$fileSubType)
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_MISSING_LOG, $id);
		
		return $this->serveFile($dbEntryDistribution, $fileSubType, $fileName);
	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param int $fileSubType
	 * @param string $fileName
	 * @param bool $forceProxy
	 * @throws KalturaAPIException
	 */
	protected function serveFile(EntryDistribution $entryDistribution, $fileSubType, $fileName, $forceProxy = false)
	{
		$syncKey = $entryDistribution->getSyncKey($fileSubType);
		if(!kFileSyncUtils::fileSync_exists($syncKey))
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_MISSING_LOG, $entryDistribution->getId(), $fileSubType);

		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, true, false);
		
		header("Content-Disposition: attachment; filename=\"$fileName\"");
		
		if($local)
		{
			$filePath = $fileSync->getFullPath();
			$mimeType = kFile::mimeType($filePath);
			kFile::dumpFile($filePath, $mimeType);
		}
		else
		{
			$remoteUrl = kDataCenterMgr::getRedirectExternalUrl($fileSync);
			KalturaLog::info("Redirecting to [$remoteUrl]");
			if($forceProxy)
			{
				kFile::dumpUrl($remoteUrl);
			}
			else
			{
				// or redirect if no proxy
				header("Location: $remoteUrl");
			}
		}	
	}
}
