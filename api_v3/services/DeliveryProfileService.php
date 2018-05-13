<?php

/**
 * delivery service is used to control delivery objects
 *
 * @service deliveryProfile
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
		$delivery->fromObject($dbKalturaDelivery, $this->getResponseProfile());
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
		DeliveryProfilePeer::setUseCriteriaFilter(false);
		$dbDelivery = DeliveryProfilePeer::retrieveByPK($id);
		DeliveryProfilePeer::setUseCriteriaFilter(true);
		if (!$dbDelivery)
			throw new KalturaAPIException(KalturaErrors::DELIVERY_ID_NOT_FOUND, $id);
		
		// Don't allow to update default delivery profiles from the outside
		if($dbDelivery->getIsDefault())
			throw new KalturaAPIException(KalturaErrors::DELIVERY_UPDATE_ISNT_ALLOWED, $id);
		
		$delivery->toUpdatableObject($dbDelivery);
		$dbDelivery->save();
		
		$delivery = KalturaDeliveryProfileFactory::getDeliveryProfileInstanceByType($dbDelivery->getType());
		$delivery->fromObject($dbDelivery, $this->getResponseProfile());
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
		DeliveryProfilePeer::setUseCriteriaFilter(false);
		$dbDelivery = DeliveryProfilePeer::retrieveByPK($id);
		DeliveryProfilePeer::setUseCriteriaFilter(true);
		
		if (!$dbDelivery)
			throw new KalturaAPIException(KalturaErrors::DELIVERY_ID_NOT_FOUND, $id);
			
		$delivery = KalturaDeliveryProfileFactory::getDeliveryProfileInstanceByType($dbDelivery->getType());
		$delivery->fromObject($dbDelivery, $this->getResponseProfile());
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
		$dbKalturaDelivery = $dbDelivery->cloneToNew ( $dbKalturaDelivery );
		
		$delivery = KalturaDeliveryProfileFactory::getDeliveryProfileInstanceByType($dbKalturaDelivery->getType());
		$delivery->fromObject($dbKalturaDelivery, $this->getResponseProfile());
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

		DeliveryProfilePeer::setUseCriteriaFilter(false);
		
		$c = new Criteria();
		if (($filter->idEqual || $filter->idIn) && !($filter->partnerIdEqual || $filter->idIn))
		{
			$c->add(DeliveryProfilePeer::PARTNER_ID, array(0, kCurrentContext::getCurrentPartnerId()), Criteria::IN);
		}
		$delivery->attachToCriteria($c);
		
		$totalCount = DeliveryProfilePeer::doCount($c);
		
		$pager->attachToCriteria($c);
		$dbList = DeliveryProfilePeer::doSelect($c);
		
		DeliveryProfilePeer::setUseCriteriaFilter(true);
		
		$objects = KalturaDeliveryProfileArray::fromDbArray($dbList, $this->getResponseProfile());
		$response = new KalturaDeliveryProfileListResponse();
		$response->objects = $objects;
		$response->totalCount = $totalCount;
		return $response;    
	}
}

