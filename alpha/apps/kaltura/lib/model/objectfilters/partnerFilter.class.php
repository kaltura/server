<?php
class partnerFilter extends baseObjectFilter
{
	public function init ()
	{
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_id",
			"_in_id",
			"_gt_id" ,
			"_eq_name",
			"_like_name",
			"_mlikeor_name",
			"_mlikeand_name",
			"_in_status",
			"_eq_status",
			"_gte_created_at",
			"_lte_created_at",
			"_like_partner_name-description-website-admin_name-admin_email",
			) , NULL );

		$this->allowed_order_fields = array ( "created_at" , "updated_at", "id", "name", "website", "admin_name", "admin_email", "status");
		
		$this->aliases = array(
			"name" => "partner_name",
			"website" => "url1"
		);
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "PartnerFilter",
				"desc" => ""
			);
	}
	
	// TODO - move to base class, all that should stay here is the peer class, not the logic of the field translation !
	// The base class should invoke $peek_class::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = PartnerPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return PartnerPeer::ID;
	}
}

?>