<?php
/**
 * @package Core
 * @subpackage model.filters
 */
class categoryEntryFilter extends baseObjectFilter
{
	public function init ()
	{
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_category_id",
			"_in_category_id",
			"_eq_entry_id",
			"_in_entry_id",
			"_gte_created_at",
			"_lte_created_at",
			"_likex_category_full_ids",
			"_eq_status",
			"_in_status",
			) , NULL );

		$this->allowed_order_fields = array (
			'created_at',
		);

		$this->aliases = array ();
	}

	public function describe()
	{
		return
			array (
				"display_name" => "CategoryEntryFilter",
				"desc" => ""
			);
	}

	// TODO - move to base class, all that should stay here is the peer class, not the logic of the field translation !
	// The base class should invoke $peek_class::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = categoryEntryPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return categoryEntryPeer::ID;
	}

	public function setCategoryIdEqual($v)
	{
		$this->set('_eq_category_id', $v);
	}

	public function setEntryIdEqual($v)
	{
		$this->set('_eq_entry_id', $v);
	}

	public function setFullIdsStartsWith($v)
	{
		$this->set('_likex_category_full_ids', $v);
	}
}