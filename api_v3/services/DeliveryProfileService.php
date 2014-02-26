<?php

/**
 * delivery service is used to control delivery objects
 *
 * @service delivery
 * @package api
 * @subpackage services
 */
class DeliveryProfileService extends KalturaBaseService
{
	
	/**
	 * Add new delivery.
	 *
	 * @action add
	 * @param KalturaDeliveryProfile $delivery
	 * @return KalturaDeliveryProfile
	 */
	function addAction(KalturaDeliveryProfile $delivery)
	{
		$dbKalturaDelivery = $delivery->toInsertableObject();
		$dbKalturaDelivery->setPartnerId($this->getPartnerId());
		$dbKalturaDelivery->setParentId(0);
		$dbKalturaDelivery->save();
		
		$delivery = KalturaDeliveryProfileFactory::getDeliveryProfileInstanceByType($dbKalturaDelivery->getType());
		$delivery->fromObject($dbKalturaDelivery);
		return $delivery;
	}
	
	/**
	 * Update exisiting delivery
	 *
	 * @action update
	 * @param string $id
	 * @param KalturaDeliveryProfile $delivery
	 * @return KalturaDeliveryProfile
	 */
	function updateAction( $id , KalturaDeliveryProfile $delivery )
	{
		$dbDelivery = DeliveryProfilePeer::retrieveByPK($id);
		if (!$dbDelivery)
			throw new KalturaAPIException(KalturaErrors::DELIVERY_ID_NOT_FOUND, $id);
		
		$delivery->toUpdatableObject($dbDelivery);
		$dbDelivery->save();
		
		$delivery = KalturaDeliveryProfileFactory::getDeliveryProfileInstanceByType($dbDelivery->getType());
		$delivery->fromObject($dbDelivery);
		return $delivery;
	}
	
	/**
	* Get delivery by id
	*
	* @action get
	* @param string $id
	* @return KalturaDeliveryProfile
	*/
	function getAction( $id )
	{
		$dbDelivery = DeliveryProfilePeer::retrieveByPK($id);
		if (!$dbDelivery)
			throw new KalturaAPIException(KalturaErrors::DELIVERY_ID_NOT_FOUND, $id);
			
		$delivery = KalturaDeliveryProfileFactory::getDeliveryProfileInstanceByType($dbDelivery->getType());
		$delivery->fromObject($dbDelivery);
		return $delivery;
	}
	
	/**
	* Add delivery based on existing delivery.
	* Must provide valid sourceDeliveryId
	*
	* @action clone
	* @param int $deliveryId
	* @return KalturaDeliveryProfile
	*/
	function cloneAction( $deliveryId )
	{
		$dbDelivery = DeliveryProfilePeer::retrieveByPK( $deliveryId );
		
		if ( ! $dbDelivery )
			throw new KalturaAPIException ( APIErrors::DELIVERY_ID_NOT_FOUND , $deliveryId );
		
		$className = get_class($dbDelivery);
		$class = new ReflectionClass($className);
		$dbKalturaDelivery = $class->newInstanceArgs(array());
		$dbKalturaDelivery->setParentId($deliveryId);
		
		$dbKalturaDelivery = $dbDelivery->cloneToNew ( $dbKalturaDelivery );
		
		$delivery = KalturaDeliveryProfileFactory::getDeliveryProfileInstanceByType($dbKalturaDelivery->getType());
		$delivery->fromObject($dbKalturaDelivery);
		return $delivery;
	}
	
	/**
	* Retrieve a list of available delivery depends on the filter given
	*
	* @action list
	* @param KalturaDeliveryProfileFilter $filter
	* @param KalturaFilterPager $pager
	* @return KalturaDeliveryProfileListResponse
	*/
	function listAction( KalturaDeliveryProfileFilter $filter=null , KalturaFilterPager $pager=null)
	{
		if (!$filter)
			$filter = new KalturaDeliveryProfileFilter();

		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$delivery = new DeliveryProfileFilter();
		$filter->toObject($delivery);

		$c = new Criteria();
		$delivery->attachToCriteria($c);
		
		$totalCount = DeliveryProfilePeer::doCount($c);
		
		$pager->attachToCriteria($c);
		$dbList = DeliveryProfilePeer::doSelect($c);
		
		$list = KalturaDeliveryProfileArray::fromDbArray($dbList);
		$response = new KalturaDeliveryProfileListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;    
	}
}

