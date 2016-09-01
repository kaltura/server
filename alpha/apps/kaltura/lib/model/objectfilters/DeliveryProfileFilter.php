<?php
/**
 * @package Core
 * @subpackage model.filters
 */ 
class DeliveryProfileFilter extends baseObjectFilter
{
	/* (non-PHPdoc)
	 * @see myBaseObject::init()
	 */
	public function init()
	{
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_id",
			"_in_id",
			"_in_partner_id",
			"_eq_partner_id",
			"_gte_created_at",
			"_lte_created_at",
			"_gte_updated_at",
			"_lte_updated_at",
			"_eq_system_name",
			"_in_system_name",
			"_eq_protocol",
			"_in_protocol",
			"_eq_status",
			"_in_status",
			"_eq_streamer_type",
			"_is_live",
			) , NULL );

		$this->allowed_order_fields = array ("created_at", "updated_at");

	}

	/**
	 * @return array 
	 */
	public function describe() 
	{
		return 
			array (
				"display_name" => "DeliveryProfileFilter",
				"desc" => ""
			);
	}

	/* (non-PHPdoc)
	 * @see baseObjectFilter::getFieldNameFromPeer()
	 */
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = DeliveryProfilePeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	/* (non-PHPdoc)
	 * @see baseObjectFilter::getIdFromPeer()
	 */
	public function getIdFromPeer (  )
	{
		return DeliveryProfilePeer::ID;
	}
	
	public function attachToFinalCriteria(Criteria $c)
	{
		$filterIsLive = $this->get('_is_live');
		
		if(!is_null($filterIsLive) && $filterIsLive == true)
		{
			$c->add(DeliveryProfilePeer::TYPE, DeliveryProfilePeer::getAllLiveDeliveryProfileTypes(), Criteria::IN);
		}
		else if(!is_null($filterIsLive) && $filterIsLive == false)
		{
			$c->add(DeliveryProfilePeer::TYPE, DeliveryProfilePeer::getAllLiveDeliveryProfileTypes(), Criteria::NOT_IN);
		}
		
		$this->unsetByName('_is_live');
		return parent::attachToFinalCriteria($c);
	}
}