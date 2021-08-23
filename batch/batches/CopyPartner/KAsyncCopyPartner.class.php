<?php
/**
 * Copy an entire partner to and new one
 *
 * @package Scheduler
 * @subpackage CopyPartner
 */
class KAsyncCopyPartner extends KJobHandlerWorker
{
	protected $fromPartnerId;
	protected $toPartnerId;
	
	const EMAIL_ADDRESSES = 'emailAddresses';
	const SUBSCRIPTION = 'subscription';
	const ID = 'id';
	const FREE_TRIAL_AMOUNT_DESC = 'add free trial amount';
	const FREE_TRIAL_CREDIT = 'cf_free_trial_credits';
	const PLAN = 'plan';
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::COPY_PARTNER;
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	public function getJobType()
	{
		return self::getType();
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 * @return KalturaBatchJob
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->doCopyPartner($job, $job->data);
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 * @return KalturaBatchJob
	 */
	protected function doCopyPartner(KalturaBatchJob $job, KalturaCopyPartnerJobData $jobData)
	{
		$this->log( "doCopyPartner job id [$job->id], From PID: $jobData->fromPartnerId, To PID: $jobData->toPartnerId" );

		$this->fromPartnerId = $jobData->fromPartnerId;
		$this->toPartnerId = $jobData->toPartnerId;
		
		// copy permssions before trying to copy additional objects such as distribution profiles which are not enabled yet for the partner
 		$this->copyAllEntries();
 		$this->addPartnerAsChargeBeeSubscription();
		
 		return $this->closeJob($job, null, null, "doCopyPartner finished", KalturaBatchJobStatus::FINISHED);
	}
	
	/**
	 * copyAllEntries()
	 */
	protected function copyAllEntries()
	{
		$entryFilter = new KalturaBaseEntryFilter();
 		$entryFilter->order = KalturaBaseEntryOrderBy::CREATED_AT_ASC;
		
		$pageFilter = new KalturaFilterPager();
		$pageFilter->pageSize = 50;
		$pageFilter->pageIndex = 1;
		
		/* @var $this->getClient() KalturaClient */
		do
		{
			// Get the source partner's entries list
			self::impersonate( $this->fromPartnerId );
			$entriesList = $this->getClient()->baseEntry->listAction( $entryFilter, $pageFilter );

			$receivedObjectsCount = $entriesList->objects ? count($entriesList->objects) : 0;
			$pageFilter->pageIndex++;
			
			if ( $receivedObjectsCount > 0 )
			{
				// Write the source partner's entries to the destination partner 
				self::impersonate( $this->toPartnerId );
				foreach ( $entriesList->objects as $entry )
				{
					$newEntry = $this->getClient()->baseEntry->cloneAction( $entry->id );
				}
			}			
		} while ( $receivedObjectsCount );
	
		self::unimpersonate();
	}

	public function addPartnerAsChargeBeeSubscription()
	{
		self::impersonate( $this->toPartnerId );
		$toPartner = $this->getClient()->partner->get($this->toPartnerId);
		self::unimpersonate();
		if ($toPartner->partnerPackage == KAsyncStorageUpdateUtils::PARTNER_PACKAGE_FREE && $toPartner->partnerParentId == null)
		{
			$this->log("Creating new subscription in ChargeBee for partner id: [$this->toPartnerId]" );
			$this->handleSubscriptionInChargeBee($toPartner);
		}
	}

	public function handleSubscriptionInChargeBee($toPartner)
	{
		list($chargeBeeConfMap, $site, $siteApiKey) = kChargeBeeUtils::getSiteConfig($toPartner->country);
		if (!$chargeBeeConfMap || !$site || !$siteApiKey)
		{
			return;
		}
		$chargeBeeClient = new kChargeBeeClient($chargeBeeConfMap[$site], $chargeBeeConfMap[$siteApiKey]);
		$responseSubscription = $chargeBeeClient->createSubscription($chargeBeeConfMap[kChargeBeeUtils::PLAN_ID], $chargeBeeConfMap[kChargeBeeUtils::AUTO_COLLECTION], $toPartner->firstName, $toPartner->lastName, $toPartner->adminEmail);
		$this->log('Response from chargeBee createSubscription: ' . print_r($responseSubscription, true));
		$subscriptionId = isset($responseSubscription[self::SUBSCRIPTION]) ?  $responseSubscription[self::SUBSCRIPTION][self::ID] : null;
		$chargeBeePlugin = KalturaChargeBeeClientPlugin::get(KBatchBase::$kClient);
		$chargeBeeVendor = $this->createChargeBeeVendorIntegration($subscriptionId, $chargeBeePlugin, $chargeBeeConfMap[kChargeBeeUtils::PLAN_ID]);
		$this->handleSubscriptionResult($subscriptionId, $chargeBeeClient, $chargeBeeConfMap, $chargeBeeVendor, $chargeBeePlugin);
	}

	public function handleSubscriptionResult($subscriptionId, $chargeBeeClient, $chargeBeeConfMap, $chargeBeeVendor, $chargeBeePlugin)
	{
		if ($subscriptionId)
		{
			$freeTrialAmount =  $this->getFreeTrialAmount($chargeBeeClient, $chargeBeeConfMap[kChargeBeeUtils::PLAN_ID]);
			if ($freeTrialAmount)
			{
				$updatedAmount = $chargeBeeClient->updateFreeTrial($subscriptionId, $freeTrialAmount, self::FREE_TRIAL_AMOUNT_DESC);
				$this->log('Response from chargeBee updateFreeTrial: ' . print_r($updatedAmount, true));
			}
		}
		else
		{
			$addresses = isset($chargeBeeConfMap[self::EMAIL_ADDRESSES]) ? array_map('trim', explode(',', $chargeBeeConfMap[self::EMAIL_ADDRESSES])) : null;
			$success = kSendMail::sendMail($addresses, 'Create Subscription to ChargeBee has failed', "Create Subscription to ChargeBee has failed on partner id: [$this->toPartnerId]");
			if (!$success)
			{
				KalturaLog::info('Mail for Create Subscription did not send successfully');
			}
			$this->updateChargeBeeVendorIntegrationStatus($chargeBeeVendor->id, $chargeBeePlugin, KalturaVendorStatus::ERROR);
		}
	}

	protected function getFreeTrialAmount($chargeBeeClient, $planId)
	{
		$plan = $this->getPlan($chargeBeeClient, $planId);
		return isset($plan[self::PLAN][self::FREE_TRIAL_CREDIT]) ? $plan[self::PLAN][self::FREE_TRIAL_CREDIT] : 0;
	}

	protected function getPlan($chargeBeeClient, $planId)
	{
		$responsePlan = $chargeBeeClient->retrievePlan($planId);
		KalturaLog::log('Response from chargeBee plan: ' . print_r($responsePlan, true));
		return $responsePlan;
	}

	public function createChargeBeeVendorIntegration($subscriptionId, $chargeBeePlugin, $planId)
	{
		$chargeBeeVendorIntegration = new KalturaChargeBeeVendorIntegration();
		$chargeBeeVendorIntegration->subscriptionId = $subscriptionId;
		$chargeBeeVendorIntegration->type = KalturaVendorTypeEnum::CHARGE_BEE_FREE_TRIAL;
		$chargeBeeVendorIntegration->planId = $planId;
		self::impersonate( $this->toPartnerId );
		$chargeBeeVendor =  $chargeBeePlugin->chargeBeeVendor->add($chargeBeeVendorIntegration);
		self::unimpersonate();
		return $chargeBeeVendor;
	}


	public function updateChargeBeeVendorIntegrationStatus($chargeBeeVendorId, $chargeBeePlugin, $status)
	{
		$chargeBeeVendorIntegration = new KalturaChargeBeeVendorIntegration();
		$chargeBeeVendorIntegration->status = $status;
		$chargeBeePlugin->chargeBeeVendor->update($chargeBeeVendorId, $chargeBeeVendorIntegration);
	}
}
