<?php

/**
 * @package plugins.reach
 */
class kReachManager implements kObjectChangedEventConsumer, kObjectCreatedEventConsumer, kObjectAddedEventConsumer, kGenericEventConsumer, kObjectReplacedEventConsumer
{
	/**
	 * @var array<booleanNotificationTemplate>
	 */
	protected static $booleanNotificationTemplatesFulfilled;
	protected static $booleanNotificationTemplatesFromReachProfiles;
	protected static $reachProfilesFilteredThatIncludesRegularRules;
	protected static $isInit = false;
	CONST PROFILE_ID = 0;
	CONST CONDITION = 1;
	CONST ACTION = 2;
	CONST EMPTY_STRING = "N/A";

	protected function getObjectType($eventObjectClassName)
	{
		$mapObjectType = array("entry" => objectType::ENTRY,
			"LiveStreamEntry" => objectType::ENTRY,
			"category" => objectType::CATEGORY,
			"asset" => objectType::ASSET,
			"flavorAsset" => objectType::FLAVORASSET,
			"thumbAsset" => objectType::THUMBASSET,
			"uiconf" => objectType::UICONF,
			"conversionProfile2" => objectType::CONVERSIONPROFILE2,
			"kuser" => objectType::KUSER,
			"permission" => objectType::PERMISSION,
			"permissionItem" => objectType::PERMISSIONITEM,
			"userRole" => objectType::USERROLE,
			"categoryEntry" => objectType::CATEGORY_ENTRY,
			"CaptionAsset" => CaptionAssetEventNotificationsPlugin::getEventNotificationEventObjectTypeCoreValue(CaptionAssetEventNotificationEventObjectType::CAPTION_ASSET),
			"TranscriptAsset" => TranscriptAssetEventNotificationsPlugin::getEventNotificationEventObjectTypeCoreValue(TranscriptAssetEventNotificationEventObjectType::TRANSCRIPT_ASSET),
			"LiveStreamScheduleEvent" => ScheduleEventNotificationsPlugin::getEventNotificationEventObjectTypeCoreValue(ScheduleEventNotificationEventObjectType::SCHEDULE_EVENT),
			);

		if (isset($mapObjectType[$eventObjectClassName]))
		{
			return $mapObjectType[$eventObjectClassName];
		}
		return null;
	}

	private function addingEntryVendorTaskByObjectIds($entryId, $allowedCatalogItemIds, $profileId, $object)
	{
		$catalogItemIdsToAdd = array_unique($allowedCatalogItemIds);

		//If both the entry and reach profile don't exist, there's no need to hit the loop
		$entry = entryPeer::retrieveByPK($entryId);
		$entryObjectType = EntryObjectType::ENTRY;
		$reachProfile = ReachProfilePeer::retrieveActiveByPk($profileId);
		if(!$entry || !$reachProfile)
		{
			KalturaLog::log('Not all mandatory objects were found, tasks will not be added');
			return true;
		}

		$reachRestrainAdminTag = kConf::get("reach_restrain_admin_tag", kConfMapNames::RUNTIME_CONFIG, null);
		if(in_array($reachRestrainAdminTag, $entry->getAdminTagsArr()))
		{
			KalturaLog::log("Entry has reach restraining admin tag [$reachRestrainAdminTag], tasks will not be added");
			return true;
		}

		foreach ($catalogItemIdsToAdd as $catalogItemIdToAdd)
		{
			//Validate the existence of the catalog item
			$catalogItemToAdd = VendorCatalogItemPeer::retrieveByPK($catalogItemIdToAdd);
			if(!$catalogItemToAdd)
			{
				KalturaLog::log("Catalog item with ID $catalogItemIdToAdd could not be retrieved, skipping");
				continue;
			}

			$featureType = $catalogItemToAdd->getServiceFeature();
			if(!$catalogItemToAdd->isFeatureTypeSupportedForEntry($entry, $entryObjectType))
			{
				KalturaLog::log("Catalog item with ID $catalogItemIdToAdd with feature type $featureType is not supported for entry Id $entryId");
				continue;
			}

			//Pass the object Id as the context of the task
			$taskJobData = $catalogItemToAdd->getTaskJobData($object);
			self::addEntryVendorTaskByObjectIds($entry, $entryObjectType, $catalogItemToAdd, $reachProfile, $this->getContextByObjectType($object), $taskJobData);
		}
	}

	/* (non-PHPdoc)
 	* @see kGenericEventConsumer::consumeEvent()
 	*/
	public function consumeEvent(KalturaEvent $event)
	{
		$scope = $event->getScope();
		$partnerId = $scope->getPartnerId();
		$object = $scope->getObject();
		$entryId = $object->getEntryId();
		foreach (self::$booleanNotificationTemplatesFulfilled as $booleanNotificationTemplate)
		{
			$profileId = $booleanNotificationTemplate[self::PROFILE_ID];
			$fullFieldCatalogItemIds = $booleanNotificationTemplate[self::ACTION][0]->getCatalogItemIds();
			$fullFieldCatalogItemIdsArr = array_map('trim', explode(',', $fullFieldCatalogItemIds));
			$allowedCatalogItemIds = PartnerCatalogItemPeer::retrieveActiveCatalogItemIds($fullFieldCatalogItemIdsArr, $partnerId);
			if(!count($allowedCatalogItemIds))
			{
				KalturaLog::debug('None of the fulfilled catalog item ids are active on partner, [' . implode(',', $fullFieldCatalogItemIds) . ']');
				continue;
			}
			$this->addingEntryVendorTaskByObjectIds($entryId, $allowedCatalogItemIds, $profileId, $object);
		}
		return true;
	}

