<?php

class ConfMapsFilter extends baseObjectFilter
{
	public function init()
	{
		$this->fields = kArray::makeAssociativeDefaultValue(array(
			'_eq_map_name',
			'_eq_host_name',
			'_eq_version'
		), null);

		$this->allowed_order_fields = array(
		);
	}

	public function getFieldNameFromPeer($field_name)
	{
		return ScheduleEventResourcePeer::translateFieldName($field_name, $this->field_name_translation_type, BasePeer::TYPE_COLNAME);
	}
	public function getIdFromPeer()
	{
		return ConfMapsPeer::ID;
	}
}