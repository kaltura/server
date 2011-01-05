<?php
/**
 * Entry Distribution service
 *
 * @service entryDistribution
 */
class EntryDistributionService extends KalturaBaseService
{
	public function initService($partnerId, $puserId, $ksStr, $serviceName, $action)
	{
		parent::initService($partnerId, $puserId, $ksStr, $serviceName, $action);

		myPartnerUtils::addPartnerToCriteria(new EntryDistributionPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		
		if(!ContentDistributionPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN);
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
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND
	 */
	function validateAction($id)
	{
		$dbEntryDistribution = EntryDistributionPeer::retrieveByPK($id);
		if (!$dbEntryDistribution)
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND, $id);
			
		$distributionProfileId = $dbEntryDistribution->getDistributionProfileId();
		$dbDistributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if(!$dbDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND, $distributionProfileId);
		
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
	 * @throws ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND
	 */
	function deleteAction($id)
	{
		$dbEntryDistribution = EntryDistributionPeer::retrieveByPK($id);
		if (!$dbEntryDistribution)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND, $id);

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
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND
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
		);
		
		if(!in_array($dbEntryDistribution->getStatus(), $validStatus))
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_STATUS, $id, $dbEntryDistribution->getStatus());
		
		$distributionProfileId = $dbEntryDistribution->getDistributionProfileId();
		$dbDistributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if(!$dbDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND, $distributionProfileId);
		
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
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND
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
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND, $distributionProfileId);
		
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
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_STATUS
	 */
	function submitFetchReportAction($id)
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
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND, $distributionProfileId);
		
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
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND
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
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND, $distributionProfileId);
		
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
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND
	 */
	function retrySubmitAction($id)
	{
		$dbEntryDistribution = EntryDistributionPeer::retrieveByPK($id);
		if (!$dbEntryDistribution)
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND, $id);
		
		$distributionProfileId = $dbEntryDistribution->getDistributionProfileId();
		$dbDistributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if(!$dbDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND, $distributionProfileId);
		
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
}
