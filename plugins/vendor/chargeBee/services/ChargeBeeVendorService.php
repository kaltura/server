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
	 * @action handle
	 * @return KalturaChargeBeeVendorIntegration
	 * @throws KalturaChargeBeeErrors::CHARGE_BEE_VENDOR_INTEGRATION_NOT_FOUND
	 */
	public function handleNotificationAction()
	{
		if (!isset($_SERVER['PHP_AUTH_USER']) or !isset($_SERVER['PHP_AUTH_PW']))
		{
			throw new KalturaAPIException(KalturaChargeBeeErrors::UNAUTHORIZED_USER_PASSWORD);
		}
		
		$chargeBeeConfiguration = self::getChargeBeeConfiguration();
		if (!isset($chargeBeeConfiguration['user']) or !isset($chargeBeeConfiguration['password']))
		{
			throw new KalturaAPIException(KalturaChargeBeeErrors::MISSING_USER_PASSWORD_CONFIGURATION);
		}
		
		if ($_SERVER['PHP_AUTH_USER'] != $chargeBeeConfiguration['user'] or $_SERVER['PHP_AUTH_PW'] != $chargeBeeConfiguration['password'])
		{
			throw new KalturaAPIException(KalturaChargeBeeErrors::UNAUTHORIZED_USER_PASSWORD);
		}
		
		$eventType = $this->getPostData();
	}
	
	protected function getPostData()
	{
		$data = json_decode(file_get_contents('php://input'));
		
		if (!isset($data) or !isset($data->event_type))
		{
			throw new KalturaAPIException(KalturaChargeBeeErrors::MISSING_EVENT_TYPE);
		}
		$eventType = $data->event_type;
		if ($eventType == 'payment_source_added')
		{
			return $eventType;
		}
		else if ($eventType == 'payment_failed')
		{
			return $eventType;
		}
		else if ($eventType == 'subscription_trial_end_reminder')
		{
			return $eventType;
		}
		else
		{
			throw new KalturaAPIException(KalturaChargeBeeErrors::MISSING_EVENT_TYPE);
		}
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
