<?php
class kReachFlowManager implements kBatchJobStatusEventConsumer
{
	const ADMIN_CONSOLE_RULE_PREFIX = "AutomaticAdminConsoleRule_";

	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		if ($dbBatchJob->getJobType() == BatchJobType::COPY_PARTNER &&  $dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED)
			return true;

		return false;
	}

	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob)
	{
		if ($dbBatchJob->getJobType() == BatchJobType::COPY_PARTNER &&  $dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED)
		{
			return $this->handleCopyReachDataToPartner($dbBatchJob);
		}
	}


	/**
	 * @param BatchJob $dbBatchJob
	 * @return bool
	 */
	protected function handleCopyReachDataToPartner(BatchJob $dbBatchJob)
	{
		/** @var $dbBatchJob kCopyPartnerJobData */
		$fromPartnerId = $dbBatchJob->getData()->getFromPartnerId();
		$toPartnerId = $dbBatchJob->getData()->getToPartnerId();

		if (!ReachPlugin::isAllowedPartner($fromPartnerId) || !ReachPlugin::isAllowedPartner($toPartnerId))
		{
			KalturaLog::info("Skip copying reach data from partner [$fromPartnerId] to partner [$toPartnerId]. Reach plugin is not enabled");
			return true;
		}

		KalturaLog::info("Start Copying Active ReachProfiles and PartnerCatalogItems from partner [$fromPartnerId]: to partner [$toPartnerId]");
		$reachProfileIdMapping = array();
		$reachProfiles = ReachProfilePeer::retrieveByPartnerId($fromPartnerId);
		foreach ($reachProfiles as $profile)
		{
			/* @var $profile ReachProfile */
			$newReachProfile = $profile->copy();
			$newReachProfile->setPartnerId($toPartnerId);
			$rules = $newReachProfile->getRulesArray();
			foreach ( $rules as $key => $rule )
			{
				/* @var krule $rule*/
				$description = $rule->getDescription();
				if (empty($description)
					|| substr($rule->getDescription(), 0, strlen(self::ADMIN_CONSOLE_RULE_PREFIX)) !== self::ADMIN_CONSOLE_RULE_PREFIX)
				{
					unset($rules[$key]);
				}
			}
			$newReachProfile->setRulesArray($rules);
			$newReachProfile->save();
			$reachProfileIdMapping[$profile->getId()] = $newReachProfile->getId();
		}
		KalturaLog::debug("Reach profile ID mapping: " . print_r($reachProfileIdMapping, true));

		$catalogItems = PartnerCatalogItemPeer::retrieveActiveCatalogItems($fromPartnerId);
		foreach ($catalogItems as $catalogItem)
		{
			/* @var $catalogItem PartnerCatalogItem */
			$newCatalogItem = $catalogItem->copy();
			$newCatalogItem->setPartnerId($toPartnerId);
			$oldDefaultProfileId = $catalogItem->getDefaultReachProfileId();
			if ($oldDefaultProfileId && isset($reachProfileIdMapping[$oldDefaultProfileId]))
			{
				$newCatalogItem->setDefaultReachProfileId($reachProfileIdMapping[$oldDefaultProfileId]);
				KalturaLog::debug("Updated defaultReachProfileId from [$oldDefaultProfileId] to [" . $reachProfileIdMapping[$oldDefaultProfileId] . "] for catalog item [" . $catalogItem->getCatalogItemId() . "]");
			}
			elseif ($oldDefaultProfileId)
			{
				KalturaLog::debug("defaultReachProfileId [$oldDefaultProfileId] on catalog item [" . $catalogItem->getCatalogItemId() . "] has no matching profile in mapping");
			}
			$newCatalogItem->save();
		}

		return true;
	}
}

