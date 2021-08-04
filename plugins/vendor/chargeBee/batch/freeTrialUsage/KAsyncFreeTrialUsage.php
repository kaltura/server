<?php
/**
 * Going over all the FREE TRIAL vendor_integration:
 *
 * @package plugins.chargeBee
 * @subpackage freeTrialUsage
 */

class KAsyncFreeTrialUsage extends KPeriodicWorker
{
	const MAX_PAGE_SIZE = 500;
	const ESTIMATE = 'estimate';
	const INVOICE_ESTIMATE = 'invoice_estimate';
	const AMOUNT_DUE = 'amount_due';
	const SUBSCRIPTION = 'subscription';
	const TRIAL_END = 'trial_end';
	const IMMEDIATE = 0;
	const LOCK_EXPIRY = 'lock_expiry';
	const VENDOR_MAP = 'vendor';
	const DEFAULT_LOCK_EXPIRY = 36000;

	/*
	* @var KalturaChargeBeeClientPlugin
	*/
	private $chargeBeeClientPlugin = null;

	/**
	 * @param KSchedularTaskConfig $taskConfig
	 */
	public function __construct($taskConfig = null)
	{
		parent::__construct($taskConfig);
		$this->chargeBeeClientPlugin = KalturaChargeBeeClientPlugin::get(KBatchBase::$kClient);
	}

	/* (non-PHPdoc)
	* @see KBatchBase::getType()
	*/
	public static function getType()
	{
		return KalturaBatchJobType::FREE_TRIAL_DAILY_USAGE;
	}

	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function run($jobs = null)
	{
		list($chargeBeeFilter, $pager) = self::prepareFilterAndPager();
		$shouldCalculateTotalCount = true;
		do
		{
			try
			{
				KalturaLog::debug('Getting all the charge bee free trial items');
				$lockExpiryTimeout = kconf::get(self::LOCK_EXPIRY, self::VENDOR_MAP, self::DEFAULT_LOCK_EXPIRY);
				$vendorIntegrationList = $this->chargeBeeClientPlugin->chargeBeeVendor->listAndLock($chargeBeeFilter, $pager, $this->getId(), $lockExpiryTimeout);
			}
			catch (Exception $e)
			{
				KalturaLog::warning('Could not list Vendor Integration ' . $e->getMessage());
				return;
			}

			if ($shouldCalculateTotalCount)
			{
				$totalCount = $vendorIntegrationList->totalCount;
				$shouldCalculateTotalCount = false;
			}

			$this->handlePartnersUsage($vendorIntegrationList);
			$pager->pageIndex++;
			$totalCount = $totalCount - $pager->pageSize;
			$pager->pageSize = min(self::MAX_PAGE_SIZE, $totalCount);

		} while ($totalCount > 0);
		KalturaLog::debug('Done');
	}

	protected static function prepareFilterAndPager()
	{
		$chargeBeeFilter = new KalturaChargeBeeVendorIntegrationFilter();
		$chargeBeeFilter->typeEqual = KalturaVendorTypeEnum::CHARGE_BEE_FREE_TRIAL;
		$chargeBeeFilter->statusEqual = KalturaVendorStatus::ACTIVE;
		$chargeBeeFilter->orderBy = KalturaChargeBeeVendorIntegrationOrderBy::CREATED_AT_ASC;

		$pager = new KalturaFilterPager();
		$pager->pageSize = self::MAX_PAGE_SIZE;
		$pager->pageIndex = 1;

		return array($chargeBeeFilter, $pager);
	}

	protected function handlePartnersUsage($vendorIntegrationList)
	{
		$vendorIntegrationOrdered = kChargeBeeUtils::orderVendorIntegration($vendorIntegrationList->objects);
		foreach ($vendorIntegrationOrdered as $vendorIntegrationsPerTimeSlot)
		{
			//todo get report from analytics with all the vendors integration on the time slot.
			foreach ($vendorIntegrationsPerTimeSlot as $vendorIntegration)
			{
				$partner = $this->getPartner($vendorIntegration->partnerId);
				if ($partner->status !== KalturaPartnerStatus::READ_ONLY)
				{
					$chargeBeeClient = kChargeBeeUtils::getChargeBeeClient($partner->country);
					if ($chargeBeeClient)
					{
						$this->checkSubscriptionCredit($chargeBeeClient, $vendorIntegration, $partner);
					}
				}
			}
		}
	}

	protected function checkSubscriptionCredit($chargeBeeClient, $vendorIntegration, $partner)
	{
		$amountDue = $this->getAmountDueByEstimateInvoice($chargeBeeClient, $vendorIntegration);
		//$amountDue = 1;
		if ($amountDue > 0)
		{
			KalturaLog::log('Credit is finished, amount due is: ' . print_r($amountDue, true));
			$this->changePartnerToReadOnly($partner);
			$responseUpdateSubscription = $chargeBeeClient->updateSubscriptionTrialEnd($vendorIntegration->subscriptionId, self::IMMEDIATE);
			KalturaLog::log('Response from chargeBee update a subscription: ' . print_r($responseUpdateSubscription, true));
		}
		else
		{
			$trial_end = $this->getTrialEnd($chargeBeeClient, $vendorIntegration);
			//$trial_end = 1;
			if ($trial_end && $trial_end <= time())
			{
				KalturaLog::log('trial end was ended on: ' . $trial_end);
				$this->changePartnerToReadOnly($partner);
			}
		}
	}

	public function changePartnerToReadOnly($partner)
	{

		KBatchBase::impersonate($partner->id);
		$updatedPartner = KBatchBase::$kClient->systemPartner->updateStatus($partner->id, KalturaPartnerStatus::READ_ONLY, 'changing to read only');
		$childPartners = $this->getChildren($partner->id);
		KBatchBase::unimpersonate();
		foreach ($childPartners->objects as $childPartner)
		{
			$this->changePartnerToReadOnly($childPartner);
		}
	}

	public function getChildren($partnerId)
	{
		$filter = new KalturaSystemPartnerFilter();
		$filter->partnerParentIdEqual = $partnerId;

		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		$pager->pageIndex = 1;

		return KBatchBase::$kClient->systemPartner->listAction($filter, $pager);
	}

	protected function getAmountDueByEstimateInvoice($chargeBeeClient, $vendorIntegration)
	{
		$responseEstimate = $chargeBeeClient->estimateInvoice($vendorIntegration->subscriptionId, 200, 'this is description'); //todo:change this
		KalturaLog::log('Response from chargeBee estimation: ' . print_r($responseEstimate, true));
		return (isset($responseEstimate[self::ESTIMATE][self::INVOICE_ESTIMATE][self::AMOUNT_DUE])) ?
			$responseEstimate[self::ESTIMATE][self::INVOICE_ESTIMATE][self::AMOUNT_DUE] : 0;
	}

	protected function getTrialEnd($chargeBeeClient, $vendorIntegration)
	{
		$responseSubscription = $chargeBeeClient->retrieveSubscription($vendorIntegration->subscriptionId);
		return (isset($responseSubscription[self::SUBSCRIPTION][self::TRIAL_END])) ?
			$responseSubscription[self::SUBSCRIPTION][self::TRIAL_END] : null;
	}

	protected function getPartner($partnerId)
	{
		KBatchBase::impersonate($partnerId);
		$partner = KBatchBase::$kClient->partner->get($partnerId);
		KBatchBase::unimpersonate();
		return $partner;
	}

}