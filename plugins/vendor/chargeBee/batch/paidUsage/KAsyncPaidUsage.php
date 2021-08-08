<?php
/**
 * Going over all the PAID vendor_integration:
 *
 * @package plugins.chargeBee
 * @subpackage paidUsage
 */

class KAsyncPaidUsage extends KPeriodicWorker
{
	const MAX_PAGE_SIZE = 500;
	const LOCK_EXPIRY = 'lock_expiry';
	const VENDOR_MAP = 'vendor';
	const DEFAULT_LOCK_EXPIRY = 36000;
	const EXTEND_LOCK = 'extend_lock';
	const EXTEND_LOCK_EXPIRY = 86400;

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
		if (!$this->shouldRunToday())
		{
			return;
		}
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

	protected function shouldRunToday()
	{
		$runDay = KBatchBase::$taskConfig->params->runDay;
		$currentDay = date('d', time());
		if ($currentDay == $runDay)
		{
			return true;
		}
		KalturaLog::warning('Should not run today, run date is: ' . $runDay);
		return false;
	}

	protected static function prepareFilterAndPager($objectId = null)
	{
		$chargeBeeFilter = new KalturaChargeBeeVendorIntegrationFilter();
		$chargeBeeFilter->typeEqual = KalturaVendorTypeEnum::CHARGE_BEE_PAYGO;
		$chargeBeeFilter->statusEqual = KalturaVendorStatus::ACTIVE;
		$chargeBeeFilter->orderBy = KalturaChargeBeeVendorIntegrationOrderBy::CREATED_AT_ASC;
		if ($objectId)
		{
			$chargeBeeFilter->idEqual = $objectId;
		}

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
		$invoiceId = $vendorIntegration->invoiceId;
		if (isset($invoiceId))
		{
			$this->updateSubscriptionAddOns($chargeBeeClient, $invoiceId, $addonId, $addonQuantity);
			$this->closeSubscriptionInvoice($chargeBeeClient, $invoiceId);
			$this->extendLockExpiryOnVendorIntegration($vendorIntegration);
		}
	}

	protected function extendLockExpiryOnVendorIntegration($vendorIntegration)
	{
		$extendLockExpiryTimeout = kconf::get(self::EXTEND_LOCK, self::VENDOR_MAP, self::EXTEND_LOCK_EXPIRY);
		list($chargeBeeFilter, $pager) = self::prepareFilterAndPager($vendorIntegration->id);
		$this->chargeBeeClientPlugin->chargeBeeVendor->listAndLock($chargeBeeFilter, $pager, $this->getId(), $extendLockExpiryTimeout);
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