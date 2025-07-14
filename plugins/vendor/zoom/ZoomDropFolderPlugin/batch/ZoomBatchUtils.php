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
		$user = self::getKalturaUser($partnerId, $zoomUser, $zoomVendorIntegration->userSearchMethod);
		KBatchBase::unimpersonate();
		$userId = '';
		if ($user)
		   {
			   KalturaLog::debug('Found [' . $user->id . ']');
			   $userId = $user->id;
		   }
		else
		{
			KalturaLog::debug('Not Found [' . $user->id . ']');
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

	protected static function getElasticSearchOperator($fieldName, $searchType, $searchTerm)
	{
		$searchParams = new KalturaESearchUserParams();
		$searchParams->searchOperator = new KalturaESearchUserOperator();
		$searchParams->searchOperator->searchItems = [];
		$searchParams->searchOperator->searchItems[0] = new KalturaESearchUserItem();
		$searchParams->searchOperator->searchItems[0]->fieldName = $fieldName;
		$searchParams->searchOperator->searchItems[0]->searchTerm = $searchTerm;
		$searchParams->searchOperator->searchItems[0]->itemType = $searchType;

		return $searchParams;
	}


	public static function getKalturaUser($partnerId, $kZoomUser, $userSearchMethod = null)
	{
		$pager = new KalturaFilterPager();
		$pager->pageSize = 1;
		$pager->pageIndex = 1;

		KalturaLog::debug('Searching for user with id [' . $kZoomUser->getProcessedName() . ']');
		$elasticSearchPlugin = KalturaElasticSearchClientPlugin::get(KBatchBase::$kClient);
		$primarySearchParams = self::getElasticSearchOperator(
			KalturaESearchUserFieldName::USER_ID,
			KalturaESearchItemType::EXACT_MATCH,
			$kZoomUser->getProcessedName()
		);

		$kalturaUser = $elasticSearchPlugin->eSearch->searchUser($primarySearchParams, $pager);
		$secondarySearchParams = null;

		KalturaLog::debug('Searching method [' . $userSearchMethod . ']');
		if(!isset($kalturaUser->objects[0]))
		{
			KalturaLog::debug('User not found by id [' . $kZoomUser->getProcessedName() . ']');
			switch ($userSearchMethod)
			{
				case KalturaZoomUsersSearchMethod::EXTERNAL:
				{
					KalturaLog::debug('Searching by external_id [' . $kZoomUser->getProcessedName() . ']');
					$secondarySearchParams = self::getElasticSearchOperator(
						KalturaESearchUserFieldName::EXTERNAL_ID,
						KalturaESearchItemType::EXACT_MATCH,
						$kZoomUser->getProcessedName()
					);
					break;
				}
				case KalturaZoomUsersSearchMethod::EMAIL:
				default:
				{
					KalturaLog::debug('Searching by email [' . $kZoomUser->getProcessedName() . ']');
					$secondarySearchParams = self::getElasticSearchOperator(
						KalturaESearchUserFieldName::EMAIL,
						KalturaESearchItemType::STARTS_WITH,
						$kZoomUser->getOriginalName()
					);
					break;
				}
			}
		}

		// Search only if there is no previous value in the argument, or it holds an empty list from an earlier call
		$kalturaUser = (!isset($kalturaUser->objects[0])) ? $elasticSearchPlugin->eSearch->searchUser($secondarySearchParams, $pager) : $kalturaUser;
		if(isset($kalturaUser->objects[0]))
		{
			KalturaLog::debug('Found user with id [' . $kalturaUser->objects[0]->object->id . ']');
			return $kalturaUser->objects[0]->object;
		}
		KalturaLog::debug('Could not find user');
		return null;
	}

	public static function processZoomUserName($userName, $zoomVendorIntegration, $zoomClient)
	{
		$result = $userName;
		KalturaLog::debug('Processing User [' . $result . '] with method [' . $zoomVendorIntegration->zoomUserMatchingMode . ']');
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