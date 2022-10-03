<?php


class ResourceUserFilter extends baseObjectFilter
{

	public function init ()
	{
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			'_eq_resource_tag',
			'_in_resource_tag',
			'_eq_user_id',
			'_in_user_id',
			'_eq_status',
			'_in_status',
			'_gte_created_at',
			'_lte_created_at',
			'_gte_updated_at',
			'_lte_updated_at',
		) , NULL );

		$this->allowed_order_fields = array (
			"created_at",
			"updated_at",
		);

		$this->aliases = array (

		);
	}

	public function describe()
	{
		return
			array (
				"display_name" => "ScheduleEvent",
				"desc" => ""
			);
	}

	public function getFieldNameFromPeer($field_name)
	{
		return ResourceUserPeer::translateFieldName($field_name, $this->field_name_translation_type, BasePeer::TYPE_COLNAME);
	}

	protected function getIdFromPeer()
	{
		return ResourceUserPeer::ID;
	}
}