	protected static function initReachProfileForPartner($partnerId)
	{
		if (self::$isInit)
		{
			return;
		}

		self::$isInit = true;
		//will hold array of: array(profileId,condition,action) where there are boolean event notification ids.
		self::$booleanNotificationTemplatesFromReachProfiles = array();
		//will hold the reach profiles the includes regular rules (may contain boolean rules as well - when there are mixed rules).
		self::$reachProfilesFilteredThatIncludesRegularRules = array();
		$reachProfiles = ReachProfilePeer::retrieveByPartnerId($partnerId);
		foreach ($reachProfiles as $profile)
		{
			$rules = $profile->getRulesArray();
			foreach ($rules as $rule)
			{
				if (!$rule->getConditions())
				{
					self::$reachProfilesFilteredThatIncludesRegularRules[$profile->getId()] = $profile;
					continue;
				}
				foreach ($rule->getConditions() as $condition)
				{
					if ( $condition->getType()== ConditionType::BOOLEAN && $condition->getbooleanEventNotificationIds() && $condition->getbooleanEventNotificationIds() !== self::EMPTY_STRING)
					{
						self::$booleanNotificationTemplatesFromReachProfiles[] = array($profile->getId(), $condition, $rule->getActions());
					}
					else
					{
						self::$reachProfilesFilteredThatIncludesRegularRules[$profile->getId()] = $profile;
					}
				}
			}
		}
	}

	/* (non-PHPdoc)
 	* @see kGenericEventConsumer::shouldConsumeEvent()
	 * side effect: while checking if the rules are fulfilled, building an array:
 	* $booleanNotificationTemplatesFulfilled - contain array of (profileId, conditions (with boolean event notification that were fulfilled), actions)
 	*/
	public function shouldConsumeEvent(KalturaEvent $event)
	{
		$scope = $event->getScope();
		$partnerId = $scope->getPartnerId();
		self::$booleanNotificationTemplatesFulfilled = array();
		if (!ReachPlugin::isAllowedPartner($partnerId))
		{
			return false;
		}
		$this->buildingReachArrays($event, $partnerId, $scope, true);
		return count(self::$booleanNotificationTemplatesFulfilled);
	}

	protected function buildingReachArrays($event, $partnerId, $scope, $shouldConsumeEventHelper = false)
	{
		if (!$shouldConsumeEventHelper)
		{
			self::$booleanNotificationTemplatesFulfilled = array();
			if (!ReachPlugin::isAllowedPartner($partnerId))
			{
				return;
			}
		}
		$eventType = kEventNotificationFlowManager::getEventType($event);
		$eventObjectClassName = kEventNotificationFlowManager::getEventObjectType($event);
		$objectType = self::getObjectType($eventObjectClassName);
		if ($objectType)
		{
			$this->initReachProfileForPartner($partnerId);
			if (self::$booleanNotificationTemplatesFromReachProfiles)
			{
				foreach (self::$booleanNotificationTemplatesFromReachProfiles as $profileAction)
				{
					$booleanEventNotificationIdArray = explode(',', $profileAction[self::CONDITION]->getbooleanEventNotificationIds());
					$boolEventNotificationObjectList = EventNotificationTemplatePeer::retrieveByEventTypeObjectTypeAndPKS($eventType, $objectType, $partnerId, $booleanEventNotificationIdArray);
					foreach ($boolEventNotificationObjectList as $boolEventNotificationObject)
					{
						$scope->resetDynamicValues();
						$boolEventNotificationObject->applyDynamicValues($scope);
						$fulfilled = $boolEventNotificationObject->fulfilled($scope);
						if ($fulfilled)
						{
							self::$booleanNotificationTemplatesFulfilled[] = array($profileAction[self::PROFILE_ID], $profileAction[self::CONDITION], $profileAction[self::ACTION]);
							break;
						}
					}
				}
			}
		}
	}

