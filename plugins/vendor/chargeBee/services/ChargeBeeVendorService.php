<?php
/**
 * @service chargeBeeVendor
 * @package plugins.chargeBee
 * @subpackage api.services
 */
class ChargeBeeVendorService extends KalturaBaseService
{
	const MAP_NAME = 'vendor';

	protected static $PARTNER_NOT_REQUIRED_ACTIONS = array('handleNotification');
	
	const REQUEST_USER = 'PHP_AUTH_USER';
	const REQUEST_PASSWORD = 'PHP_AUTH_PW';
	const TRIAL_END_NOW = 0;
	
	const CONFIGURATION_USER = 'user';
	const CONFIGURATION_PASSWORD = 'password';

	const REQUEST_PAYMENT_SOURCE_ADDED = 'payment_source_added';
	const REQUEST_PAYMENT_FAILED = 'payment_failed';
	const REQUEST_SUBSCRIPTION_TRIAL_END_REMINDER = 'subscription_trial_end_reminder';
	const REQUEST_SUBSCRIPTION_CANCELLED = 'subscription_cancelled';
	const REQUEST_INVOICE_CREATED = 'pending_invoice_created';
	
	/**
	 * no partner will be provided by vendors as this called externally and not from kaltura
	 * @param string $actionName
	 * @return bool
	 */
	protected function partnerRequired($actionName)
	{
		return in_array ($actionName, self::$PARTNER_NOT_REQUIRED_ACTIONS);
	}

	/**
	 * Add chargeBee vendor integration object
	 *
	 * @action add
	 * @param KalturaChargeBeeVendorIntegration $chargeBeeVendorIntegration
	 * @return KalturaChargeBeeVendorIntegration
	 */
	public function addAction(KalturaChargeBeeVendorIntegration $chargeBeeVendorIntegration)
	{
		$dbChargeBeeVendorIntegration = $chargeBeeVendorIntegration->toInsertableObject();
		/* @var $dbChargeBeeVendorIntegration kChargeBeeVendorIntegration */

		$dbChargeBeeVendorIntegration->setAccountId($chargeBeeVendorIntegration->subscriptionId);
		$dbChargeBeeVendorIntegration->setVendorType($chargeBeeVendorIntegration->type);
		$dbChargeBeeVendorIntegration->setPartnerId(kCurrentContext::getCurrentPartnerId());
		$dbChargeBeeVendorIntegration->setStatus(VendorIntegrationStatus::ACTIVE);
		$dbChargeBeeVendorIntegration->save();

		$chargeBeeVendorIntegration->fromObject($dbChargeBeeVendorIntegration, $this->getResponseProfile());
		return $chargeBeeVendorIntegration;
	}
	
	/**
	 * Retrieve chargeBee vendor integration object by account it
	 *
	 * @action get
	 * @param int $id
	 * @return KalturaChargeBeeVendorIntegration
	 * @throws KalturaChargeBeeErrors::CHARGE_BEE_VENDOR_INTEGRATION_NOT_FOUND
	 */
	public function getAction($id)
	{
		$dbChargeBeeVendorIntegration = VendorIntegrationPeer::retrieveByPK($id);
		if (!$dbChargeBeeVendorIntegration)
		{
			throw new KalturaAPIException(KalturaChargeBeeErrors::CHARGE_BEE_VENDOR_INTEGRATION_NOT_FOUND, $id);
		}

		$chargeBeeVendorIntegration = new KalturaChargeBeeVendorIntegration();
		$chargeBeeVendorIntegration->fromObject($dbChargeBeeVendorIntegration, $this->getResponseProfile());
		return $chargeBeeVendorIntegration;
	}

	/**
	 * List KalturaChargeBeeVendorIntegration objects
	 *
	 * @action list
	 * @param KalturaChargeBeeVendorIntegrationFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaChargeBeeVendorIntegrationResponse
	 */
	public function listAction(KalturaChargeBeeVendorIntegrationFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
		{
			$filter = new KalturaChargeBeeVendorIntegrationFilter();
		}

		if (!$pager)
		{
			$pager = new KalturaFilterPager();
		}

		return $filter->getListResponse($pager, $this->getResponseProfile());
	}


