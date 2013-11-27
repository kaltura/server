<?php
/**
 * @package plugins.scheduledTask
 * @subpackage model.filters
 */ 
class ScheduledTaskProfileFilter extends baseObjectFilter
{
	/* (non-PHPdoc)
	 * @see baseObjectFilter::getFieldNameFromPeer()
	 */
	protected function getFieldNameFromPeer($fieldName)
	{
		$res = ScheduledTaskProfilePeer::translateFieldName($fieldName , $this->field_name_translation_type , BasePeer::TYPE_COLNAME);
		return $res;
	}

	/* (non-PHPdoc)
	 * @see baseObjectFilter::getIdFromPeer()
	 */
	protected function getIdFromPeer()
	{
		return ScheduledTaskProfilePeer::ID;
		
	}

	/* (non-PHPdoc)
	 * @see myBaseObject::init()
	 */
	protected function init()
	{
		$this->fields = kArray::makeAssociativeDefaultValue(array(
			"_eq_id",
			"_in_id",
			"_eq_partner_id",
			"_in_partner_id",
			"_eq_system_name",
			"_in_system_name",
			"_eq_status",
			"_in_status",
			"_gte_created_at",
			"_lte_created_at",
			"_gte_updated_at",
			"_lte_updated_at",	
		), NULL);
		
		$this->allowed_order_fields = array("created_at", "updated_at", "id");
	}
}