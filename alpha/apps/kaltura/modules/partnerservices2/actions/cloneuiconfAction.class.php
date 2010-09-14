<?php
require_once ( "defPartnerservices2Action.class.php");

class cloneuiconfAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "cloneUiConf",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"uiconf_id" => array ("type" => "string", "desc" => "")
						),
					"optional" => array (
						"detailed" => array ("type" => "boolean", "desc" => ""),
						"new_name" => array ("type" => "text", "desc" => "")
						)
					),
				"out" => array (
					"uiconf" => array ("type" => "uiConf", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_UI_CONF_ID,
				)
			); 
	}
	
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$this->applyPartnerFilterForClass ( new uiConfPeer() , $partner_id );
		
		$ui_conf_id = $this->getPM ( "uiconf_id" );
		$detailed = $this->getP ( "detailed" , false );

		$new_name = $this->getP ( "new_name" );
		if (!$new_name || $new_name == '') $new_name = null;
		
		$ui_conf = null;
		if ( $ui_conf_id )
		{
			$ui_conf = uiConfPeer::retrieveByPK( $ui_conf_id );
		}

		if ( ! $ui_conf )
		{
			$this->addError ( APIErrors::INVALID_UI_CONF_ID , $ui_conf_id  );
		}
		else
		{
			$ui_conf_verride_params = new uiConf();
			$ui_conf_verride_params->setPartnerId( $partner_id );
			$ui_conf_verride_params->setDisplayInSearch(1);  // the cloned ui_conf should NOT be a template
			$new_ui_conf = $ui_conf->cloneToNew ( $ui_conf_verride_params , $new_name );
			
			if (!$new_ui_conf)
			{
				$this->addError ( APIErrors::UI_CONF_CLONE_FAILED , $ui_conf_id );
			}
			else
			{
				$level = ( $detailed ? objectWrapperBase::DETAIL_LEVEL_DETAILED : objectWrapperBase::DETAIL_LEVEL_REGULAR );
				$wrapper = objectWrapperBase::getWrapperClass( $new_ui_conf , $level );
				// TODO - remove this code when cache works properly when saving objects (in their save method)
//				$wrapper->removeFromCache( "kshow" , $new_ui_conf->getId() );
				$this->addMsg ( "uiconf" , $wrapper ) ;
			}
		}
	}
}
?>