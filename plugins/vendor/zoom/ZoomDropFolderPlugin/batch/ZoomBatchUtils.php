<?php

class ZoomBatchUtils
{
	const HOST_ID = 'host_id';
	const EMAIL = 'email';
	const CMS_USER_FIELD = 'cms_user_id';
	const KALTURA_ZOOM_DEFAULT_USER = 'KalturaZoomDefault';

	public static function shouldExcludeUserRecordingIngest ($userId, $groupParticipationType, $optInGroupNames, $optOutGroupNames, $partnerId)
	{
		if ($groupParticipationType == KalturaZoomGroupParticipationType::OPT_IN)
		{
			KalturaLog::debug('Account is configured to OPT IN the users that are members of the following groups ['.print_r($optInGroupNames, true).']');
			return self::isUserNotMemberOfGroups($userId, $partnerId, $optInGroupNames);
		}
		else
		{
			KalturaLog::debug('Account is configured to OPT OUT the users that are members of the following groups ['.print_r($optOutGroupNames, true).']');
			return !self::isUserNotMemberOfGroups($userId, $partnerId, $optOutGroupNames);
		}
	}

	protected static function isUserNotMemberOfGroups($userId, $partnerId, $participationGroupList)
	{
		$userFilter = new KalturaGroupUserFilter();
		$userFilter->userIdEqual = $userId;
	
		KBatchBase::impersonate($partnerId);
		$userGroupsResponse = KBatchBase::$kClient->groupUser->listAction($userFilter);
		KBatchBase::unimpersonate();
		
		$userGroupsArray = $userGroupsResponse->objects;
		
		$userGroupNamesArray = array();
		foreach ($userGroupsArray as $group)
		{
			array_push($userGroupNamesArray, $group->groupId);
		}
		
		KalturaLog::debug('User with id ['.$userId.'] is a member of the following groups ['.print_r($userGroupNamesArray, true).']');
		
		$intersection = array_intersect($userGroupNamesArray, $participationGroupList);
		return empty($intersection);
	}

	public static function getUserId ($zoomClient, $partnerId, $meetingFile, $zoomVendorIntegration)
	{
		$hostId = $meetingFile[self::HOST_ID];
		$zoomUser = $zoomClient->retrieveZoomUser($hostId);
		$hostEmail = '';
		if(isset($zoomUser[self::EMAIL]) && !empty($zoomUser[self::EMAIL]))
		{
			$hostEmail = $zoomUser[self::EMAIL];
		}
		return self::getEntryOwnerId($hostEmail, $partnerId, $zoomVendorIntegration, $zoomClient);
	}

	public static function getEntryOwnerId($hostEmail, $partnerId, $zoomVendorIntegration, $zoomClient)
	{
		$userId = self::KALTURA_ZOOM_DEFAULT_USER;
		$defaultUser = $zoomVendorIntegration->defaultUserId;
		$createUserIfNotExist = $zoomVendorIntegration->createUserIfNotExist;
		if($hostEmail == '')
		{
			return $createUserIfNotExist ? $userId : $defaultUser;
		}
		$zoomUser = new kZoomUser();
		$zoomUser->setOriginalName($hostEmail);
		$zoomUser->setProcessedName(self::processZoomUserName($hostEmail, $zoomVendorIntegration, $zoomClient));
		KBatchBase::impersonate($partnerId);
		/* @var $user KalturaUser */
		$user = self::getKalturaUser($partnerId, $zoomUser);
		KBatchBase::unimpersonate();
		$userId = '';
		if ($user)
		   {
		   $userId = $user->id;
		}
		else
		{
			if ($zoomVendorIntegration->createUserIfNotExist)
			{
				$userId = $zoomUser->getProcessedName();
			}
			else if ($zoomVendorIntegration->defaultUserId)
			{
				$userId = $zoomVendorIntegration->defaultUserId;
			}
		}
		return $userId;
	}

	public static function getKalturaUser($partnerId, $kZoomUser)
	{
		$pager = new KalturaFilterPager();
		$pager->pageSize = 1;
		$pager->pageIndex = 1;
		
		$filter = new KalturaUserFilter();
		$filter->partnerIdEqual = $partnerId;
		$filter->idEqual = $kZoomUser->getProcessedName();
		$kalturaUser = KBatchBase::$kClient->user->listAction($filter, $pager);
		if (!$kalturaUser->objects)
		{
			$email = $kZoomUser->getOriginalName();
			$filterUser = new KalturaUserFilter();
			$filterUser->partnerIdEqual = $partnerId;
			$filterUser->emailStartsWith = $email;
			$kalturaUser = KBatchBase::$kClient->user->listAction($filterUser, $pager);
			if (!$kalturaUser->objects || strcasecmp($kalturaUser->objects[0]->email, $email) != 0)
			{
				return null;
			}
		}
		
		if($kalturaUser->objects)
		{
			return $kalturaUser->objects[0];
		}
		return null;
	}

	public static function processZoomUserName($userName, $zoomVendorIntegration, $zoomClient)
	{
		$result = $userName;
		switch ($zoomVendorIntegration->zoomUserMatchingMode)
		{
			case kZoomUsersMatching::ADD_POSTFIX:
				$postFix = $zoomVendorIntegration->zoomUserPostfix;
				if (!kString::endsWith($result, $postFix, false))
				{
					$result = $result . $postFix;
				}
				
				break;
			case kZoomUsersMatching::REMOVE_POSTFIX:
				$postFix = $zoomVendorIntegration->zoomUserPostfix;
				if (kString::endsWith($result, $postFix, false))
				{
					$result = substr($result, 0, strlen($result) - strlen($postFix));
				}
				
				break;
			case kZoomUsersMatching::CMS_MATCHING:
				$zoomUser = $zoomClient->retrieveZoomUser($userName);
				if(isset($zoomUser[self::CMS_USER_FIELD]) && !empty($zoomUser[self::CMS_USER_FIELD]))
				{
					$result = $zoomUser[self::CMS_USER_FIELD];
				}
				break;
			case kZoomUsersMatching::DO_NOT_MODIFY:
			default:
				break;
		}
		
		return $result;
	}
}