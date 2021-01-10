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
			"CaptionAsset" => CaptionAssetEventNotificationsPlugin::getEventNotificationEventObjectTypeCoreValue('CaptionAsset'),);

		if (isset($mapObjectType[$eventObjectClassName]))
		{
			return $mapObjectType[$eventObjectClassName];
		}
		return null;
	}

	private function addingEntryVendorTaskByObjectIds($entryId, $allowedCatalogItemIds, $profileId, $object)
	{
		$existingCatalogItemIds = EntryVendorTaskPeer::retrieveExistingTasksCatalogItemIds($entryId, $allowedCatalogItemIds);
		$catalogItemIdsToAdd = array_unique(array_diff($allowedCatalogItemIds, $existingCatalogItemIds));
		$taskJobData = self::getTaskJobData($object);
		foreach ($catalogItemIdsToAdd as $catalogItemIdToAdd)
		{
			//Pass the object Id as the context of the task
			self::addEntryVendorTaskByObjectIds($entryId, $catalogItemIdToAdd, $profileId, $this->getContextByObjectType($object), $taskJobData);
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
				KalturaLog::debug("None of the fullfield catalog item ids are active on partner, [" . implode(",", $fullFieldCatalogItemIds) . "]");
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
		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::shouldConsumeAddedEvent()
	 */
	public function shouldConsumeCreatedEvent(BaseObject $object)
	{
		if ($object instanceof EntryVendorTask && $object->getStatus() == EntryVendorTaskStatus::PENDING)
			return true;

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
			&& in_array($object->getStatus(), array(EntryVendorTaskStatus::ERROR, EntryVendorTaskStatus::READY))
		)
			return true;

		if($object instanceof entry && $object->getType() == entryType::MEDIA_CLIP)
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

		return true;
	}

	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::objectAdded()
	 */
	public function objectCreated(BaseObject $object, BatchJob $raisedJob = null)
	{
		$this->updateReachProfileCreditUsage($object);
		return true;
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		if ($object instanceof EntryVendorTask && in_array(EntryVendorTaskPeer::STATUS, $modifiedColumns)
			&& $object->getStatus() == EntryVendorTaskStatus::PENDING
			&& $object->getColumnsOldValue(EntryVendorTaskPeer::STATUS) == EntryVendorTaskStatus::PENDING_MODERATION
		)
			return $this->updateReachProfileCreditUsage($object);

		if ($object instanceof EntryVendorTask
			&& in_array(EntryVendorTaskPeer::STATUS, $modifiedColumns)
			&& $object->getStatus() == EntryVendorTaskStatus::ERROR
			&& in_array($object->getColumnsOldValue(EntryVendorTaskPeer::STATUS), array(EntryVendorTaskStatus::PENDING, EntryVendorTaskStatus::PROCESSING))
		)
			return $this->handleErrorTask($object);

		if ($object instanceof EntryVendorTask
			&& in_array(EntryVendorTaskPeer::STATUS, $modifiedColumns)
			&& $object->getStatus() == EntryVendorTaskStatus::READY
		)
		{
			$this->addLabelAddition($object);
			return $this->invalidateAccessKey($object);
		}

		if ($object instanceof entry && $object->getType() == entryType::MEDIA_CLIP)
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
			if (in_array(entryPeer::STATUS, $modifiedColumns))
			{
				if ($object->getStatus() == entryStatus::READY)
				{
					return $this->handleEntryReady($object);
				}
				if(in_array($object->getStatus(), array(entryStatus::DELETED, entryStatus::ERROR_CONVERTING, entryStatus::ERROR_CONVERTING)))
				{
					return $this->abortTasks($object);
				}
			}
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
			$pendingEntryReadyTask->setAccessKey(kReachUtils::generateReachVendorKs($pendingEntryReadyTask->getEntryId(), $pendingEntryReadyTask->getIsOutputModerated(), $pendingEntryReadyTask->getAccessKeyExpiry()));
			if($pendingEntryReadyTask->getPrice() == 0)
				$pendingEntryReadyTask->setPrice(kReachUtils::calculateTaskPrice($object, $pendingEntryReadyTask->getCatalogItem()));
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
		//Refund credit for tasks which could not be handled by the service provider
		ReachProfilePeer::updateUsedCredit($entryVendorTask->getReachProfileId(), -$entryVendorTask->getPrice());
		
		//Rest task price so that reports will be alligned with the total used credit
		$entryVendorTask->setOldPrice($entryVendorTask->getPrice());
		$entryVendorTask->setPrice(0);
		$entryVendorTask->save();
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

			$newLabel = "{$dbCaptionAsset->getLabel()} ($labelAddition)";
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
			KalturaLog::debug("Failed to crackKs with error message [" . $ex->getMessage() . "], accessKey won't be invalidated");
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
			$oldPrice = $pendingEntryVendorTask->getPrice();
			$newPrice = kReachUtils::calculateTaskPrice($entry, $pendingEntryVendorTask->getCatalogItem());
			$priceDiff = $newPrice - $oldPrice;
			
			if(!$priceDiff)
				continue;
			
			$pendingEntryVendorTask->setPrice($newPrice);
			if (!isset($addedCostByProfileId[$pendingEntryVendorTask->getReachProfileId()]))
				$addedCostByProfileId[$pendingEntryVendorTask->getReachProfileId()] = 0;

			if (kReachUtils::checkPriceAddon($pendingEntryVendorTask, $priceDiff))
			{
				$pendingEntryVendorTask->save();
				if($pendingEntryVendorTask->getStatus() != EntryVendorTaskStatus::PENDING_MODERATION)
					$addedCostByProfileId[$pendingEntryVendorTask->getReachProfileId()] += $priceDiff;
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
				continue;
			
			ReachProfilePeer::updateUsedCredit($reachProfileId, $addedCost);
		}

		return true;
	}

	public static function addEntryVendorTaskByObjectIds($entryId, $vendorCatalogItemId, $reachProfileId, $context = null, $taskJobData = null)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		$reachProfile = ReachProfilePeer::retrieveActiveByPk($reachProfileId);
		$vendorCatalogItem = VendorCatalogItemPeer::retrieveByPK($vendorCatalogItemId);
		
		if(!$entry || !$reachProfile || !$vendorCatalogItem)
		{
			KalturaLog::log("Not all mandatory objects were found, task will not be added");
			return true;
		}

		$sourceFlavor = assetPeer::retrieveOriginalByEntryId($entry->getId());
		$sourceFlavorVersion = $sourceFlavor != null ? $sourceFlavor->getVersion() : 0;

		if (kReachUtils::isDuplicateTask($entryId, $vendorCatalogItemId, $entry->getPartnerId(), $sourceFlavorVersion, false))
		{
			KalturaLog::log("Trying to insert a duplicate entry vendor task for entry [$entryId], catalog item [$vendorCatalogItemId] and entry version [$sourceFlavorVersion]");
			return true;
		}

		//check if credit has expired
		if (kReachUtils::hasCreditExpired($reachProfile) && $vendorCatalogItem->getPricing() && $vendorCatalogItem->getPricing()->getPricePerUnit())
		{
			KalturaLog::log("Credit cycle has expired, Task could not be added for entry [$entryId] and catalog item [$vendorCatalogItemId]");
			return true;
		}

		if (!kReachUtils::isEnoughCreditLeft($entry, $vendorCatalogItem, $reachProfile))
		{
			KalturaLog::log("Exceeded max credit allowed, Task could not be added for entry [$entryId] and catalog item [$vendorCatalogItemId]");
			return true;
		}
		
		if(!kReachUtils::isEntryTypeSupported($entry->getType(), $entry->getMediaType()))
		{
			KalturaLog::log("Entry of type [{$entry->getType()}] is not supported by Reach");
			return true;
		}

		if($entry->getParentEntryId())
		{
			KalturaLog::log("Entry [{$entry->getId()}] is a child entry, entry vendor task object wont be created for it");
			return true;
		}

		$entryVendorTask = self::addEntryVendorTask($entry, $reachProfile, $vendorCatalogItem, false, $sourceFlavorVersion, $context, EntryVendorTaskCreationMode::AUTOMATIC);
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

	public static function addEntryVendorTask(entry $entry, ReachProfile $reachProfile, VendorCatalogItem $vendorCatalogItem, $validateModeration = true, $version = 0, $context = null, $creationMode = EntryVendorTaskCreationMode::MANUAL)
	{
		if($entry->getIsTemporary())
		{
			KalturaLog::debug("Entry [{$entry->getId()}] is temporary, entry vendor task object wont be created for it");
			return null;
		}
		
		//Create new entry vendor task object
		$entryVendorTask = new EntryVendorTask();

		//Assign default parameters
		$entryVendorTask->setEntryId($entry->getId());
		$entryVendorTask->setCatalogItemId($vendorCatalogItem->getId());
		$entryVendorTask->setReachProfileId($reachProfile->getId());
		$entryVendorTask->setPartnerId($entry->getPartnerId());
		$entryVendorTask->setKuserId(self::getTaskKuserId($entry));
		$entryVendorTask->setUserId(self::getTaskPuserId($entry));
		$entryVendorTask->setVendorPartnerId($vendorCatalogItem->getVendorPartnerId());
		$entryVendorTask->setVersion($version);
		$entryVendorTask->setQueueTime(null);
		$entryVendorTask->setFinishTime(null);

		//Set calculated values
		$shouldModerateOutput = !$reachProfile->shouldModerateOutputCaptions($vendorCatalogItem->getServiceType());
		$accessKeyExpiry = $vendorCatalogItem->getKsExpiry();
		$entryVendorTask->setIsOutputModerated($shouldModerateOutput);
		$entryVendorTask->setAccessKeyExpiry($accessKeyExpiry);
		$entryVendorTask->setAccessKey(kReachUtils::generateReachVendorKs($entryVendorTask->getEntryId(), $shouldModerateOutput, $accessKeyExpiry));
		$entryVendorTask->setPrice(kReachUtils::calculateTaskPrice($entry, $vendorCatalogItem));
		$entryVendorTask->setServiceType($vendorCatalogItem->getServiceType());
		$entryVendorTask->setServiceFeature($vendorCatalogItem->getServiceFeature());
		$entryVendorTask->setTurnAroundTime($vendorCatalogItem->getTurnAroundTime());

		if ($context)
			$entryVendorTask->setContext($context);

		if ($creationMode)
			$entryVendorTask->setCreationMode($creationMode);

		$status = EntryVendorTaskStatus::PENDING;
		if ($validateModeration && $reachProfile->shouldModerate($vendorCatalogItem->getServiceType()))
		{
			$entryVendorTask->setIsRequestModerated(true);
			$status = EntryVendorTaskStatus::PENDING_MODERATION;
		}
		
		if($entry->getStatus() != entryStatus::READY)
		{
			$status = EntryVendorTaskStatus::PENDING_ENTRY_READY;
		}
		
		//KalturaRecorded entries are ready on creation so make sure the vendors wont fetch the job until it receive its assets
		if($entry->getSourceType() == EntrySourceType::KALTURA_RECORDED_LIVE)
		{
			$entryAssets = assetPeer::retrieveReadyByEntryId($entry->getId());
			if(!count($entryAssets))
			{
				$status = EntryVendorTaskStatus::PENDING_ENTRY_READY;
			}
		}
		
		$dictionary = $reachProfile->getDictionaryByLanguage($vendorCatalogItem->getSourceLanguage());
		if ($dictionary)
			$entryVendorTask->setDictionary($dictionary->getData());

		$entryVendorTask->setStatus($status);
		return $entryVendorTask;
	}
	
	//For automatic dispatched tasks make sure to set the entry creator user as the entry owner
	protected static function getTaskKuserId(entry $entry)
	{
		$kuserId = kCurrentContext::getCurrentKsKuserId();
		if(kCurrentContext::$ks_partner_id <= PartnerPeer::GLOBAL_PARTNER)
		{
			$kuserId = $entry->getKuserId();
		}
		
		return $kuserId;
	}
	
	//For automatic dispatched tasks make sure to set the entry creator user as the entry owner
	protected static function getTaskPuserId(entry $entry)
	{
		$puserId = kCurrentContext::$ks_uid;
		if(kCurrentContext::$ks_partner_id <= PartnerPeer::GLOBAL_PARTNER)
		{
			$puserId = $entry->getPuserId();
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
					KalturaLog::debug("None of the fullfield catalog item ids are active on partner, [" . implode(",", $fullFieldCatalogItemIds) . "]");
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
				return "error'd while converting";
			case entryStatus::ERROR_IMPORTING:
				return "error'd while importing";
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

	protected static function getTaskJobData($object)
	{
		if($object instanceof CaptionAsset)
		{
			$taskJobData = new kTranslationVendorTaskData();
			$taskJobData->captionAssetId = $object->getId();
			return $taskJobData;
		}

		return null;
	}
}
