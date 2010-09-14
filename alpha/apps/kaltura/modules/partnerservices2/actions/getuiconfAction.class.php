<?php
require_once ( "myInsertEntryHelper.class.php");
require_once ( "myKshowUtils.class.php");
require_once ( "defPartnerservices2Action.class.php");
require_once ( "addkshowAction.class.php");



/**
 * 1. Will create a kshow with name and summary for a specific partner.
 * 2. Will generate widget-html for this kshow.  
 */
class getuiconfAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 	
			array (
				"display_name" => "getUIConf",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"ui_conf_id" => array ("type" => "integer", "desc" => "")
						),
					"optional" => array (
						"detailed" => array ("type" => "boolean", "desc" => "")
						)
					),
				"out" => array (
					"ui_conf" => array ("type" => "uiConf", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_UI_CONF_ID ,
				)
			); 
	}
	
	public function needKuserFromPuser ( )	{		return self::KUSER_DATA_NO_KUSER;	}
	
	protected function ticketType ()	
	{	
		// validate for all partners that are not kaltura (partner_id=0)		
		$partner_id = $this->getP ( "partner_id");		
		return ( $partner_id != 0 ? self::REQUIED_TICKET_REGULAR : self::REQUIED_TICKET_NONE );
	}

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$this->applyPartnerFilterForClass( new uiConfPeer() , $partner_id );
		
		$ui_conf_id = $this->getPM ( "ui_conf_id");
		$detailed = $this->getP ( "detailed" , false );
		$level = ( $detailed ? objectWrapperBase::DETAIL_LEVEL_DETAILED : objectWrapperBase::DETAIL_LEVEL_REGULAR );

		$ui_conf = uiConfPeer::retrieveByPK( $ui_conf_id );
		if($ui_conf && !$ui_conf->isValid())
		{
			$this->addError(APIErrors::INTERNAL_SERVERL_ERROR, "uiConf object [{$ui_conf->getId()}] is not valid");
			return;
		}
		
		if ( $ui_conf )
		{
			$this->addMsg ( "ui_conf" , objectWrapperBase::getWrapperClass( $ui_conf ,  $level  ) );
			
			self::$escape_text = true;
			/*
			$service_provider_list = myPartnerUtils::getMediaServiceProviders ( $partner_id , $subp_id );
			$this->addMsg( "config_" , $service_provider_list );
			
			$ui =  array ( "__0_moduleData" => 
						array ( "moduleUrl" => "main" , 
						"style" => "SearchView"  , "locale" => "SearchView"  ) ,
					"__1_moduleData" => 
						array ( "moduleUrl" => "SearchView.swf" , 
						"style" => "SearchView"  , "locale" => "SearchView"  ) ,
					"__2_moduleData" => 
					array ( "moduleUrl" => "SearchView.swf" , 
					"style" => "SearchView"  , "locale" => "SearchView"  ) , 
				"__3_moduleData" => 
					array ( "moduleUrl" => "SearchView.swf" , 
					"style" => "SearchView"  , "locale" => "SearchView"  ) ,					
					
				) ;
			$this->addMsg( "ui" , $ui );
			*/
		}
		else
		{
			$this->addError ( APIErrors::INVALID_UI_CONF_ID , $ui_conf_id );
			return;
		}
	}
	
	
	
}
?>