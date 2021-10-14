<?php

class VirtualEventFilter extends baseObjectFilter
{
	public function init()
	{
		$this->fields = kArray::makeAssociativeDefaultValue(array(
			'_eq_partner_id',
			'_in_partner_id',
			'_eq_name',
			'_in_name',
			'_eq_status',
			'_in_status',
			'_eq_attendees_group_id',
			'_in_attendees_group_id',
			'_eq_admins_group_id',
			'_in_admins_group_id',
			'_eq_registration_schedule_event_id',
			'_in_registration_schedule_event_id',
			'_eq_agenda_schedule_event_id',
			'_in_agenda_schedule_event_id',
			'_eq_event_schedule_event_id',
			'_in_event_schedule_event_id',
			'_gte_created_at',
			'_lte_created_at',
			'_gte_updated_at',
			'_lte_updated_at',
			'_gte_deletion_due_date',
			'_lte_deletion_due_date',
		), null);
		
		$this->allowed_order_fields = array(
		);
	}
	
	public function getFieldNameFromPeer($field_name)
	{
		return VirtualEventPeer::translateFieldName($field_name, $this->field_name_translation_type, BasePeer::TYPE_COLNAME);
	}
	public function getIdFromPeer()
	{
		return VirtualEventPeer::ID;
	}
}