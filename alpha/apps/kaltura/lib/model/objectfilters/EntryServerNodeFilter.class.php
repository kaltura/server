<?php
/**
 * @package Core
 * @subpackage model.filters
 */
class EntryServerNodeFilter extends baseObjectFilter {

	/* (non-PHPdoc)
	 * @see myBaseObject::init()
	 */
	public function init()
	{
		$this->fields = kArray::makeAssociativeDefaultValue(array(
			'_gte_created_at',
			'_lte_created_at',
			'_gte_updated_at',
			'_lte_updated_at',
			'_eq_entry_id',
			'_in_entry_id',
			'_eq_server_node_id',
			'_eq_status',
			'_in_status',
			'_eq_server_type',
			'_in_server_type',
		), null);

		$this->allowed_order_fields = array(
			'created_at',
			'updated_at'
		);
	}

	/**
	 * @return array
	 */
	public function describe()
	{
		return array(
			'display_name' => 'EntryServerNodeFilter',
			'desc' => ''
		);
	}

	/* (non-PHPdoc)
	 * @see baseObjectFilter::getFieldNameFromPeer()
	 */
	public function getFieldNameFromPeer($field_name)
	{
		$res = EntryServerNodePeer::translateFieldName($field_name, $this->field_name_translation_type, BasePeer::TYPE_COLNAME);
		return $res;
	}

	/* (non-PHPdoc)
	 * @see baseObjectFilter::getIdFromPeer()
	 */
	public function getIdFromPeer()
	{
		return EntryServerNodePeer::ID;
	}

}