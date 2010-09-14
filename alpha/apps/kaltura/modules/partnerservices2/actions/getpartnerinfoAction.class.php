<?php
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

class getpartnerinfoAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "getPartnerInfo",
				"desc" => "Get the data of a partner by partner_id." ,
				"in" => array (
					"mandatory" => array (
						"partner_id" 		=> array ("type" => "integer", "desc" => ""),
						),
					"optional" => array (
						"detailed" => array ("type" => "string", "desc" => ""),
						)
					),
				"out" => array (
					"partner" => array ("type" => "Partner", "desc" => ""),
					),
				"errors" => array (
				 	APIErrors::UNKNOWN_PARTNER_ID,
				 	
				)
			);
	}

		
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		// make sure the secret fits the one in the partner's table
		$detailed = trim ( $this->getP ( "detailed" , "true" , true )); 		
		if ( $detailed === "0" || $detailed === "false" ) $detailed = false;
		
		$partner = PartnerPeer::retrieveByPK( $partner_id );
		
		if ( ! $partner )
		{
			// CANNOT be because we are already in the service. it would hae fallen before...
			$this->addException( APIErrors::UNKNOWN_PARTNER_ID );
		}
		
		$level = ( $detailed ? objectWrapperBase::DETAIL_LEVEL_DETAILED : objectWrapperBase::DETAIL_LEVEL_REGULAR );
		$wrapper = objectWrapperBase::getWrapperClass( $partner , $level );
		$this->addMsg ( "partner" , $wrapper ) ;
	}
}
?>