<?php
/**
 * will sync the chargebee_vendor_integration against the subscriptions in chargeBee.
 *
 * @package plugins.chargeBee
 * @subpackage syncSubscriptions
 */

class KAsyncSyncSubscriptions extends KPeriodicWorker
{
	const MAX_PAGE_SIZE = 500;
	const LOCK_EXPIRY = 'lock_expiry';
	const VENDOR_MAP = 'vendor';
	const DEFAULT_LOCK_EXPIRY = 36000;
	const EXTEND_LOCK = 'extend_lock';
	const EXTEND_LOCK_EXPIRY = 86400;
	const SUBSCRIPTION = 'subscription';
	const STATUS = 'status';
	const PRIMARY_PAYMENT_SOURCE_ID = 'primary_payment_source_id';
	const IN_TRIAL = 'in_trial';
	const PAID = 'paid';
	const CANCELED = 'canceled';
	const REASON_SUB_CANCELED = 'subscription at CB is canceled';

	/**
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
		return KalturaBatchJobType::SYNC_SUBSCRIPTIONS;
	}

	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function run($jobs = null)
	{

		list($chargeBeeFilter, $pager) = self::prepareFilterAndPager();
		$shouldCalculateTotalCount = true;
		do {
			try {
				KalturaLog::debug('Getting all the charge bee items');
				$lockExpiryTimeout = kconf::get(self::LOCK_EXPIRY, self::VENDOR_MAP, self::DEFAULT_LOCK_EXPIRY);
				$vendorIntegrationList = $this->chargeBeeClientPlugin->chargeBeeVendor->listAndLock($chargeBeeFilter, $pager, $this->getId(), $lockExpiryTimeout);
			} catch (Exception $e) {
				KalturaLog::warning('Could not list Vendor Integration ' . $e->getMessage());
				return;
			}

			if ($shouldCalculateTotalCount) {
				$totalCount = $vendorIntegrationList->totalCount;
				$shouldCalculateTotalCount = false;
			}

			$this->syncSubscriptions($vendorIntegrationList);
			$pager->pageIndex++;
			$totalCount = $totalCount - $pager->pageSize;
			$pager->pageSize = min(self::MAX_PAGE_SIZE, $totalCount);

		} while ($totalCount > 0);

		KalturaLog::debug('Done');
	}

	protected static function prepareFilterAndPager($objectId = null)
	{
		$chargeBeeFilter = new KalturaChargeBeeVendorIntegrationFilter();
		$chargeBeeFilter->typeIn = KalturaVendorTypeEnum::CHARGE_BEE_FREE_TRIAL . ',' . KalturaVendorTypeEnum::CHARGE_BEE_PAYGO;
		$chargeBeeFilter->statusEqual = KalturaVendorStatus::ACTIVE;
		$chargeBeeFilter->orderBy = KalturaChargeBeeVendorIntegrationOrderBy::CREATED_AT_ASC;
		if ($objectId) {
			$chargeBeeFilter->idEqual = $objectId;
		}

		$pager = new KalturaFilterPager();
		$pager->pageSize = self::MAX_PAGE_SIZE;
		$pager->pageIndex = 1;

		return array($chargeBeeFilter, $pager);
	}

	protected function syncSubscriptions($vendorIntegrationList)
	{
		foreach ($vendorIntegrationList->objects as $vendorIntegration) {
			$partner = $this->getPartner($vendorIntegration->partnerId);
			$chargeBeeClient = kChargeBeeUtils::getChargeBeeClient($partner->country);
			if ($chargeBeeClient)
			{
				$this->checkSubscription($chargeBeeClient, $vendorIntegration);
				$this->extendLockExpiryOnVendorIntegration($vendorIntegration);
			}
		}
	}

	protected function checkSubscription($chargeBeeClient, $vendorIntegration)
	{
		$responseSubscription = $this->getSubscriptionFromCB($chargeBeeClient, $vendorIntegration->subscriptionId);
		if (isset($responseSubscription[self::SUBSCRIPTION]))
		{
			$status = $responseSubscription[self::SUBSCRIPTION][self::STATUS];
			switch ($status)
			{
				case self::IN_TRIAL:
					if ($vendorIntegration->type != KalturaVendorTypeEnum::CHARGE_BEE_FREE_TRIAL)
					{
						$this->updateVendorIntegration($vendorIntegration, KalturaVendorTypeEnum::CHARGE_BEE_FREE_TRIAL);
					}
					break;
				case self::PAID:
					$paymentSource = isset($responseSubscription[self::SUBSCRIPTION][self::PRIMARY_PAYMENT_SOURCE_ID]);
					if (!$paymentSource && $vendorIntegration->type != KalturaVendorTypeEnum::CHARGE_BEE_FREE_TRIAL)
					{
						$this->updateVendorIntegration($vendorIntegration, KalturaVendorTypeEnum::CHARGE_BEE_FREE_TRIAL);
					}
					elseif ($paymentSource && $vendorIntegration->type != KalturaVendorTypeEnum::CHARGE_BEE_PAYGO)
					{
						$this->updateVendorIntegration($vendorIntegration, KalturaVendorTypeEnum::CHARGE_BEE_PAYGO);
					}
					break;
				case self::CANCELED:
					$this->updateVendorIntegration($vendorIntegration, null, KalturaVendorStatus::DISABLED);
					$this->updatePartnerStatus($vendorIntegration->partnerId, KalturaPartnerStatus::FULL_BLOCK, self::REASON_SUB_CANCELED);
					break;
				default:
					break;
			}
		}
	}

	protected function getSubscriptionFromCB($chargeBeeClient, $subscriptionId)
	{
		$responseSubscription = $chargeBeeClient->retrieveSubscription($subscriptionId);
		KalturaLog::log('Response from chargeBee, subscription: ' . print_r($responseSubscription, true));
		return $responseSubscription;
	}

	protected function updateVendorIntegration($vendorIntegration, $type = null, $status = null)
	{
		$kVendorIntegration = new KalturaChargeBeeVendorIntegration();
		if ($type)
		{
			$kVendorIntegration->type = $type;
		}
		if ($status)
		{
			$kVendorIntegration->status = $status;
		}
		$this->chargeBeeClientPlugin->chargeBeeVendor->update($vendorIntegration->id, $kVendorIntegration);
	}

	protected function updatePartnerStatus($partnerId, $status, $reason)
	{
		$systemPartnerClientPlugin = KalturaSystemPartnerClientPlugin::get(self::$kClient);
		$systemPartnerClientPlugin->systemPartner->updateStatus($partnerId, $status, $reason);
	}

	protected function extendLockExpiryOnVendorIntegration($vendorIntegration)
	{
		$extendLockExpiryTimeout = kconf::get(self::EXTEND_LOCK, self::VENDOR_MAP, self::EXTEND_LOCK_EXPIRY);
		list($chargeBeeFilter, $pager) = self::prepareFilterAndPager($vendorIntegration->id);
		$this->chargeBeeClientPlugin->chargeBeeVendor->listAndLock($chargeBeeFilter, $pager, $this->getId(), $extendLockExpiryTimeout);
	}

	protected function getPartner($partnerId)
	{
		KBatchBase::impersonate($partnerId);
		$partner = KBatchBase::$kClient->partner->get($partnerId);
		KBatchBase::unimpersonate();
		return $partner;
	}

}