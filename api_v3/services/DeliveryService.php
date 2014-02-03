<?php

/**
 * delivery service is used to control delivery objects
 *
 * @service delivery
 * @package api
 * @subpackage services
 */
class DeliveryService extends KalturaBaseService
{
	
	/**
	 * Add new delivery.
	 *
	 * @action add
	 * @param KalturaDelivery $delivery
	 * @return KalturaDelivery
	 */
	function addAction(KalturaDelivery $delivery)
	{
		$dbKalturaDelivery = $delivery->toInsertableObject();
		$dbKalturaDelivery->setPartnerId($this->getPartnerId());
		$dbKalturaDelivery->setParentId(0);
		$dbKalturaDelivery->save();
		
		$delivery = KalturaDeliveryFactory::getDeliveryInstanceByType($dbKalturaDelivery->getType());
		$delivery->fromObject($dbKalturaDelivery);
		return $delivery;
	}
	
	/**
	 * Update exisiting delivery
	 *
	 * @action update
	 * @param string $id
	 * @param KalturaDelivery $widget
	 * @return KalturaDelivery
	 */
	function updateAction( $id , KalturaDelivery $delivery )
	{
		$dbDelivery = DeliveryPeer::retrieveByPK($id);
		if (!$dbDelivery)
			throw new KalturaAPIException(KalturaErrors::DELIVERY_ID_NOT_FOUND, $id);
		
		$delivery->toUpdatableObject($dbDelivery);
		$dbDelivery->save();
		
		$delivery = KalturaDeliveryFactory::getDeliveryInstanceByType($dbDelivery->getType());
		$delivery->fromObject($dbDelivery);
		return $delivery;
	}
	
	/**
	* Get delivery by id
	*
	* @action get
	* @param string $id
	* @return KalturaDelivery
	*/
	function getAction( $id )
	{
		$dbDelivery = DeliveryPeer::retrieveByPK($id);
		if (!$dbDelivery)
			throw new KalturaAPIException(KalturaErrors::DELIVERY_ID_NOT_FOUND, $id);
			
		$delivery = KalturaDeliveryFactory::getDeliveryInstanceByType($dbDelivery->getType());
		$delivery->fromObject($dbDelivery);
		return $delivery;
	}
	
	/**
	* Add delivery based on existing delivery.
	* Must provide valid sourceDeliveryId
	*
	* @action clone
	* @param int $deliveryId
	* @return KalturaDelivery
	*/
	function cloneAction( $deliveryId )
	{
		$dbDelivery = DeliveryPeer::retrieveByPK( $deliveryId );
		
		if ( ! $dbDelivery )
			throw new KalturaAPIException ( APIErrors::DELIVERY_ID_NOT_FOUND , $deliveryId );
		
		$className = get_class($dbDelivery);
		$class = new ReflectionClass($className);
		$dbKalturaDelivery = $class->newInstanceArgs(array());
		$dbKalturaDelivery->setParentId($deliveryId);
		
		$dbKalturaDelivery = $dbDelivery->cloneToNew ( $dbKalturaDelivery );
		
		$delivery = KalturaDeliveryFactory::getDeliveryInstanceByType($dbKalturaDelivery->getType());
		$delivery->fromObject($dbKalturaDelivery);
		return $delivery;
	}
	
	/**
	* Retrieve a list of available delivery depends on the filter given
	*
	* @action list
	* @param KalturaDeliveryFilter $filter
	* @param KalturaFilterPager $pager
	* @return KalturaDeliveryListResponse
	*/
	function listAction( KalturaDeliveryFilter $filter=null , KalturaFilterPager $pager=null)
	{
		if (!$filter)
			$filter = new KalturaDeliveryFilter();

		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$delivery = new DeliveryFilter();
		$filter->toObject($delivery);

		$c = new Criteria();
		$delivery->attachToCriteria($c);
		
		$totalCount = DeliveryPeer::doCount($c);
		
		$pager->attachToCriteria($c);
		$dbList = DeliveryPeer::doSelect($c);
		
		$list = KalturaDeliveryArray::fromDbArray($dbList);
		$response = new KalturaDeliveryListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;    
	}
}

