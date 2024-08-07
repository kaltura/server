<?php

class kObjectCreatedHandler implements kObjectCreatedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::shouldConsumeCreatedEvent()
	 */
	public function shouldConsumeCreatedEvent(BaseObject $object)
	{
		if($object instanceof Entry)
		{
			if ($object->getIsRecordedEntry() == true)
				return true;
		}

		if($object instanceof KuserKgroup)
		{
				return true;
		}

		if ($object instanceof accessControl)
		{
			return true;
		}



		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::objectCreated()
	 */
	public function objectCreated(BaseObject $object)
	{
		if ($object instanceof KuserKgroup)
		{
			return $this->handleKuserKgroupCreated($object);
		}

		if ($object instanceof entry)
		{
			return $this->handleEntryCreated($object);
		}

		if ($object instanceof accessControl)
		{
			return $this->handleAccessControlCreated($object);
		}

		return true;
	}

	protected function handleKuserKgroupCreated (KuserKgroup $object)
	{
		$kgroup = kuserPeer::retrieveByPK($object->getKgroupId());
		$numberOfUsersPerGroup = $kgroup->getMembersCount();
		$kgroup->setMembersCount($numberOfUsersPerGroup+1);
		$kgroup->save();
		return true;
	}

	protected function handleEntryCreated (entry $object)
	{
		/* @var $object entry */
		$rootEntryId = $object->getRootEntryId();
		if(!$rootEntryId)
			return true;

		$liveEntry = entryPeer::retrieveByPK($rootEntryId);
		if(!$liveEntry)
		{
			KalturaLog::info("Live entry with id [{$object->getRootEntryId()}] not found, categories will not be copied");
			return true;
		}

		/* @var $liveEntry LiveEntry */
		$recordingOptions = $liveEntry->getRecordingOptions();
		if(!$recordingOptions)
			return true;

		/* @var $recordingOptions kLiveEntryRecordingOptions */
		if($recordingOptions->getShouldCopyEntitlement())
		{
			$this->syncEntryEntitlementInfo($object, $liveEntry);
			$this->syncCategoryEntries($object, $liveEntry);
		}
		if ($recordingOptions->getShouldCopyThumbnail())
		{
			$this->syncLiveEntryThumbnail ($object, $liveEntry);
		}

		return true;
	}

	public function syncCategoryEntries(entry $vodEntry, LiveEntry $liveEntry)
	{
		$liveCategoryEntryArray = categoryEntryPeer::selectByEntryId($liveEntry->getId());

		if(!count($liveCategoryEntryArray))
			return;

		foreach($liveCategoryEntryArray as $categoryEntry)
		{
			/* @var $categoryEntry categoryEntry */
			$vodCategoryEntry = $categoryEntry->copy();
			$vodCategoryEntry->setEntryId($vodEntry->getId());
			$vodCategoryEntry->save();
		}
	}

	public function syncEntryEntitlementInfo(entry $vodEntry, LiveEntry $liveEntry)
	{
		$entitledPusersEdit = $liveEntry->getEntitledPusersEdit();
		$entitledPusersPublish = $liveEntry->getEntitledPusersPublish();

		if(!$entitledPusersEdit && !$entitledPusersPublish)
			return;

		if($entitledPusersEdit)
			$vodEntry->setEntitledPusersEdit($entitledPusersEdit);

		if($entitledPusersPublish)
			$vodEntry->setEntitledPusersPublish($entitledPusersPublish);

		$vodEntry->save();
	}

	protected function syncLiveEntryThumbnail (entry $object, LiveEntry $liveEntry)
	{
		//Get live entry thumbnails
		$thumbAssetList = assetPeer::retrieveReadyThumbnailsByEntryId($liveEntry->getId());

		foreach ($thumbAssetList as $thumbAsset)
		{
			/* @var $thumbAsset thumbAsset */
			$newThumbAsset = $thumbAsset->copyToEntry($object->getEntryId(), $liveEntry->getPartnerId());
		}
	}

	protected function handleAccessControlCreated(accessControl $object)
	{
		// if requested set this profile as partners default
		$partner = PartnerPeer::retrieveByPK($object->getPartnerId());
		if ($partner && $object->getIsDefault() === true && $partner->getDefaultAccessControlId() !== $object->getId())
		{
			$partner->setDefaultAccessControlId($object->getId());
			$partner->save();
		}

		return true;
	}
}