<?php

/**
 * @package plugins.vendor
 * @subpackage api.objects
 */
abstract class KalturaIntegrationSetting extends KalturaObject
{
	/**
	 * @var int
	 * @readonly
	 */
	public $id;

	/**
	 * @var KalturaVendorIntegrationStatus
	 * @readonly
	 */
	public $status;

	/**
	 * @var string
	 */
	public $defaultUserId;

	/**
	 * @var string
	 * @readonly
	 */
	public $accountId;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $createUserIfNotExist;

	/**
	 * @var int
	 */
	public $conversionProfileId;

	/**
	 * @var KalturaHandleParticipantsMode
	 */
	public $handleParticipantsMode;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $deletionPolicy;

	/**
	 * @var string
	 * @readonly
	 */
	public $createdAt;

	/**
	 * @var string
	 * @readonly
	 */
	public $updatedAt;

	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * @var KalturaNullableBoolean
	 */
	public $enableMeetingUpload;
	
	/**
	 * @var KalturaNullableBoolean
	 */
	public $enableMeetingChat;

	private static $map_between_objects = array
	(
		'id',
		'status',
		'accountId',
		'createUserIfNotExist',
		'handleParticipantsMode',
		'conversionProfileId',
		'defaultUserId' => 'defaultUserEMail',
		'deletionPolicy',
		'createdAt',
		'updatedAt',
		'partnerId',
		'enableMeetingUpload',
		'enableMeetingChat',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
		{
			$dbObject = new VendorIntegration();
		}

		return parent::toObject($dbObject, $skip);
	}

	/**
	 * @param $sourceObject
	 * @param KalturaDetachedResponseProfile|null $responseProfile
	 * @return KalturaIntegrationSetting|object|null
	 * @throws APIException
	 * @throws KalturaClientException
	 */
	public static function getInstance($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$object = null;
		/* @var $sourceObject VendorIntegration */

		switch ($sourceObject->getVendorType())
		{
			case VendorTypeEnum::ZOOM_ACCOUNT:
				$object = new KalturaZoomIntegrationSetting();
				break;
			
			case VendorTypeEnum::WEBEX_API_ACCOUNT:
				$object = new KalturaWebexAPIIntegrationSetting();
				break;

			default:
				$object = KalturaPluginManager::loadObject('KalturaIntegrationSetting', $sourceObject->getVendorType());
				break;
		}

		if (!$object)
		{
			throw new APIException('Integration setting for type ' .  $sourceObject->getVendorType() .' could not be instantiated.');
		}

		return $object;
	}

	public function toInsertableObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
		{
			$dbObject = new VendorIntegration();
		}
		$dbObject->setPartnerId(kCurrentContext::getCurrentPartnerId());

		return parent::toInsertableObject($dbObject,array('createdAt','updatedAt')) ;
	}
}