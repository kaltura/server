<?php
/**
 * Going over all the PAID vendor_integration:
 *
 * @package plugins.chargeBee
 * @subpackage freeTrialUsage
 */

class KAsyncPaidUsage extends KPeriodicWorker
{
	const MAX_PAGE_SIZE = 500;
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
		return KalturaBatchJobType::PAID_MONTHLY_USAGE;
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
				KalturaLog::debug('Getting all the charge bee paid items');
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
		$chargeBeeFilter->typeEqual = KalturaVendorTypeEnum::CHARGE_BEE_FREE_PAYGO;
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
			$addonId='Vpaas_Storage'; //todo take from report
			$addonQuantity = 3; //todo take from report
			foreach ($vendorIntegrationsPerTimeSlot as $vendorIntegration)
			{
				$partner = $this->getPartner($vendorIntegration->partnerId);
				$chargeBeeClient = kChargeBeeUtils::getChargeBeeClient($partner->country);
				if ($chargeBeeClient)
				{
					$this->handleSubscriptionInvoice($chargeBeeClient, $vendorIntegration, $addonId, $addonQuantity);
				}
			}
		}
	}

	protected function handleSubscriptionInvoice($chargeBeeClient, $vendorIntegration, $addonId, $addonQuantity)
	{
		//$updatedAmount = $chargeBeeClient->updateFreeTrial('16BjmwSd2Gsdi1tHw', 1000, 'add promotional credits');
		$invoiceId = $vendorIntegration->invoiceId;
		if (isset($invoiceId))
		{
			$this->updateSubscriptionAddOns($chargeBeeClient, $invoiceId, $addonId, $addonQuantity);
			$this->closeSubscriptionInvoice($chargeBeeClient, $invoiceId);
		}
	}

	protected function updateSubscriptionAddOns($chargeBeeClient, $invoiceId, $addonId, $addonQuantity)
	{
		$responseInvoice = $chargeBeeClient->createInvoice($invoiceId, $addonId, $addonQuantity);
		KalturaLog::log('Response from chargeBee invoice add ons: ' . print_r($responseInvoice, true));
	}

	protected function closeSubscriptionInvoice($chargeBeeClient, $invoiceId)
	{
		$responseInvoiceClose = $chargeBeeClient->closeInvoice($invoiceId);
		KalturaLog::log('Response from chargeBee invoice close: ' . print_r($responseInvoiceClose, true));
	}

	protected function getPartner($partnerId)
	{
		KBatchBase::impersonate($partnerId);
		$partner = KBatchBase::$kClient->partner->get($partnerId);
		KBatchBase::unimpersonate();
		return $partner;
	}

}