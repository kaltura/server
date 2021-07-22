<?php
/**
 * @service chargeBeeVendor
 * @package plugins.chargeBee
 * @subpackage api.services
 */
class ChargeBeeVendorService extends KalturaBaseService
{
	const MAP_NAME = 'vendor';
	const CONFIGURATION_PARAM_NAME = 'chargeBee';
	
	protected static $PARTNER_NOT_REQUIRED_ACTIONS = array('handleNotification');
	
	const REQUEST_USER = 'PHP_AUTH_USER';
	const REQUEST_PASSWORD = 'PHP_AUTH_PW';
	
	const CONFIGURATION_USER = 'user';
	const CONFIGURATION_PASSWORD = 'password';

	const REQUEST_PAYMENT_SOURCE_ADDED = 'payment_source_added';
	const REQUEST_PAYMENT_FAILED = 'payment_failed';
	const REQUEST_SUBSCRIPTION_TRIAL_END_REMINDER = 'subscription_trial_end_reminder';
	const REQUEST_SUBSCRIPTION_CANCELED = 'subscription_canceled';
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
		$dbChargeBeeVendorIntegration->setPartnerId($chargeBeeVendorIntegration->partnerId);
		$dbChargeBeeVendorIntegration->setStatus(VendorStatus::ACTIVE);
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
		
		$chargeBeeConfiguration = self::getChargeBeeConfiguration();
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
			case self::REQUEST_SUBSCRIPTION_CANCELED:
				$this->handleSubscriptionCanceled($data);
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
		$vendorIntegration = $this->retrieveVendorIntegration($data->content->customer->id, KalturaVendorTypeEnum::CHARGE_BEE_FREE_TRIAL);
		
		$partnerId = $this->retrievePartnerIdByVendorIntegration($vendorIntegration);
		
		PermissionPeer::disableForPartner(PermissionName::FEATURE_LIMIT_ALLOWED_ACTIONS, $partnerId);
		
		$childPartners = PartnerPeer::retrieveChildsOfPartner($partnerId);
		foreach ($childPartners as $childPartner)
		{
			PermissionPeer::disableForPartner(PermissionName::FEATURE_LIMIT_ALLOWED_ACTIONS, $childPartner->getPartnerId());
		}
		
		$vendorIntegration->setVendorType(KalturaVendorTypeEnum::CHARGE_BEE_FREE_PAYGO);
		
		$partner = PartnerPeer::retrieveByPK($partnerId);
		list($chargeBeeConfMap, $site, $siteApiKey) = kChargeBeeUtils::getSiteConfig($partner->country);
		
		$chargeBeeClient = new kChargeBeeClient($site, $siteApiKey);
		$chargeBeeClient->updateSubscriptionTrialEnd($data->content->customer->id, 0);
	}
	
	
	protected function handlePaymentFailed($data)
	{
		$vendorIntegration = $this->retrieveVendorIntegration($data->content->customer->id, KalturaVendorTypeEnum::CHARGE_BEE_FREE_PAYGO);
		
		$partnerId = $this->retrievePartnerIdByVendorIntegration($vendorIntegration);
		
		$partner = PartnerPeer::retrieveByPK($partnerId);
		
		PermissionPeer::enableForPartner(FEATURE_LIMIT_ALLOWED_ACTIONS, PermissionType::SPECIAL_FEATURE, $partnerId);
		$partner->setStatus(KalturaPartnerStatus::READ_ONLY);
		$partner->save();
		
		$childPartners = PartnerPeer::retrieveChildsOfPartner($partnerId);
		foreach ($childPartners as $childPartner)
		{
			PermissionPeer::enableForPartner(PermissionName::FEATURE_LIMIT_ALLOWED_ACTIONS, $childPartner->getPartnerId());
			$childPartner->setStatus(KalturaPartnerStatus::READ_ONLY);
			$childPartner->save();
		}
	}
	
	
	protected function handleSubscriptionTrialEndReminder($data)
	{
		$vendorIntegration = $this->retrieveVendorIntegration($data->id, KalturaVendorTypeEnum::CHARGE_BEE_FREE_PAYGO);
		
		$partnerId = $this->retrievePartnerIdByVendorIntegration($vendorIntegration);
		
		$partner = PartnerPeer::retrieveByPK($partnerId);
		
		$partner->setStatus(KalturaPartnerStatus::FULL_BLOCK);
		$partner->save();
		
		$childPartners = PartnerPeer::retrieveChildsOfPartner($partnerId);
		foreach ($childPartners as $childPartner)
		{
			$childPartner->setStatus(KalturaPartnerStatus::FULL_BLOCK);
			$childPartner->save();
		}
	}
	
	
	protected function handleSubscriptionCanceled($data)
	{
		$vendorIntegration = $this->retrieveVendorIntegration($data->content->customer->id, KalturaVendorTypeEnum::CHARGE_BEE_FREE_PAYGO);
		
		$partnerId = $this->retrievePartnerIdByVendorIntegration($vendorIntegration);
		
		PermissionPeer::enableForPartner(FEATURE_LIMIT_ALLOWED_ACTIONS, $partnerId);
		
		$childPartners = PartnerPeer::retrieveChildsOfPartner($partnerId);
		foreach ($childPartners as $childPartner)
		{
			PermissionPeer::enableForPartner(PermissionName::FEATURE_LIMIT_ALLOWED_ACTIONS, $childPartner->getPartnerId());
		}
	}
	
	
	protected function handlePendingInvoiceCreated($data)
	{
		$vendorIntegration = $this->retrieveVendorIntegration($data->content->customer->id, KalturaVendorTypeEnum::CHARGE_BEE_FREE_PAYGO);
		
		if (!$data->content->transaction->linked_invoices[0]->invoice_id)
		{
			throw new KalturaAPIException(KalturaChargeBeeErrors::MISSING_INVOICE_ID);
		}
		
		$vendorIntegration->setInvoiceId($data->content->transaction->linked_invoices[0]->invoice_id);
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
			throw new KalturaAPIException(KalturaChargeBeeErrors::FAILED_RETRIEVING_PARTNER);
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
	

	/**
	 * @return array
	 * @throws KalturaAPIException
	 * @throws Exception
	 */
	public static function getChargeBeeConfiguration()
	{
		if(!kConf::hasMap(self::MAP_NAME))
		{
			throw new KalturaAPIException(KalturaChargeBeeErrors::NO_VENDOR_CONFIGURATION);
		}
		return kConf::get(self::CONFIGURATION_PARAM_NAME, self::MAP_NAME);
	}
}
