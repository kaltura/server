<?php

/**
 * The ResourceUser service enables you to create and manage associations between users and resources.
 * @service resourceUser
 * @package plugins.schedule
 * @subpackage api.services
 */
class ResourceUserService extends KalturaBaseService
{
    public function initService($serviceId, $serviceName, $actionName)
    {
        parent::initService($serviceId, $serviceName, $actionName);
        $this->applyPartnerFilterForClass('ResourceUser');
    }

    /**
     * Allows you to add a new KalturaScheduleResource object
     *
     * @action add
     * @param KalturaResourceUser $resourceUser
     * @return KalturaResourceUser
     */
    public function addAction(KalturaResourceUser $resourceUser)
    {
	    // save in database
	    $dbScheduleResource = $resourceUser->toInsertableObject();
	    $dbScheduleResource->save();

	    // return the saved object
	    $scheduleResource = new KalturaResourceUser();
	    $scheduleResource->fromObject($dbScheduleResource, $this->getResponseProfile());
	    return $scheduleResource;
    }


    /**
     * Allows you to delete an existing KalturaResourceUser object
     *
     * @action delete
     * @param string $resourceTag
     * @param string $userId
     * @return KalturaResourceUser
     */
    public function deleteAction($resourceTag, $userId)
    {
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
		$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $userId);

	    if (!$kuser)
	    {
		    if (kCurrentContext::$master_partner_id != Partner::BATCH_PARTNER_ID)
		    {
			    throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $userId);
		    }

		    kuserPeer::setUseCriteriaFilter(false);
		    $kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $userId);
		    kuserPeer::setUseCriteriaFilter(true);

		    if (!$kuser)
			    throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $userId);
	    }

	    $dbResourceUser = ResourceUserPeer::retrieveByResourceTagAndKuserId($resourceTag, $kuser->getId());
	    if(!$dbResourceUser)
	    {
		    throw new KalturaAPIException(KalturaErrors::OBJECT_NOT_FOUND);
	    }

	    $dbResourceUser->setStatus(ResourceUserStatus::DELETED);
	    $dbResourceUser->save();

	    $resourceUser = new KalturaResourceUser();
	    $resourceUser->fromObject($dbResourceUser, $this->getResponseProfile());

	    return $resourceUser;
    }


	/**
	 * Allows you to list a group KalturaScheduleResource objects
	 *
	 * @action list
	 * @param KalturaResourceUserFilter $scheduleResourceFilter
	 * @param KalturaFilterPager $pager
	 * @return KalturaResourceUserListResponse
	 */
	public function listAction(KalturaResourceUserFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter || !($filter->resourceTagEqual || $filter->resourceTagIn || $filter->userIdEqual || $filter->userIdIn))
		{
			throw new KalturaAPIException(KalturaScheduleErrors::MUST_FILTER_ON_TAG_OR_USER_ID);
		}

		if(!$pager)
		{
			$pager = new KalturaFilterPager();
		}

		return $filter->getListResponse($pager, $this->getResponseProfile());
	}


}