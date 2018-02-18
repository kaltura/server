<?php

/**
 * @package plugins.scheduledTaskContentDistribution
 * @subpackage lib.objectTaskEngine
 */
class KObjectTaskDistributeEngine extends KObjectTaskEntryEngineBase
{

	/**
	 * @param KalturaBaseEntry $object
	 */
	function processObject($object)
	{
		/** @var KalturaDistributeObjectTask $objectTask */
		$objectTask = $this->getObjectTask();
		if (is_null($objectTask))
			return;

		$entryId = $object->id;
		$distributionProfileId = $objectTask->distributionProfileId;
		if (!$distributionProfileId)
			throw new Exception('Distribution profile id was not configured');

		KalturaLog::info("Trying to distribute entry $entryId with profile $distributionProfileId");

		$client = $this->getClient();
		$contentDistributionPlugin = KalturaContentDistributionClientPlugin::get($client);
		$distributionProfile = $contentDistributionPlugin->distributionProfile->get($distributionProfileId);

		if ($distributionProfile->submitEnabled == KalturaDistributionProfileActionStatus::DISABLED)
			throw new Exception("Submit action for distribution profile $distributionProfileId id disabled");

		$entryDistribution = $this->getEntryDistribution($entryId, $distributionProfileId);
		if ($entryDistribution && $entryDistribution->status == KalturaEntryDistributionStatus::REMOVED)
		{
			KalturaLog::info("Entry distribution is in status REMOVED, deleting it completely");
			$contentDistributionPlugin->entryDistribution->delete($entryDistribution->id);
			$entryDistribution = null;
		}

		if ($entryDistribution)
		{
			KalturaLog::info("Entry distribution already exists with id $entryDistribution->id");
		}
		else
		{
			$entryDistribution = new KalturaEntryDistribution();
			$entryDistribution->distributionProfileId = $distributionProfileId;
			$entryDistribution->entryId = $entryId;
			$entryDistribution = $contentDistributionPlugin->entryDistribution->add($entryDistribution);
		}

		$shouldSubmit = false;
		switch($entryDistribution->status)
		{
			case KalturaEntryDistributionStatus::PENDING:
				$shouldSubmit = true;
				break;
			case KalturaEntryDistributionStatus::QUEUED:
				KalturaLog::info('Entry distribution is already queued');
				break;
			case KalturaEntryDistributionStatus::READY:
				KalturaLog::info('Entry distribution was already submitted');
				break;
			case KalturaEntryDistributionStatus::SUBMITTING:
				KalturaLog::info('Entry distribution is currently being submitted');
				break;
			case KalturaEntryDistributionStatus::UPDATING:
				KalturaLog::info('Entry distribution is currently being updated, so it was submitted already');
				break;
			case KalturaEntryDistributionStatus::DELETING:
				// throwing exception, the task will retry on next execution
				throw new Exception('Entry distribution is currently being deleted and cannot be handled at this stage');
				break;
			case KalturaEntryDistributionStatus::ERROR_SUBMITTING:
			case KalturaEntryDistributionStatus::ERROR_UPDATING:
			case KalturaEntryDistributionStatus::ERROR_DELETING:
				KalturaLog::info('Entry distribution is in error state, trying to resubmit');
				$shouldSubmit = true;
				break;
			case KalturaEntryDistributionStatus::IMPORT_SUBMITTING:
			case KalturaEntryDistributionStatus::IMPORT_UPDATING:
				KalturaLog::info('Entry distribution is waiting for an import job to be finished, do nothing, it will be submitted/updated automatically');
				break;
			default:
				throw new Exception("Entry distribution status $entryDistribution->status is invalid");
		}

		if ($shouldSubmit)
		{
			$contentDistributionPlugin->entryDistribution->submitAdd($entryDistribution->id, true);
		}
	}

	protected function getEntryDistribution($entryId, $distributionProfileId)
	{
		$distributionPlugin = KalturaContentDistributionClientPlugin::get($this->getClient());
		$entryDistributionFilter = new KalturaEntryDistributionFilter();
		$entryDistributionFilter->entryIdEqual = $entryId;
		$entryDistributionFilter->distributionProfileIdEqual = $distributionProfileId;
		$result = $distributionPlugin->entryDistribution->listAction($entryDistributionFilter);
		if (count($result->objects))
			return $result->objects[0];
		else
			return null;
	}
}