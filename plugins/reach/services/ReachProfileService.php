<?php
/**
 * Reach Profile Service
 *
 * @service reachProfile
 * @package plugins.reach
 * @subpackage api.services
 * @throws KalturaErrors::SERVICE_FORBIDDEN
 */

class ReachProfileService extends KalturaBaseService
{

	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		if (!ReachPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, ReachPlugin::PLUGIN_NAME);

		$this->applyPartnerFilterForClass('reachProfile');
	}

	/**
	 * Allows you to add a partner specific reach profile
	 *
	 * @action add
	 * @param KalturaReachProfile $reachProfile
	 * @return KalturaReachProfile
	 */
	public function addAction(KalturaReachProfile $reachProfile)
	{
		$dbReachProfile = $reachProfile->toInsertableObject();

		/* @var $dbReachProfile ReachProfile */
		$dbReachProfile->setPartnerId(kCurrentContext::getCurrentPartnerId());
		$dbReachProfile->setStatus(KalturaReachProfileStatus::ACTIVE);
		$credit = $dbReachProfile->getCredit();
		if ( $credit && $credit instanceof kReoccurringVendorCredit)
		{
			/* @var $credit kReoccurringVendorCredit */
			$credit->setPeriodDates();
			$dbReachProfile->setCredit($credit);
		}

		$dbReachProfile->save();

		// return the saved object
		$reachProfile->fromObject($dbReachProfile, $this->getResponseProfile());
		return $reachProfile;
	}

	/**
	 * Retrieve specific reach profile by id
	 *
	 * @action get
	 * @param int $id
	 * @return KalturaReachProfile
	 * @throws KalturaReachErrors::REACH_PROFILE_NOT_FOUND
	 */
	function getAction($id)
	{
		$dbReachProfile = ReachProfilePeer::retrieveByPK($id);
		if (!$dbReachProfile)
			throw new KalturaAPIException(KalturaReachErrors::REACH_PROFILE_NOT_FOUND, $id);

		$reachProfile = new KalturaReachProfile();
		$reachProfile->fromObject($dbReachProfile, $this->getResponseProfile());

		$partner = PartnerPeer::retrieveByPK($dbReachProfile->getPartnerId());
		if ($partner)
		{
			$reachProfile->globalCredit = $partner->getCredit();
		}
		return $reachProfile;
	}

	/**
	 * List KalturaReachProfile objects
	 *
	 * @action list
	 * @param KalturaReachProfileFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaReachProfileListResponse
	 */
	public function listAction(KalturaReachProfileFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaReachProfileFilter();

		if (!$pager)
			$pager = new KalturaFilterPager();

		return $filter->getListResponse($pager, $this->getResponseProfile());
	}

	/**
	 * Update an existing reach profile object
	 *
	 * @action update
	 * @param int $id
	 * @param KalturaReachProfile $reachProfile
	 * @return KalturaReachProfile
	 *
	 * @throws KalturaReachErrors::REACH_PROFILE_NOT_FOUND
	 */
	public function updateAction($id, KalturaReachProfile $reachProfile)
	{
		// get the object
		/* @var $dbReachProfile ReachProfile */
		$dbReachProfile = ReachProfilePeer::retrieveByPK($id);
		if (!$dbReachProfile)
			throw new KalturaAPIException(KalturaReachErrors::REACH_PROFILE_NOT_FOUND, $id);

		// save the object
		$dbReachProfile = $reachProfile->toUpdatableObject($dbReachProfile);
		$credit = $dbReachProfile->getCredit();
		if ($credit)
		{
			if ($credit instanceof kReoccurringVendorCredit)
			{
				/* @var $credit kReoccurringVendorCredit */
				$credit->setPeriodDates();
				$dbReachProfile->setCredit($credit);
			}
			$dbReachProfile->calculateCreditPercentUsage();
		}
		$dbReachProfile->save();

		// return the saved object
		$reachProfile = new KalturaReachProfile();
		$reachProfile->fromObject($dbReachProfile, $this->getResponseProfile());
		return $reachProfile;
	}

	/**
	 * Update reach profile status by id
	 *
	 * @action updateStatus
	 * @param int $id
	 * @param KalturaReachProfileStatus $status
	 * @return KalturaReachProfile
	 *
	 * @throws KalturaReachErrors::REACH_PROFILE_NOT_FOUND
	 */
	function updateStatusAction($id, $status)
	{
		// get the object
		$dbReachProfile = ReachProfilePeer::retrieveByPK($id);
		if (!$dbReachProfile)
			throw new KalturaAPIException(KalturaReachErrors::REACH_PROFILE_NOT_FOUND, $id);
		
		$dbReachProfile->setStatus($status);
		$credit = $dbReachProfile->getCredit();
		if ($status == KalturaReachProfileStatus::ACTIVE && $credit && $credit instanceof kReoccurringVendorCredit)
        {
	        /* @var $credit kReoccurringVendorCredit */
			$credit->setPeriodDates();
			$dbReachProfile->setCredit($credit);
		}
		
		// save the object
		$dbReachProfile->save();

		// return the saved object
		$reachProfile = new KalturaReachProfile();
		$reachProfile->fromObject($dbReachProfile, $this->getResponseProfile());
		return $reachProfile;
	}

	/**
	 * Delete vednor profile by id
	 *
	 * @action delete
	 * @param int $id
	 *
	 * @throws KalturaReachErrors::REACH_PROFILE_NOT_FOUND
	 */
	public function deleteAction($id)
	{
		// get the object
		$dbReachProfile = ReachProfilePeer::retrieveByPK($id);
		if (!$dbReachProfile)
			throw new KalturaAPIException(KalturaReachErrors::REACH_PROFILE_NOT_FOUND, $id);

		// set the object status to deleted
		$dbReachProfile->setStatus(KalturaReachProfileStatus::DELETED);
		$dbReachProfile->save();
	}

	/**
	 * sync vednor profile credit
	 *
	 * @action syncCredit
	 * @param int $reachProfileId
	 * @return KalturaReachProfile
	 * @throws KalturaReachErrors::REACH_PROFILE_NOT_FOUND
	 */
	public function syncCredit($reachProfileId)
	{
		$dbReachProfile = ReachProfilePeer::retrieveByPK($reachProfileId);
		if (!$dbReachProfile)
			throw new KalturaAPIException(KalturaReachErrors::REACH_PROFILE_NOT_FOUND, $reachProfileId);

		// set the object status to deleted
		if( $dbReachProfile->shouldSyncCredit())
		{
			//ignore updating updatedAt field since we are syncing only the credit within the profile and we update the lastSyncTime
			$dbReachProfile->setIgnoreUpdatedAt(true);
			$dbReachProfile->syncCredit();
			$dbReachProfile->save();
		}

		// return the saved object
		$reachProfile = new KalturaReachProfile();
		$reachProfile->fromObject($dbReachProfile, $this->getResponseProfile());
		return $reachProfile;
	}

	/**
	 * tranfer credit between partner's global credit and reace profile credit
	 *
	 * @action transferCredit
	 * @param int $reachProfileId
	 * @param float $amount
	 * @return KalturaReachProfile
	 * @throws KalturaReachErrors::REACH_PROFILE_NOT_FOUND
	 */
	public function transferCreditAction($reachProfileId, $amount)
	{
		$partnerId = kCurrentContext::getCurrentPartnerId();
		$dbPartner = PartnerPeer::retrieveByPK( kCurrentContext::getCurrentPartnerId() );
		if (is_null($dbPartner))
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_PARTNER_ID, $partnerId);
		}

		if (is_null($amount))
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_CREDIT_VALUE, $partnerId);
		}

		$dbReachProfile = ReachProfilePeer::retrieveByPK($reachProfileId);
		if (!$dbReachProfile)
		{
			throw new KalturaAPIException(KalturaReachErrors::REACH_PROFILE_NOT_FOUND, $reachProfileId);
		}

		try
		{
			// first make sure there are enough credit in the object that we tranfer funds from
			if ($amount < 0)
			{
				$dbReachProfile->updateCreditFunds($amount);
				$dbPartner->updateCredit(-$amount);
			}
			else
			{
				$dbPartner->updateCredit(-$amount);
				$dbReachProfile->updateCreditFunds($amount);
			}

		}
		catch(Exception $ex)
		{
			if ($ex instanceof kCoreException)
			{
				$this->handleCoreException($ex,$partnerId , $reachProfileId);
			}
			else
			{
				throw $ex;
			}
		}

		$dbReachProfile->syncCreditPercentageUsage();
		$reachProfile = new KalturaReachProfile();
		$reachProfile->fromObject($dbReachProfile, $this->getResponseProfile());
		return $reachProfile;
	}

	/**
	 * @param kCoreException $ex
	 * @param $partnerId
	 * @param $reachProfileId
	 * @throws KalturaAPIException
	 * @throws kCoreException
	 */
	private function handleCoreException(kCoreException $ex, $partnerId, $reachProfileId)
	{
		switch($ex->getCode())
		{
			case kCoreException::INVALID_PARTNER_CREDIT:
				throw new KalturaAPIException(KalturaErrors::INSUFFICIENT_CREDIT, $partnerId);
			case kCoreException::INVALID_REACH_PROFILE_CREDIT:
				throw new KalturaAPIException(KalturaReachErrors::INSUFFICIENT_CREDIT_TRANSFER, $reachProfileId);
			default:
				throw $ex;
		}
	}
}
