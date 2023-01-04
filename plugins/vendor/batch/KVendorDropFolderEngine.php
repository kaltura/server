<?php
/**
 * @package plugins.vendor
 */
abstract class KVendorDropFolderEngine extends KDropFolderFileTransferEngine
{
	const MAX_PUSER_LENGTH = 100;
	const TAG_SOURCE = "source";
	const SOURCE_FLAVOR_ID = 0;
	const LABEL_DEL = '_';
	const CHAT_FILE_TYPE = 'txt';
	
	abstract protected function getDefaultUserString();
	
	abstract protected function getEntryOwnerId($vendorIntegration, $hostEmail);
	
	protected function excludeRecordingIngestForUser($vendorIntegration, $hostEmail, $groupParticipationType, $optInGroupNames, $optOutGroupNames)
	{
		$partnerId = $this->dropFolder->partnerId;
		
		$userId = $this->getEntryOwnerId($vendorIntegration, $hostEmail);
		if (!$userId)
		{
			KalturaLog::err("Could not find user [$hostEmail]");
			return true;
		}
		
		if ($groupParticipationType == kVendorGroupParticipationType::OPT_IN)
		{
			KalturaLog::debug('Account is configured to OPT IN the users that are members of the following groups ['.print_r($optInGroupNames, true).']');
			return $this->isUserNotMemberOfGroups($userId, $partnerId, $optInGroupNames);
		}
		elseif ($groupParticipationType == kVendorGroupParticipationType::OPT_OUT)
		{
			KalturaLog::debug('Account is configured to OPT OUT the users that are members of the following groups ['.print_r($optOutGroupNames, true).']');
			return !$this->isUserNotMemberOfGroups($userId, $partnerId, $optOutGroupNames);
		}
	}
	
	protected function isUserNotMemberOfGroups($userId, $partnerId, $participationGroupList)
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
	
	protected function addEntryToCategory($categoryName, $entryId, $partnerId)
	{
		$categoryId = $this->findCategoryIdByName($categoryName, $partnerId);
		if ($categoryId)
		{
			$this->addCategoryEntry($categoryId, $entryId, $partnerId);
		}
	}
	
	protected function findCategoryIdByName($categoryName, $partnerId)
	{
		$isFullPath = $this->isFullPath($categoryName);
		
		$categoryFilter = new KalturaCategoryFilter();
		if ($isFullPath)
		{
			$categoryFilter->fullNameEqual = $categoryName;
		}
		else
		{
			$categoryFilter->nameOrReferenceIdStartsWith = $categoryName;
		}
		
		KBatchBase::impersonate($partnerId);
		$categoryResponse = KBatchBase::$kClient->category->listAction($categoryFilter, new KalturaFilterPager());
		KBatchBase::unimpersonate();
		$categoryId = null;
		if ($isFullPath)
		{
			if ($categoryResponse->objects && count($categoryResponse->objects) == 1)
			{
				$categoryId = $categoryResponse->objects[0]->id;
			}
		}
		else
		{
			$categoryIds = array();
			foreach ($categoryResponse->objects as $category)
			{
				if ($category->name === $categoryName)
				{
					$categoryIds[] = $category->id;
				}
			}
			$categoryId = (count($categoryIds) == 1) ? $categoryIds[0] : null;
		}
		return $categoryId;
	}
	
	protected function isFullPath($categoryName)
	{
		$numCategories = count(explode('>', $categoryName));
		return ($numCategories > 1);
	}
	
	protected function addCategoryEntry($categoryId, $entryId, $partnerId)
	{
		$categoryEntry = new KalturaCategoryEntry();
		$categoryEntry->categoryId = $categoryId;
		$categoryEntry->entryId = $entryId;
		KBatchBase::impersonate($partnerId);
		KBatchBase::$kClient->categoryEntry->add($categoryEntry);
		KBatchBase::unimpersonate();
	}
	
	protected function getKalturaUserIdsFromVendorUsers($vendorUsers, $partnerId, $createIfNotFound, $userToExclude)
	{
		if (!$vendorUsers)
		{
			return $vendorUsers;
		}
		
		$userIdsList = array();
		foreach ($vendorUsers as $vendorUser)
		{
			try
			{
				KalturaLog::info("Attempting to find vendor user {$vendorUser->getOriginalName()} in Kaltura");
				/* @var $vendorUser kVendorUser */
				/* @var $kalturaUser KalturaUser */
				$kalturaUser = $this->getKalturaUser($partnerId, $vendorUser);
				if ($kalturaUser)
				{
					if (strtolower($kalturaUser->id) !== $userToExclude)
					{
						$userIdsList[] = $kalturaUser->id;
					}
				}
				elseif ($createIfNotFound)
				{
					KalturaLog::info("Vendor user not found in Kaltura, creating new user [{$vendorUser->getProcessedName()}]");
					$this->createNewVendorUser($partnerId, $vendorUser->getProcessedName());
					$userIdsList[] = $vendorUser->getProcessedName();
				}
			}
			catch (Exception $e)
			{
				KalturaLog::warning("Error handling user [{$vendorUser->getOriginalName()}] from vendor: " . $e->getMessage());
				continue;
			}
		}
		return $userIdsList;
	}
	