	/**
	 * @param BaseObject $object
	 * @param BatchJob $raisedJob
	 * @return bool true if the consumer should handle the event
	 */
	public function shouldConsumeAddedEvent(BaseObject $object)
	{
		if ($object instanceof categoryEntry)
		{
			$event = new kObjectAddedEvent($object);
			$this->buildingReachArrays($event, $event->getScope()->getPartnerId(), $event->getScope(), false);
			return true;
		}

		if ($object instanceof entry && ReachPlugin::isEntryTypeSupportedForReach($object->getType())
				&& $object->getStatus() == entryStatus::READY
				&& $object->getLengthInMsecs())
		{
			return true;
		}
		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::shouldConsumeAddedEvent()
	 */
	public function shouldConsumeCreatedEvent(BaseObject $object)
	{
		if ($object instanceof EntryVendorTask && $object->getStatus() == EntryVendorTaskStatus::PENDING)
			return true;

		if(($object instanceof entry && ReachPlugin::isEntryTypeSupportedForReach($object->getType())) ||
			($object instanceof LiveStreamScheduleEvent && $object->getTemplateEntryId()))
		{
			$event = new kObjectCreatedEvent($object);

			return $this->shouldConsumeEvent($event);
		}

		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	*/
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if ($object instanceof EntryVendorTask
			&& in_array(EntryVendorTaskPeer::STATUS, $modifiedColumns)
			&& $object->getStatus() == EntryVendorTaskStatus::PENDING
			&& $object->getColumnsOldValue(EntryVendorTaskPeer::STATUS) == EntryVendorTaskStatus::PENDING_MODERATION
		)
			return true;

		if ($object instanceof EntryVendorTask
			&& in_array(EntryVendorTaskPeer::STATUS, $modifiedColumns)
			&& in_array($object->getStatus(), array(EntryVendorTaskStatus::ERROR, EntryVendorTaskStatus::READY, EntryVendorTaskStatus::ABORTED))
		)
			return true;

		if($object instanceof entry && ReachPlugin::isEntryTypeSupportedForReach($object->getType()))
		{
			$event = new kObjectChangedEvent($object,$modifiedColumns);
			if ($this->shouldConsumeEvent($event))
				return true;

			if (in_array(entryPeer::LENGTH_IN_MSECS, $modifiedColumns))
			{
				return true;
			}
			if (in_array(entryPeer::STATUS, $modifiedColumns) && in_array($object->getStatus(), array(entryStatus::READY, entryStatus::DELETED)))
			{
				return true;
			}
		}

		if (($object instanceof LiveStreamScheduleEvent && $object->getStatus() != ScheduleEventStatus::DELETED &&  $object->getTemplateEntryId()))
		{
			$event = new kObjectChangedEvent($object,$modifiedColumns);
			if ($this->shouldConsumeEvent($event))
				return true;
		}

		if ($object instanceof flavorAsset
			&& in_array(assetPeer::STATUS, $modifiedColumns)
			&& $object->getStatus() == asset::ASSET_STATUS_READY
			&& myEntryUtils::isEntryReady($object->getEntryId()))
		{
			return true;
		}

		if ($object instanceof categoryEntry && in_array(categoryEntryPeer::STATUS, $modifiedColumns) && $object->getStatus() == CategoryEntryStatus::ACTIVE)
		{
			$event = new kObjectChangedEvent($object,$modifiedColumns);
			$this->buildingReachArrays($event, $event->getScope()->getPartnerId(), $event->getScope(), false);
			return true;
		}

		if ($object instanceof CaptionAsset && in_array(assetPeer::STATUS, $modifiedColumns) && $object->getStatus() == asset::ASSET_STATUS_READY)
		{
			$event = new kObjectChangedEvent($object,$modifiedColumns);
			return $this->shouldConsumeEvent($event);
		}

		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectReplacedEventConsumer::shouldConsumeReplacedEvent()
	*/
	public function shouldConsumeReplacedEvent(BaseObject $object)
	{
		if($object && $object instanceof entry && $object->getSourceType() == EntrySourceType::KALTURA_RECORDED_LIVE)
			return true;
		
		return false;
	}
	
	/**
	 * @param BaseObject $object
	 * @param BatchJob $raisedJob
	 * @return bool true if should continue to the next consumer
	 */
	public function objectAdded(BaseObject $object, BatchJob $raisedJob = null)
	{
		if ($object instanceof categoryEntry && $object->getStatus() == CategoryEntryStatus::ACTIVE)
		{
			if (count(self::$booleanNotificationTemplatesFulfilled))
			{
				$event = new kObjectAddedEvent($object);
				$this->consumeEvent($event);
			}
			$this->checkAutomaticRules($object);
		}

		if ($object instanceof entry && ReachPlugin::isEntryTypeSupportedForReach($object->getType())
				&& $object->getStatus() == entryStatus::READY
				&& $object->getLengthInMsecs())
		{
			$this->checkAutomaticRules($object, true);
		}

		return true;
	}

	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::objectAdded()
	 */
	public function objectCreated(BaseObject $object, BatchJob $raisedJob = null)
	{
		if ($object instanceof EntryVendorTask)
		{
			if ($object->isScheduled())
			{
				$object->addSchedulingData();
			}
			if(!kReachUtils::isPayPerUseTask($object))
			{
				$this->updateReachProfileCreditUsage($object);
			}
		}

		if (($object instanceof entry && ReachPlugin::isEntryTypeSupportedForReach($object->getType())) ||
			($object instanceof LiveStreamScheduleEvent && $object->getTemplateEntryId()))
		{
			$this->initReachProfileForPartner($object->getPartnerId());
			if (count(self::$booleanNotificationTemplatesFulfilled))
			{
				$event = new kObjectCreatedEvent($object);
				$this->consumeEvent($event);
			}
		}

		return true;
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		if ($object instanceof EntryVendorTask && in_array(EntryVendorTaskPeer::STATUS, $modifiedColumns))
		{
			if($object->getStatus() == EntryVendorTaskStatus::PENDING
				&& $object->getColumnsOldValue(EntryVendorTaskPeer::STATUS) == EntryVendorTaskStatus::PENDING_MODERATION)
			{
				if ($object->isScheduled())
				{
					$object->addSchedulingData();
				}
				if(!kReachUtils::isPayPerUseTask($object))
				{
					$this->updateReachProfileCreditUsage($object);
				}
			}
			if($object->getStatus() == EntryVendorTaskStatus::READY && kReachUtils::isPayPerUseTask($object))
			{
				$this->updateReachProfileCreditUsage($object);
			}
		}

		if ($object instanceof EntryVendorTask
			&& in_array(EntryVendorTaskPeer::STATUS, $modifiedColumns)
			&& $object->getStatus() == EntryVendorTaskStatus::ERROR
			&& in_array($object->getColumnsOldValue(EntryVendorTaskPeer::STATUS), array(EntryVendorTaskStatus::PENDING, EntryVendorTaskStatus::PROCESSING, EntryVendorTaskStatus::SCHEDULED))
		)
		{
			return $this->handleErrorTask($object);
		}

		if ($object instanceof EntryVendorTask
			&& in_array(EntryVendorTaskPeer::STATUS, $modifiedColumns)
			&& $object->getStatus() == EntryVendorTaskStatus::ABORTED
			&& in_array($object->getColumnsOldValue(EntryVendorTaskPeer::STATUS), array(EntryVendorTaskStatus::PENDING, EntryVendorTaskStatus::SCHEDULED))
		)
		{
			return $this->handleAbortTask($object);
		}

		if ($object instanceof EntryVendorTask
			&& in_array(EntryVendorTaskPeer::STATUS, $modifiedColumns)
			&& $object->getStatus() == EntryVendorTaskStatus::READY
		)
		{
			$this->addLabelAddition($object);
			return $this->invalidateAccessKey($object);
		}

		if ($object instanceof entry && ReachPlugin::isEntryTypeSupportedForReach($object->getType()))
		{
			$this->initReachProfileForPartner($object->getPartnerId());
			if (count(self::$booleanNotificationTemplatesFulfilled))
			{
				$event = new kObjectChangedEvent($object,$modifiedColumns);
				$this->consumeEvent($event);
			}
			if (in_array(entryPeer::LENGTH_IN_MSECS, $modifiedColumns))
			{
				$this->handleEntryDurationChanged($object);
			}
			// Checking if entry duration was modified to handle YouTube entries duration set
			if (in_array(entryPeer::STATUS, $modifiedColumns)
				|| (in_array(entryPeer::LENGTH_IN_MSECS, $modifiedColumns)
					&& $object->getColumnsOldValue(entryPeer::LENGTH_IN_MSECS) === 0))
			{
				if ($object->getStatus() == entryStatus::READY && !$object->getBlockAutoTranscript())
				{
					return $this->handleEntryReady($object);
				}
				if(in_array($object->getStatus(), array(entryStatus::DELETED, entryStatus::ERROR_CONVERTING, entryStatus::ERROR_CONVERTING)))
				{
					return $this->abortTasks($object);
				}
			}
		}

		if ($object instanceof flavorAsset && !$object->getentry()->getBlockAutoTranscript())
		{
			return $this->handleEntryReady($object->getentry());
		}

		if ($object instanceof categoryEntry && in_array(categoryEntryPeer::STATUS, $modifiedColumns) && $object->getStatus() == CategoryEntryStatus::ACTIVE)
		{
			if (count(self::$booleanNotificationTemplatesFulfilled))
			{
				$event = new kObjectChangedEvent($object,$modifiedColumns);
				$this->consumeEvent($event);
			}
			return $this->checkAutomaticRules($object);
		}

		if ($object instanceof CaptionAsset && in_array(assetPeer::STATUS, $modifiedColumns) && $object->getStatus() == asset::ASSET_STATUS_READY)
		{
			$event = new kObjectChangedEvent($object,$modifiedColumns);
			$this->consumeEvent($event);
		}

		if (($object instanceof LiveStreamScheduleEvent && $object->getStatus() != ScheduleEventStatus::DELETED &&  $object->getTemplateEntryId()))
		{
			$event = new kObjectChangedEvent($object,$modifiedColumns);
			$this->consumeEvent($event);
		}

		return true;
	}
	
	/* (non-PHPdoc)
 	* @see kObjectReplacedEventConsumer::shouldConsumeReplacedEvent()
	*/
	public function objectReplaced(BaseObject $object, BaseObject $replacingObject, BatchJob $raisedJob = null)
	{
		$this->handleEntryDurationChanged($object);
		return $this->checkPendingEntryTasks($object);
	}

	private function handleEntryReady(entry $object)
	{
		$this->checkAutomaticRules($object, true);
		
		if($object->getSourceType() != EntrySourceType::KALTURA_RECORDED_LIVE)
			return $this->checkPendingEntryTasks($object);
		
		return true;
	}

	protected function checkPendingEntryTasks($object)
	{
		//Check if there are any tasks that were created with pending entry ready status
		$pendingEntryReadyTasks = EntryVendorTaskPeer::retrieveByEntryIdAndStatuses($object->getId(), $object->getPartnerId(), array(EntryVendorTaskStatus::PENDING_ENTRY_READY));
		foreach ($pendingEntryReadyTasks as $pendingEntryReadyTask)
		{
			/* @var $pendingEntryReadyTask EntryVendorTask */
			$newStatus = $pendingEntryReadyTask->getIsRequestModerated() ? EntryVendorTaskStatus::PENDING_MODERATION : EntryVendorTaskStatus::PENDING;
			$pendingEntryReadyTask->setStatus($newStatus);
			$dbVendorCatalogItem = VendorCatalogItemPeer::retrieveByPK($pendingEntryReadyTask->getCatalogItemId());
			if (!$dbVendorCatalogItem)
			{
				KalturaLog::log("Catalog item [" . $pendingEntryReadyTask->getCatalogItemId() . " not found] ");
				continue;
			}
			$pendingEntryReadyTask->setAccessKey($dbVendorCatalogItem->generateReachVendorKs($pendingEntryReadyTask->getEntryId(), $pendingEntryReadyTask->getIsOutputModerated(), $pendingEntryReadyTask->getAccessKeyExpiry()));
			if(!$dbVendorCatalogItem->getPayPerUse() && $pendingEntryReadyTask->getPrice() == 0)
			{
				$vendorCatalogItem = $pendingEntryReadyTask->getCatalogItem();
				$taskPrice = $vendorCatalogItem->calculateTaskPrice($object, $pendingEntryReadyTask->getEntryObjectType(), $pendingEntryReadyTask->getTaskJobData());
				$pendingEntryReadyTask->setPrice($taskPrice);
			}
			$pendingEntryReadyTask->save();
		}
		return true;
	}
	
	private function updateReachProfileCreditUsage(EntryVendorTask $entryVendorTask)
	{
		ReachProfilePeer::updateUsedCredit($entryVendorTask->getReachProfileId(), $entryVendorTask->getPrice());
	}

	private function handleErrorTask(EntryVendorTask $entryVendorTask)
	{
		if ($entryVendorTask->isScheduled())
		{
			$entryVendorTask->removeSchedulingData();
		}

		//Refund credit for tasks which could not be handled by the service provider
		kReachUtils::refundTask($entryVendorTask);
	}

	private function handleAbortTask(EntryVendorTask $entryVendorTask)
	{
		if ($entryVendorTask->isScheduled())
		{
			$entryVendorTask->removeSchedulingData();

			// Prevent refund if task is aborted too late
			/* @var $catalogItem IVendorScheduledCatalogItem */
			$catalogItem = VendorCatalogItemPeer::retrieveByPK($entryVendorTask->getCatalogItemId());
			if($entryVendorTask->getTaskJobData()->getStartDate() - time() < $catalogItem->getMinimalRefundTime())
			{
				return;
			}
			
			kReachUtils::refundTask($entryVendorTask);
		}
	}

	protected function getLabelAdditionByType(ReachProfile $reachProfile, $serviceType)
	{
		switch ($serviceType)
		{
			case VendorServiceType::HUMAN:
				return $reachProfile->getLabelAdditionForHumanServiceType();

			case VendorServiceType::MACHINE:
				return $reachProfile->getLabelAdditionForMachineServiceType();
		}
		return null;
	}

	protected function addLabelAddition(EntryVendorTask $entryVendorTask)
	{
		do
		{
			// Relevant only for Captions service
			if($entryVendorTask->getServiceFeature() != VendorServiceFeature::CAPTIONS)
			{
				break;
			}

			$captionAssetId = $entryVendorTask->getOutputObjectId();
			if(!$captionAssetId)
			{
				break;
			}

			$reachProfile = $entryVendorTask->getReachProfile();
			if(!$reachProfile)
			{
				break;
			}

			$labelAddition = $this->getLabelAdditionByType($reachProfile, $entryVendorTask->getServiceType());
			if(empty($labelAddition))
			{
				break;
			}

			$dbCaptionAsset = assetPeer::retrieveById($captionAssetId);
			if (!$dbCaptionAsset || !($dbCaptionAsset instanceof CaptionAsset))
			{
				break;
			}

			$newLabel = "{$dbCaptionAsset->getLabel()} $labelAddition";
			KalturaLog::debug("New label [{$newLabel}] for CaptionAsset ID [{$captionAssetId}]");

			$dbCaptionAsset->setLabel($newLabel);
			$dbCaptionAsset->save();

		}while(0);
	}

	private function invalidateAccessKey(EntryVendorTask $entryVendorTask)
	{
		$ksString = $entryVendorTask->getAccessKey();
		
		try
		{
			$ksObj = kSessionUtils::crackKs($ksString);
		}
		catch(Exception $ex)
		{
			KalturaLog::debug('Failed to crack KS with error message [' . $ex->getMessage() . '], accessKey will not be invalidated');
		}
		
		$ksObj->kill();
	}

	private function handleEntryDurationChanged(entry $entry)
	{
		$pendingEntryVendorTasks = EntryVendorTaskPeer::retrievePendingByEntryId($entry->getId(), $entry->getPartnerId());
		$addedCostByProfileId = array();
		foreach ($pendingEntryVendorTasks as $pendingEntryVendorTask)
		{
			/* @var $pendingEntryVendorTask EntryVendorTask */
			if(kReachUtils::isPayPerUseTask($pendingEntryVendorTask))
			{
				continue;
			}
			$oldPrice = $pendingEntryVendorTask->getPrice();
			$vendorCatalogItem = $pendingEntryVendorTask->getCatalogItem();
			$newPrice = $vendorCatalogItem->calculateTaskPrice($entry, $pendingEntryVendorTask->getEntryObjectType(), $pendingEntryVendorTask->getTaskJobData());
			$priceDiff = $newPrice - $oldPrice;
			
			if(!$priceDiff)
			{
				continue;
			}

			$pendingEntryVendorTask->setPrice($newPrice);
			if (!isset($addedCostByProfileId[$pendingEntryVendorTask->getReachProfileId()]))
			{
				$addedCostByProfileId[$pendingEntryVendorTask->getReachProfileId()] = 0;
			}

			if (kReachUtils::checkPriceAddon($pendingEntryVendorTask, $priceDiff))
			{
				$pendingEntryVendorTask->save();
				if($pendingEntryVendorTask->getStatus() != EntryVendorTaskStatus::PENDING_MODERATION)
				{
					$addedCostByProfileId[$pendingEntryVendorTask->getReachProfileId()] += $priceDiff;
				}
			}
			else
			{
				$pendingEntryVendorTask->setStatus(EntryVendorTaskStatus::ABORTED);
				$pendingEntryVendorTask->setPrice($newPrice);
				$pendingEntryVendorTask->setErrDescription("Current task price exceeded credit allowed, task was aborted");
				$pendingEntryVendorTask->save();
				$addedCostByProfileId[$pendingEntryVendorTask->getReachProfileId()] -= $oldPrice;
			}
		}

		foreach ($addedCostByProfileId as $reachProfileId => $addedCost)
		{
			if(!$addedCost)
			{
				continue;
			}
			ReachProfilePeer::updateUsedCredit($reachProfileId, $addedCost);
		}

		return true;
	}

	public static function addEntryVendorTaskByObjectIds($entryObject, $entryObjectType, VendorCatalogItem $vendorCatalogItem, ReachProfile $reachProfile, $context = null, $taskJobData = null)
	{
		$entryId = $entryObject->getId();
		$partnerId = $entryObject->getPartnerId();
		$vendorCatalogItemId = $vendorCatalogItem->getId();

		$targetVersion = $vendorCatalogItem->getTaskVersion($entryId, $entryObjectType);
		if ($vendorCatalogItem->isDuplicateTask($entryId, $entryObjectType, $partnerId))
		{
			KalturaLog::log("Trying to insert a duplicate entry vendor task for entry [$entryId], catalog item [$vendorCatalogItemId] and entry version [$targetVersion]");
			return true;
		}
		else
		{
			kReachUtils::tryToCancelOldTasks($entryId, $vendorCatalogItemId, $partnerId);
		}

		$unitsUsed = null;
		if($vendorCatalogItem->requiresPayment())
		{
			if(kReachUtils::hasCreditExpired($reachProfile))
			{
				KalturaLog::log("Credit cycle has expired, Task could not be added for entry [$entryId] and catalog item [$vendorCatalogItemId]");
				return true;
			}

			$unitsUsed = kReachUtils::getPricingUnits($vendorCatalogItem, $entryObject, $entryObjectType, $taskJobData, $unitsUsed);
			if (!kReachUtils::isEnoughCreditLeft($entryObject, $entryObjectType, $vendorCatalogItem, $reachProfile, $unitsUsed))
			{
				KalturaLog::log("Exceeded max credit allowed, Task could not be added for entry [$entryId] and catalog item [$vendorCatalogItemId]");
				return true;
			}
		}


		if(!self::shouldAddEntryVendorTaskByObject($entryObject, $entryObjectType, $vendorCatalogItem, $reachProfile))
		{
			return true;
		}

		$entryVendorTask = self::addEntryVendorTask($entryObject, $entryObjectType, $reachProfile, $vendorCatalogItem, false, $targetVersion, $context, EntryVendorTaskCreationMode::AUTOMATIC, $unitsUsed);
		if($entryVendorTask)
		{
			if ($taskJobData)
			{
				$entryVendorTask->setTaskJobData($taskJobData);
			}
			$entryVendorTask->save();
		}
		return $entryVendorTask;
	}

	public static function shouldAddEntryVendorTaskByObject($entryObject, $entryObjectType, $vendorCatalogItem, $reachProfile)
	{
		if(!$entryObjectType)
		{
			$entryObjectType = EntryObjectType::ENTRY;
		}

		switch($entryObjectType)
		{
			case EntryObjectType::ENTRY:
				if (!$vendorCatalogItem->isEntryTypeSupported($entryObject->getType(), $entryObject->getMediaType()))
				{
					KalturaLog::log("Entry of type [{$entryObject->getType()}] is not supported by Reach");
					return false;
				}

				if (!kReachUtils::areFlavorsReady($entryObject, $reachProfile))
				{
					KalturaLog::log("Not all flavor params IDs [{$reachProfile->getFlavorParamsIds()}] are ready yet");
					return false;
				}

				if($entryObject->getParentEntryId())
				{
					KalturaLog::log("Entry [{$entryObject->getId()}] is a child entry, entry vendor task object wont be created for it");
					return false;
				}

				if ($vendorCatalogItem->isEntryDurationExceeding($entryObject))
				{
					KalturaLog::log("Entry [{$entryObject->getId()}] is exceeding the catalogItem's limit, entry vendor task object wont be created for it");
					return false;
				}
				return true;

			default:
				return false;
		}

	}

	public static function addEntryVendorTask($entryObject, $entryObjectType, ReachProfile $reachProfile, VendorCatalogItem $vendorCatalogItem, $validateModeration = true, $version = 0, $context = null, $creationMode = EntryVendorTaskCreationMode::MANUAL, $unitsUsed = null)
	{
		if(!kReachManager::shouldAddEntryVendorTask($entryObject, $entryObjectType, $vendorCatalogItem))
		{
			return null;
		}
		
		//Create new entry vendor task object
		$entryVendorTask = new EntryVendorTask();

		//Assign default parameters
		$entryVendorTask->setEntryId($entryObject->getId());
		$entryVendorTask->setCatalogItemId($vendorCatalogItem->getId());
		$entryVendorTask->setReachProfileId($reachProfile->getId());
		$entryVendorTask->setPartnerId($reachProfile->getPartnerId());
		$entryVendorTask->setKuserId(self::getTaskKuserId($entryObject, $entryObjectType));
		$entryVendorTask->setUserId(self::getTaskPuserId($entryObject, $entryObjectType));
		$entryVendorTask->setVendorPartnerId($vendorCatalogItem->getVendorPartnerId());
		$entryVendorTask->setVersion($version);
		$entryVendorTask->setEntryObjectType($entryObjectType);
		$entryVendorTask->setQueueTime(null);
		$entryVendorTask->setFinishTime(null);

		//Set calculated values
		$shouldModerateOutput = !$reachProfile->shouldModerateOutputCaptions($vendorCatalogItem->getServiceType());
		$accessKeyExpiry = $vendorCatalogItem->getKsExpiry();
		$entryVendorTask->setIsOutputModerated($shouldModerateOutput);
		$entryVendorTask->setAccessKeyExpiry($accessKeyExpiry);
		$entryVendorTask->setAccessKey($vendorCatalogItem->generateReachVendorKs($entryVendorTask->getEntryId(), $shouldModerateOutput, $accessKeyExpiry));
		$entryVendorTask->setServiceType($vendorCatalogItem->getServiceType());
		$entryVendorTask->setServiceFeature($vendorCatalogItem->getServiceFeature());
		$entryVendorTask->setTurnAroundTime($vendorCatalogItem->getTurnAroundTime());

		if(!$vendorCatalogItem->getPayPerUse())
		{
			$taskPrice = $vendorCatalogItem->calculateTaskPrice($entryObject, $entryObjectType, null, $unitsUsed);
			$entryVendorTask->setPrice($taskPrice);
		}

		if($unitsUsed !== null)
		{
			$entryVendorTask->setUnitsUsed($unitsUsed);
		}

		if ($context)
		{
			$entryVendorTask->setContext($context);
		}

		if ($creationMode)
		{
			$entryVendorTask->setCreationMode($creationMode);
		}

		if ($validateModeration && $reachProfile->shouldModerate($vendorCatalogItem->getServiceType()))
		{
			$entryVendorTask->setIsRequestModerated(true);
		}

		$status = self::getEntryVendorTaskStatus($reachProfile, $vendorCatalogItem, $entryObject, $entryObjectType, $validateModeration);
		$entryVendorTask->setStatus($status);

		$dictionary = $reachProfile->getDictionaryByLanguage($vendorCatalogItem->getSourceLanguage());
		if ($dictionary)
		{
			$entryVendorTask->setDictionary($dictionary->getData());
		}

		return $entryVendorTask;
	}

	protected static function shouldAddEntryVendorTask($entryObject, $entryObjectType, $vendorCatalogItem)
	{
		if(!$entryObjectType)
		{
			$entryObjectType = EntryObjectType::ENTRY;
		}

		switch ($entryObjectType)
		{
			case EntryObjectType::ENTRY:
				/** @var $entryObject entry */
				//Check if the entry is temporary, if so, dont create the task
				if($entryObject->getIsTemporary())
				{
					KalturaLog::debug("Entry [{$entryObject->getId()}] is temporary, entry vendor task object wont be created for it");
					return false;
				}

				//Check if static content and the catalog item is excluding static content, if so, dont create the task
				if(count($vendorCatalogItem->getAdminTagsToExcludeArray()) && array_intersect($vendorCatalogItem->getAdminTagsToExcludeArray(), $entryObject->getAdminTagsArr()))
				{
					KalturaLog::debug("Entry [{$entryObject->getId()}] has admin tags that are excluded by the catalog item, entry vendor task object wont be created for it");
					return false;
				}
				return true;

			default:
				return false;
		}
	}

	protected static function getEntryVendorTaskStatus($reachProfile, $vendorCatalogItem, $entry, $entryObjectType, $validateModeration)
	{
		$status = EntryVendorTaskStatus::PENDING;

		if ($validateModeration && $reachProfile->shouldModerate($vendorCatalogItem->getServiceType()))
		{
			$status = EntryVendorTaskStatus::PENDING_MODERATION;
		}

		if(!$entryObjectType || $entryObjectType == KalturaEntryObjectType::ENTRY)
		{
			if($vendorCatalogItem->requiresEntryReady() && $entry->getStatus() != entryStatus::READY)
			{
				$status = EntryVendorTaskStatus::PENDING_ENTRY_READY;
			}

			//Kaltura Recorded entries are ready on creation so make sure the vendors wont fetch the job until it gets its assets
			if($entry->getSourceType() == EntrySourceType::KALTURA_RECORDED_LIVE && $vendorCatalogItem->requiresEntryReady())
			{
				$entryAssets = assetPeer::retrieveReadyFlavorsByEntryId($entry->getId());
				if(!count($entryAssets))
				{
					$status = EntryVendorTaskStatus::PENDING_ENTRY_READY;
				}
			}
		}

		return $status;
	}
	
	protected static function getTaskKuserId($entryObject, $entryObjectType)
	{
		$kuserId = kCurrentContext::getCurrentKsKuserId();
		if(kCurrentContext::$ks_partner_id <= PartnerPeer::GLOBAL_PARTNER)
		{
			if(!$entryObjectType)
			{
				$entryObjectType = EntryObjectType::ENTRY;
			}

			switch ($entryObjectType)
			{
				//For automatic dispatched tasks make sure to set the entry creator user as the entry owner
				case EntryObjectType::ENTRY:
					return $entryObject->getKuserId();

				default:
					return null;
			}
		}
		return $kuserId;
	}
	
	protected static function getTaskPuserId($entryObject, $entryObjectType)
	{
		$puserId = kCurrentContext::$ks_uid;
		if(kCurrentContext::$ks_partner_id <= PartnerPeer::GLOBAL_PARTNER)
		{
			if(!$entryObjectType)
			{
				$entryObjectType = EntryObjectType::ENTRY;
			}

			switch ($entryObjectType)
			{
				//For automatic dispatched tasks make sure to set the entry creator user as the entry owner
				case EntryObjectType::ENTRY:
					return $entryObject->getPuserId();

				default:
					return null;
			}
		}
		
		return $puserId;
	}

	private function checkAutomaticRules($object, $checkEmptyRulesOnly = false)
	{
		$scope = new kScope();
		$entryId = $object->getEntryId();
		$scope->setEntryId($entryId);
		$this->initReachProfileForPartner($object->getPartnerId());
		if (self::$reachProfilesFilteredThatIncludesRegularRules)
		{
			foreach (self::$reachProfilesFilteredThatIncludesRegularRules as $profile)
			{
				/* @var $profile ReachProfile */
				$fullFieldCatalogItemIds = $profile->fulfillsRules($scope, $checkEmptyRulesOnly);
				if (!count($fullFieldCatalogItemIds))
				{
					continue;
				}
				$allowedCatalogItemIds = PartnerCatalogItemPeer::retrieveActiveCatalogItemIds($fullFieldCatalogItemIds, $object->getPartnerId());
				if(!count($allowedCatalogItemIds))
				{
					KalturaLog::debug("None of the fulfilled catalog item ids are active on partner, [" . implode(",", $fullFieldCatalogItemIds) . "]");
					continue;
				}
				$this->addingEntryVendorTaskByObjectIds($entryId, $allowedCatalogItemIds, $profile->getId(), $object);
			}
		}
		return true;
	}

	private function abortTasks(entry $entry)
	{
		//Delete all pending tasks
		$entryStatusErrorMessage = $this->getAbortEntryStatusMessage($entry->getStatus());
		$pendingModerationTasks = EntryVendorTaskPeer::retrievePendingByEntryId($entry->getId(), $entry->getPartnerId());
		foreach ($pendingModerationTasks as $pendingModerationTask)
		{
			/* @var $pendingModerationTask EntryVendorTask */
			$pendingModerationTask->setStatus(EntryVendorTaskStatus::ABORTED);
			$pendingModerationTask->setErrDescription("Task was aborted by server, associated entry [{$entry->getId()}] $entryStatusErrorMessage");
			$pendingModerationTask->save();
		}
	}

	private function getAbortEntryStatusMessage($status)
	{
		switch ($status)
		{
			case entryStatus::DELETED:
				return "deleted";
			case entryStatus::ERROR_CONVERTING:
				return "error occurred while converting";
			case entryStatus::ERROR_IMPORTING:
				return "error occurred while importing";
			default:
				return "invalid status provided";
		}
	}

	private function getContextByObjectType($object)
	{
		if ($object instanceof categoryEntry)
			return $object->getCategoryId();

		return null;
	}
}
