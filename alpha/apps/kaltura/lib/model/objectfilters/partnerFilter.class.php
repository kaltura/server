<?php
/**
 * @package Core
 * @subpackage model.filters
 */ 
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
			"_eq_commercial_use",
			"_eq_partner_package",
			"_gte_partner_package",
			"_lte_partner_package",
			"_in_partner_package",
		    "_eq_partner_group_type",
		    "_in_partner_group_type",
			"_eq_partner_parent_id",
			"_in_partner_parent_id",
		    "_notin_id",
			"_partner_permissions_exist",
			'_gte_created_at',
			'_eq_monitor_usage'
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
		
	/* (non-PHPdoc)
	 * @see baseObjectFilter::attachToFinalCriteria()
	 */
	public function attachToFinalCriteria(Criteria $criteria)
	{
	    
		if(!is_null($this->get('_partner_permissions_exist')))
		{
		    if(is_null($this->get('_in_id')))
		    {
		        $mandatoryParameter = "_in_id";
		        throw new kCoreException("Mandatory parameter $mandatoryParameter missing from the filter" ,kCoreException::MISSING_MANDATORY_PARAMETERS, $mandatoryParameter);
		    }
		    
	        $permissions = explode (',' , $this->get('_partner_permissions_exist'));
	        
	        $tmpCriteria =  new Criteria();
	        $tmpCriteria->addSelectColumn(PermissionPeer::PARTNER_ID);
	        $tmpCriteria->addAnd(PermissionPeer::NAME, $permissions,  Criteria::IN);
	        
	        $ids = explode(',', $this->get('_in_id'));
	        $tmpCriteria->addAnd(PermissionPeer::PARTNER_ID, $ids, Criteria::IN);
	        
	        $tmpCriteria->addAnd(PermissionPeer::STATUS, PermissionStatus::ACTIVE, Criteria::EQUAL);
	        $stmt = PermissionPeer::doSelectStmt($tmpCriteria);
	        $this->setIdIn($stmt->fetchAll(PDO::FETCH_COLUMN));
			
			$this->unsetByName('_partner_permissions_exist');
		}
		
		return parent::attachToFinalCriteria($criteria);
	}
	
	/* (non-PHPdoc)
	 * @see baseObjectFilter::getIdFromPeer()
	 */
	public function getIdFromPeer (  )
	{
		return PartnerPeer::ID;
	}

	/**
	 * Set filter _in_id attribute
	 *  
	 * @param array $ids
	 */
	public function setIdIn(array $ids)
	{
		$this->set('_in_id', implode(',', $ids));
	}
}

