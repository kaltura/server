<?php
/**
 * @package plugins.sso
 * @subpackage model.filters
 */
class SsoFilter extends baseObjectFilter
{
	public function init()
	{
		$this->fields = kArray::makeAssociativeDefaultValue(array(
			'_eq_id',
			'_in_id',
			'_eq_application_type',
			'_eq_partner_id',
			'_eq_domain',
			'_eq_status',
			'_in_status',
			'_gte_created_at',
			'_lte_created_at',
		), null);

		$this->allowed_order_fields = array('id','created_at');
	}

	public function getFieldNameFromPeer($field_name)
	{
		return SsoPeer::translateFieldName($field_name, $this->field_name_translation_type, BasePeer::TYPE_COLNAME);
	}
	public function getIdFromPeer()
	{
		return SsoPeer::ID;
	}
}