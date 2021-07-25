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
	const FREE_TRIAL_AMOUNT = 'freeTrialAmount';
	const ID = 'id';
	
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
		$chargeBeeVendor = $this->createChargeBeeVendorIntegration($subscriptionId, $chargeBeePlugin);
		$this->handleSubscriptionResult($subscriptionId, $chargeBeeClient, $chargeBeeConfMap, $chargeBeeVendor, $chargeBeePlugin);
	}

	public function handleSubscriptionResult($subscriptionId, $chargeBeeClient, $chargeBeeConfMap, $chargeBeeVendor, $chargeBeePlugin)
	{
		if ($subscriptionId)
		{
			$updatedAmount = $chargeBeeClient->updateFreeTrial($subscriptionId, $chargeBeeConfMap[self::FREE_TRIAL_AMOUNT], 'add promotional credits');
			$this->log('Response from chargeBee updateFreeTrial: ' . print_r($updatedAmount, true));
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

	public function createChargeBeeVendorIntegration($subscriptionId, $chargeBeePlugin)
	{
		$chargeBeeVendorIntegration = new KalturaChargeBeeVendorIntegration();
		$chargeBeeVendorIntegration->subscriptionId = $subscriptionId;
		$chargeBeeVendorIntegration->type = KalturaVendorTypeEnum::CHARGE_BEE_FREE_TRIAL;
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