	/**
	 *
	 * @action update an existing chargeBeeVendorIntegration
	 * @param int $id
	 * @param KalturaChargeBeeVendorIntegration $chargeBeeVendorIntegration
	 * @return KalturaChargeBeeVendorIntegration
	 * @throws KalturaChargeBeeErrors::CHARGE_BEE_VENDOR_INTEGRATION_NOT_FOUND
	 */
	public function updateAction($id, KalturaChargeBeeVendorIntegration $chargeBeeVendorIntegration)
	{
		$dbChargeBeeVendorIntegration = VendorIntegrationPeer::retrieveByPK($id);
		if (!$dbChargeBeeVendorIntegration)
		{
			throw new KalturaAPIException(KalturaChargeBeeErrors::CHARGE_BEE_VENDOR_INTEGRATION_NOT_FOUND, $id);
		}

		$dbChargeBeeVendorIntegration = $chargeBeeVendorIntegration->toUpdatableObject($dbChargeBeeVendorIntegration);
		$dbChargeBeeVendorIntegration->save();

		$chargeBeeVendorIntegration = new KalturaChargeBeeVendorIntegration();
		$chargeBeeVendorIntegration->fromObject($dbChargeBeeVendorIntegration, $this->getResponseProfile());
		return $chargeBeeVendorIntegration;
	}
	
	
	/**
	 * Handle notifications from ChargeBee
	 *
	 * @action handleNotification
	 * @return KalturaChargeBeeVendorIntegration
	 * @throws KalturaChargeBeeErrors::CHARGE_BEE_VENDOR_INTEGRATION_NOT_FOUND
	 */
	public function handleNotificationAction()
	{
		if (!isset($_SERVER[self::REQUEST_USER]) || !isset($_SERVER[self::REQUEST_PASSWORD]))
		{
			throw new KalturaAPIException(KalturaChargeBeeErrors::UNAUTHORIZED_USER_PASSWORD);
		}
		
		$chargeBeeConfiguration = kConf::get(kChargeBeeUtils::CONFIGURATION_PARAM_NAME, self::MAP_NAME, array());
		if (!isset($chargeBeeConfiguration[self::CONFIGURATION_USER]) || !isset($chargeBeeConfiguration[self::CONFIGURATION_PASSWORD]))
		{
			throw new KalturaAPIException(KalturaChargeBeeErrors::MISSING_USER_PASSWORD_CONFIGURATION);
		}
		
		if ($_SERVER[self::REQUEST_USER] != $chargeBeeConfiguration[self::CONFIGURATION_USER] || $_SERVER[self::REQUEST_PASSWORD] != $chargeBeeConfiguration[self::CONFIGURATION_PASSWORD])
		{
			throw new KalturaAPIException(KalturaChargeBeeErrors::UNAUTHORIZED_USER_PASSWORD);
		}

		$this->handlePostData();
	}
	
	
	protected function handlePostData()
	{
		$data = json_decode(file_get_contents('php://input'));
		if (!isset($data) || !isset($data->event_type))
		{
			throw new KalturaAPIException(KalturaChargeBeeErrors::MISSING_EVENT_TYPE);
		}
		
		$eventType = $data->event_type;
		switch ($eventType)
		{
			case self::REQUEST_PAYMENT_SOURCE_ADDED:
				$this->handlePaymentSourceAdded($data);
				break;
			case self::REQUEST_PAYMENT_FAILED:
				$this->handlePaymentFailed($data);
				break;
			case self::REQUEST_SUBSCRIPTION_TRIAL_END_REMINDER:
				$this->handleSubscriptionTrialEndReminder($data);
				break;
			case self::REQUEST_SUBSCRIPTION_CANCELLED:
				$this->handleSubscriptionCancelled($data);
				break;
			case self::REQUEST_INVOICE_CREATED:
				$this->handlePendingInvoiceCreated($data);
				break;
			default:
				KalturaLog::info('ChargeBee request event_type not recognized: ' + $eventType);
		}
	}
	
	
	protected function handlePaymentSourceAdded($data)
	{
		$partnerId = $data->content->customer->id;
		$vendorIntegrations = VendorIntegrationPeer::retrieveVendorsByPartnerAndType($partnerId, KalturaVendorTypeEnum::CHARGE_BEE_FREE_TRIAL);
		if (!$vendorIntegrations)
		{
			throw new KalturaAPIException(KalturaChargeBeeErrors::FAILED_RETRIEVING_VENDOR_INTEGRATION);
		}
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if (!$partner)
		{
			throw new KalturaAPIException(KalturaChargeBeeErrors::FAILED_RETRIEVING_PARTNER);
		}
		$this->makePartnerPayGo($partner);
		$this->makeVendorIntegrationsPayGo($partner, $vendorIntegrations);
	}

