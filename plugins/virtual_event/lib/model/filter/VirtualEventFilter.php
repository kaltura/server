<?php

class VirtualEventFilter extends baseObjectFilter
{
	public function init()
	{
		$this->fields = kArray::makeAssociativeDefaultValue(array(
			'_eq_id',
			'_in_id',
			'_notin_id',
			'_eq_partner_id',
			'_in_partner_id',
			'_like_name',
			'_mlikeor_name',
			'_mlikeand_name',
			'_eq_name',
			'_in_name',
			'_like_description',
			'_mlikeor_description',
			'_mlikeand_description',
			'_eq_description',
			'_eq_status',
			'_in_status',
			'_like_tags',
			'_mlikeor_tags',
			'_mlikeand_tags',
			'_eq_tags',
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