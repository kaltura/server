<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model.filters
 */
class GenericDistributionProviderActionFilter extends baseObjectFilter
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
			"_eq_generic_distribution_provider_id",
			"_in_generic_distribution_provider_id",
			"_eq_action",
			"_in_action",
			) , null );

		$this->allowed_order_fields = array ("created_at" , "updated_at");
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "GenericDistributionProviderActionFilter",
				"desc" => ""
			);
	}
	
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = GenericDistributionProviderActionPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return GenericDistributionProviderActionPeer::ID;
	}
}