	protected function makePartnerPayGo($partner)
	{
		PermissionPeer::disableForPartner(PermissionName::FEATURE_LIMIT_ALLOWED_ACTIONS, $partner->getId());
		$partner->setPartnerPackage(PartnerPackages::PARTNER_PACKAGE_DEVELOPER_PAYG);
		$partner->save();

		$childPartners = PartnerPeer::retrieveChildsOfPartner($partner->getId());
		foreach ($childPartners as $childPartner)
		{
			PermissionPeer::disableForPartner(PermissionName::FEATURE_LIMIT_ALLOWED_ACTIONS, $childPartner->getPartnerId());
			$childPartner->setPartnerPackage(PartnerPackages::PARTNER_PACKAGE_DEVELOPER_PAYG);
			$childPartner->save();
		}
	}
	
	protected function makeVendorIntegrationsPayGo($partner, $vendorIntegrations)
	{
		$chargeBeeClient = kChargeBeeUtils::getChargeBeeClient($partner->country);
		foreach ($vendorIntegrations as $vendorIntegration)
		{
			$vendorIntegration->setIsPaymentFailed(false);
			$vendorIntegration->setVendorType(KalturaVendorTypeEnum::CHARGE_BEE_PAYGO);
			$vendorIntegration->save();
			$chargeBeeClient->updateSubscriptionTrialEnd($vendorIntegration->subscriptionId, self::TRIAL_END_NOW);
		}
	}

	protected function handlePaymentFailed($data)
	{
		$vendorIntegration = $this->retrieveVendorIntegration($data->content->transaction->subscription_id, KalturaVendorTypeEnum::CHARGE_BEE_PAYGO);
		$vendorIntegration->setIsPaymentFailed(true);
		$vendorIntegration->save();
	}
	
	
	protected function handleSubscriptionTrialEndReminder($data)
	{
		$vendorIntegration = $this->retrieveVendorIntegration($data->content->subscription->id, KalturaVendorTypeEnum::CHARGE_BEE_FREE_TRIAL);
		$vendorIntegration->setStatus(KalturaVendorIntegrationStatus::DISABLED);
		$vendorIntegration->save();

		$partnerId = $this->retrievePartnerIdByVendorIntegration($vendorIntegration);
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if (!$partner)
		{
			throw new KalturaAPIException(KalturaChargeBeeErrors::FAILED_RETRIEVING_PARTNER);
		}
		$this->blockPartner($partner);
	}

	protected function blockPartner($partner)
	{
		$partner->setStatus(KalturaPartnerStatus::FULL_BLOCK);
		$partner->save();

		$childPartners = PartnerPeer::retrieveChildsOfPartner($partner->getId());
		foreach ($childPartners as $childPartner)
		{
			$childPartner->setStatus(KalturaPartnerStatus::FULL_BLOCK);
			$childPartner->save();
		}
	}
	
	
	protected function handleSubscriptionCancelled($data)
	{
		list($vendorIntegrations, $partnerId, $vendorIntegrationFree) = $this->getVendorIntegrationsToCancel($data->content->subscription->id);
		if ($vendorIntegrationFree || count($vendorIntegrations) == 1)
		{
			$this->changeVendorIntegrationStatus($vendorIntegrations, KalturaVendorIntegrationStatus::ERROR);
			$partner = PartnerPeer::retrieveByPK($partnerId);
			if (!$partner)
			{
				throw new KalturaAPIException(KalturaChargeBeeErrors::FAILED_RETRIEVING_PARTNER);
			}
			$this->makePartnerReadOnly($partner);
		}
		else
		{
			$this->changeVendorIntegrationStatus($vendorIntegrations, KalturaVendorIntegrationStatus::ERROR);
		}
	}

