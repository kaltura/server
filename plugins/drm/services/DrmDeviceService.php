<?php
/**
 * 
 * @service drmDevice
 * @package plugins.drm
 * @subpackage api.services
 */
class DrmDeviceService extends KalturaBaseService
{	
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		if (!DrmPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
			
		$this->applyPartnerFilterForClass('DrmDevice');
	}
	
	/**
	 * Allows you to add a new DrmDevice in Pending status
	 * 
	 * @action add
	 * @param KalturaDrmDevice $drmDevice
	 * @return KalturaDrmDevice
	 * 
	 * @throws KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL
	 */
	public function addAction(KalturaDrmDevice $drmDevice)
	{
		// check for required parameters
		$drmDevice->validatePropertyNotNull('name');
		$drmDevice->validatePropertyNotNull('provider');
		$drmDevice->validatePropertyNotNull('deviceId');
		$drmDevice->validatePropertyNotNull('partnerId');
		
		
		if (!PartnerPeer::retrieveByPK($drmDevice->partnerId)) {
			throw new KalturaAPIException(KalturaErrors::INVALID_PARTNER_ID, $drmDevice->partnerId);
		}
		
		if (!DrmPlugin::isAllowedPartner($drmDevice->partnerId))
		{
			throw new KalturaAPIException(KalturaErrors::PLUGIN_NOT_AVAILABLE_FOR_PARTNER, DrmPlugin::getPluginName(), $drmDevice->partnerId);
		}
				
		// save in database
		$dbDrmDevice = $drmDevice->toInsertableObject();
		$dbDrmDevice->setStatus(DrmDeviceStatus::PENDING);
		$dbDrmDevice->save();
		
		// return the saved object
		$drmDevice = KalturaDrmDevice::getInstanceByType($dbDrmDevice->getProvider());
		$drmDevice->fromObject($dbDrmDevice, $this->getResponseProfile());
		return $drmDevice;
		
	}
	
	/**
	 * Retrieve a DrmDevice object by ID
	 * 
	 * @action get
	 * @param int $drmDeviceId 
	 * @return KalturaDrmDevice
	 * 
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	public function getAction($drmDeviceId)
	{
		$dbDrmDevice = DrmDevicePeer::retrieveByPK($drmDeviceId);
		
		if (!$dbDrmDevice) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $drmDeviceId);
		}
			
		$drmDevice = KalturaDrmDevice::getInstanceByType($dbDrmDevice->getProvider());
		$drmDevice->fromObject($dbDrmDevice, $this->getResponseProfile());
		
		return $drmDevice;
	}
	

	/**
	 * Update an existing KalturaDrmDevice object
	 * 
	 * @action update
	 * @param int $drmDeviceId
	 * @param KalturaDrmDevice $drmDevice
	 * @return KalturaDrmDevice
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */	
	public function updateAction($drmDeviceId, KalturaDrmDevice $drmDevice)
	{
		$dbDrmDevice = DrmDevicePeer::retrieveByPK($drmDeviceId);
		
		if (!$dbDrmDevice) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $drmDeviceId);
		}
							
		$dbDrmDevice = $drmDevice->toUpdatableObject($dbDrmDevice);
		$dbDrmDevice->save();
	
		$drmDevice = KalturaDrmDevice::getInstanceByType($dbDrmDevice->getProvider());
		$drmDevice->fromObject($dbDrmDevice, $this->getResponseProfile());
		
		return $drmDevice;
	}

	/**
	 * Mark the KalturaDrmDevice object as deleted
	 * 
	 * @action delete
	 * @param int $drmDeviceId 
	 * @return KalturaDrmDevice
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	public function deleteAction($drmDeviceId)
	{
		$dbDrmDevice = DrmDevicePeer::retrieveByPK($drmDeviceId);
		
		if (!$dbDrmDevice) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $drmDeviceId);
		}

		$dbDrmDevice->setStatus(DrmDeviceStatus::DELETED);
		$dbDrmDevice->save();
			
		$drmDevice = KalturaDrmDevice::getInstanceByType($dbDrmDevice->getProvider());
		$drmDevice->fromObject($dbDrmDevice, $this->getResponseProfile());
		
		return $drmDevice;
	}
	
	/**
	 * List KalturaDrmDevice objects
	 * 
	 * @action list
	 * @param KalturaDrmDeviceFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaDrmDeviceListResponse
	 */
	public function listAction(KalturaDrmDeviceFilter  $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaDrmDeviceFilter();
			
		$drmDeviceFilter = $filter->toObject();

		$c = new Criteria();
		$drmDeviceFilter->attachToCriteria($c);
		$count = DrmDevicePeer::doCount($c);		
		if (! $pager)
			$pager = new KalturaFilterPager ();
		$pager->attachToCriteria ( $c );
		$list = DrmDevicePeer::doSelect($c);
		
		$response = new KalturaDrmDeviceListResponse();
		$response->objects = KalturaDrmDeviceArray::fromDbArray($list, $this->getResponseProfile());
		$response->totalCount = $count;
		
		return $response;
	}

}
