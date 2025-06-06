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
		$dbCurrentCredit = $dbReachProfile->getCredit();
		$updatedCredit = $reachProfile->credit;
		$dbReachProfile = $reachProfile->toUpdatableObject($dbReachProfile);
		
		if ($updatedCredit)
		{
			if ($dbCurrentCredit instanceof kReoccurringVendorCredit && $dbCurrentCredit->wasCreditUpdated($updatedCredit))
			{
				$currentCredit = $dbReachProfile->getCredit();
				/* @var $updatedCredit kReoccurringVendorCredit */
				$currentCredit->setPeriodDates();
				$dbReachProfile->setCredit($currentCredit);
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
		{
			throw new KalturaAPIException(KalturaReachErrors::REACH_PROFILE_NOT_FOUND, $id);
		}

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
	 * sync vendor profile credit
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
	
}