	protected function makePartnerReadOnly($partner)
	{
		PermissionPeer::enableForPartner(PermissionName::FEATURE_LIMIT_ALLOWED_ACTIONS, PermissionType::SPECIAL_FEATURE, $partner->getId());
		$partner->setStatus(KalturaPartnerStatus::READ_ONLY);
		$partner->save();

		$childPartners = PartnerPeer::retrieveChildsOfPartner($partner->getId());
		foreach ($childPartners as $childPartner)
		{
			PermissionPeer::enableForPartner(PermissionName::FEATURE_LIMIT_ALLOWED_ACTIONS, PermissionType::SPECIAL_FEATURE, $childPartner->getPartnerId());
			$childPartner->setStatus(KalturaPartnerStatus::READ_ONLY);
			$childPartner->save();
		}
	}

	protected function changeVendorIntegrationStatus($vendorIntegrations, $status)
	{
		foreach ($vendorIntegrations as $vendorIntegration)
		{
			$vendorIntegration->setStatus($status);
			$vendorIntegration->save();
		}
	}

	protected function getVendorIntegrationsToCancel($subscriptionId)
	{
		$partnerId = null;
		$type = null;

		$vendorIntegrationFreeTrial = VendorIntegrationPeer::retrieveSingleVendorPerPartner($subscriptionId, KalturaVendorTypeEnum::CHARGE_BEE_FREE_TRIAL);
		if ($vendorIntegrationFreeTrial)
		{
			$partnerId = $vendorIntegrationFreeTrial->getPartnerId();
			$type = KalturaVendorTypeEnum::CHARGE_BEE_FREE_TRIAL;
		}
		else
		{
			$vendorIntegrationPayGo = VendorIntegrationPeer::retrieveSingleVendorPerPartner($subscriptionId, KalturaVendorTypeEnum::CHARGE_BEE_PAYGO);
			if ($vendorIntegrationPayGo)
			{
				$partnerId = $vendorIntegrationPayGo->getPartnerId();
				$type = KalturaVendorTypeEnum::CHARGE_BEE_PAYGO;
			}
		}

		$vendorIntegrations = VendorIntegrationPeer::retrieveVendorsByPartnerAndType($partnerId, $type);
		return array($vendorIntegrations, $partnerId, $vendorIntegrationFreeTrial);
	}
	
	
	protected function handlePendingInvoiceCreated($data)
	{
		$vendorIntegration = $this->retrieveVendorIntegration($data->content->invoice->subscription_id, KalturaVendorTypeEnum::CHARGE_BEE_PAYGO);
		if (!$data->content->invoice->id)
		{
			throw new KalturaAPIException(KalturaChargeBeeErrors::MISSING_INVOICE_ID);
		}
		$vendorIntegration->setInvoiceId($data->content->invoice->id);
		$vendorIntegration->save();
	}
	
	
	protected function retrieveVendorIntegration($id, $vendorType)
	{
		if (!$id)
		{
			throw new KalturaAPIException(KalturaChargeBeeErrors::MISSING_SUBSCRIPTION_ID);
		}
		
		$vendorIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartner($id, $vendorType);
		if (!$vendorIntegration)
		{
			throw new KalturaAPIException(KalturaChargeBeeErrors::FAILED_RETRIEVING_VENDOR_INTEGRATION);
		}
		
		return $vendorIntegration;
	}
	
	
	protected function retrievePartnerIdByVendorIntegration($vendorIntegration)
	{
		$partnerId = $vendorIntegration->getPartnerId();
		if (!$partnerId)
		{
			throw new KalturaAPIException(KalturaChargeBeeErrors::FAILED_RETRIEVING_PARTNER);
		}
		
		return $partnerId;
	}

}