	protected function getKalturaUser($partnerId, kVendorUser $vendorUser)
	{
		$pager = new KalturaFilterPager();
		$pager->pageSize = 1;
		$pager->pageIndex = 1;
		
		$filter = new KalturaUserFilter();
		$filter->partnerIdEqual = $partnerId;
		$filter->idEqual = $vendorUser->getProcessedName();
		KBatchBase::impersonate($partnerId);
		$kalturaUser = KBatchBase::$kClient->user->listAction($filter, $pager);
		KBatchBase::unimpersonate();
		if (!$kalturaUser->objects)
		{
			$email = $vendorUser->getOriginalName();
			$filterUser = new KalturaUserFilter();
			$filterUser->partnerIdEqual = $partnerId;
			$filterUser->emailStartsWith = $email;
			KBatchBase::impersonate($partnerId);
			$kalturaUser = KBatchBase::$kClient->user->listAction($filterUser, $pager);
			KBatchBase::unimpersonate();
			if (!$kalturaUser->objects || strcasecmp($kalturaUser->objects[0]->email, $email) != 0)
			{
				return null;
			}
		}
		
		if ($kalturaUser->objects)
		{
			return $kalturaUser->objects[0];
		}
		return null;
	}
	
	protected function createNewVendorUser($partnerId, $puserId)
	{
		if (!is_null($puserId))
		{
			$puserId = substr($puserId, 0, self::MAX_PUSER_LENGTH);
		}
		
		$user = new KalturaUser();
		$user->id = $puserId;
		$user->screenName = $puserId;
		$user->firstName = $puserId;
		$user->isAdmin = false;
		$user->type = KalturaUserType::USER;
		KBatchBase::impersonate($partnerId);
		$kalturaUser = KBatchBase::$kClient->user->add($user);
		KBatchBase::unimpersonate();
		return $kalturaUser;
	}
	
	protected function handleParticipants($entry, $userIdsList, $handleParticipantMode)
	{
		if ($handleParticipantMode == kHandleParticipantsMode::IGNORE)
		{
			return $entry;
		}
		
		if ($userIdsList)
		{
			switch ($handleParticipantMode)
			{
				case kHandleParticipantsMode::ADD_AS_CO_PUBLISHERS:
					$entry->entitledUsersPublish = implode(',', array_unique($userIdsList));
					break;
				case kHandleParticipantsMode::ADD_AS_CO_VIEWERS:
					$entry->entitledUsersView = implode(',', array_unique($userIdsList));
					break;
				default:
					break;
			}
		}
		
		return $entry;
	}
	
	protected function createFlavorAssetForEntry($entryId, $partnerId)
	{
		$kFlavorAsset = new KalturaFlavorAsset();
		$kFlavorAsset->tags = self::TAG_SOURCE;
		$kFlavorAsset->flavorParamsId = self::SOURCE_FLAVOR_ID;
		$kFlavorAsset->fileExt = strtolower($this->dropFolderFile->fileExtension);
		KBatchBase::impersonate($partnerId);
		$flavorAsset = KBatchBase::$kClient->flavorAsset->add($entryId, $kFlavorAsset);;
		KBatchBase::unimpersonate();
		return $flavorAsset;
	}
	
	protected function createAssetForTranscript($entryId, $partnerId, $label, $fileType, $transcriptFileExtension, $source)
	{
		$captionAsset = new KalturaCaptionAsset();
		$captionAsset->language = KalturaLanguage::EN;
		$captionAsset->label = $label;
		$transcriptType = $this->getTranscriptType($fileType);
		if ($transcriptType != '')
		{
			$captionAsset->label .= self::LABEL_DEL . $transcriptType;
		}
		$transcriptFormat = CaptionPlugin::getCaptionFormatFromExtension($transcriptFileExtension);
		$captionAsset->format = $transcriptFormat;
		$captionAsset->fileExt = $transcriptFileExtension;
		$captionAsset->source = $source;
		$captionPlugin = KalturaCaptionClientPlugin::get(KBatchBase::$kClient);
		KBatchBase::impersonate($partnerId);
		$newCaptionAsset = $captionPlugin->captionAsset->add($entryId, $captionAsset);
		KBatchBase::unimpersonate();
		return $newCaptionAsset;
	}
	
	protected function getTranscriptType($enumFileType)
	{
		switch($enumFileType)
		{
			case kRecordingFileType::TRANSCRIPT:
				return 'TRANSCRIPT';
			case kRecordingFileType::CC:
				return 'CC';
			default:
				return '';
		}
	}
	
	protected function setContentOnCaptionAsset($captionAsset, $transcript, $partnerId)
	{
		$captionAssetResource = new KalturaStringResource();
		$captionAssetResource->content = $transcript;
		$captionPlugin = KalturaCaptionClientPlugin::get(KBatchBase::$kClient);
		KBatchBase::impersonate($partnerId);
		$captionPlugin->captionAsset->setContent($captionAsset->id, $captionAssetResource);
		KBatchBase::unimpersonate();
	}
	
	protected function createAssetForChats($entryId, $partnerId, $recordingId)
	{
		$attachmentAsset = new KalturaAttachmentAsset();
		$attachmentAsset->filename = "Recording {$recordingId} chat file." . self::CHAT_FILE_TYPE;
		$attachmentAsset->fileExt = self::CHAT_FILE_TYPE;
		$attachmentPlugin = KalturaAttachmentClientPlugin::get(KBatchBase::$kClient);
		KBatchBase::impersonate($partnerId);
		$newAttachmentAsset = $attachmentPlugin->attachmentAsset->add($entryId, $attachmentAsset);
		KBatchBase::unimpersonate();
		return $newAttachmentAsset;
	}
	
	protected function setContentOnAttachmentAsset($attachmentAsset, $meetingChats, $partnerId)
	{
		$attachmentAssetResource = new KalturaStringResource();
		$attachmentAssetResource->content = $meetingChats;
		$attachmentPlugin = KalturaAttachmentClientPlugin::get(KBatchBase::$kClient);
		KBatchBase::impersonate($partnerId);
		$attachmentPlugin->attachmentAsset->setContent($attachmentAsset->id, $attachmentAssetResource);
		KBatchBase::unimpersonate();
	}
}
