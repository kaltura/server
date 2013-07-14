<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model.filters
 */
class GenericDistributionProviderFilter extends baseObjectFilter
{
	public function init ()
	{
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_id",
			"_in_id",
			"_gte_created_at",
			"_lte_created_at",
			"_gte_updated_at",
			"_lte_updated_at",
			"_eq_partner_id",
			"_in_partner_id",
			"_eq_is_default",
			"_in_is_default",
			"_eq_status",
			"_in_status",
			) , null );

		$this->allowed_order_fields = array ("created_at" , "updated_at");
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "GenericDistributionProviderFilter",
				"desc" => ""
			);
	}
	
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = GenericDistributionProviderPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return GenericDistributionProviderPeer::ID;
	}
}
