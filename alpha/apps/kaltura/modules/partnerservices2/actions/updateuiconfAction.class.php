<?php
require_once ( "defPartnerservices2Action.class.php");

class updateuiconfAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "updateUiconf",
				"desc" => "",
				"in" => array (
					"mandatory" => array ( 
						"uiconf_id" => array ("type" => "string", "desc" => ""),
						"uiconf" => array ("type" => "uiConf", "desc" => "")
						),
					"optional" => array (
						)
					),
				"out" => array (
					"uiconf" => array ("type" => "uiConf", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_UI_CONF_ID ,
				)
			); 
	}
	
	
	protected function getObjectPrefix () { return "uiconf"; }

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$this->applyPartnerFilterForClass( new uiConfPeer() , $partner_id );
		
		$allow_empty = $this->getP ( "allow_empty_field" , false );
		if ( $allow_empty == "false" || $allow_empty === 0 ) $allow_empty = false;
		
		$prefix = $this->getObjectPrefix();
		
		$uiconf_id = $this->getPM ( "{$prefix}_id" );
		$uiconf = uiConfPeer::retrieveByPK( $uiconf_id );

		if ( ! $uiconf )  
		{
			$this->addError( APIErrors::INVALID_UI_CONF_ID, $uiconf_id );
			return;
		}			
		if($uiconf && !$uiconf->isValid())
		{
			$this->addError(APIErrors::INTERNAL_SERVERL_ERROR, "uiConf object [{$uiconf->getId()}] is not valid");
			return;
		}		
		// get the new properties for the uiconf from the request
		$uiconf_update_data = new uiConf();
		$uiconf_update_data->setPartnerId( $partner_id );  // set this once before filling the object and once after
		
		$obj_wrapper = objectWrapperBase::getWrapperClass( $uiconf_update_data , 0 );
		$updateable_fields = $obj_wrapper->getUpdateableFields() ;
		$fields_modified = baseObjectUtils::fillObjectFromMapOrderedByFields( $this->getInputParams() , $uiconf_update_data , "{$prefix}_" , 
			$updateable_fields , BasePeer::TYPE_PHPNAME ,$allow_empty );
		if ( count ( $fields_modified ) > 0 )
		{
			if ( $uiconf_update_data )
			{
				baseObjectUtils::fillObjectFromObject( $updateable_fields  , $uiconf_update_data , $uiconf , baseObjectUtils::CLONE_POLICY_PREFER_NEW , 
					null , BasePeer::TYPE_PHPNAME ,$allow_empty );
			}

			$uiconf->save();
		}
		
		$wrapper = objectWrapperBase::getWrapperClass( $uiconf , objectWrapperBase::DETAIL_LEVEL_REGULAR );
		
		$this->addMsg ( "{$prefix}" , $wrapper );
		$this->addDebug ( "modified_fields" , $fields_modified );
	}
}
?>