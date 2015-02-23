<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage model.filters
 */ 
class BusinessProcessServerFilter extends baseObjectFilter
{
	/* (non-PHPdoc)
	 * @see baseObjectFilter::getFieldNameFromPeer()
	 */
	protected function getFieldNameFromPeer($field_name) 
	{
		$res = BusinessProcessServerPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
		
	}

	/* (non-PHPdoc)
	 * @see baseObjectFilter::getIdFromPeer()
	 */
	protected function getIdFromPeer()
	{
		return BusinessProcessServerPeer::ID;
		
	}

	/* (non-PHPdoc)
	 * @see myBaseObject::init()
	 */
	protected function init() {
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_id",
			"_in_id",
			"_notin_id",
			"_gte_created_at",
			"_lte_created_at",
			"_gte_updated_at",
			"_lte_updated_at",
			"_eq_partner_id",
			"_in_partner_id",
			"_eq_status",
			"_not_status",
			"_in_status",
			"_notin_status",
			"_eq_type",
			"_in_type",
		), NULL);	
		
		$this->allowed_order_fields = array ("created_at" , "updated_at");
	}

	
}