<?php
/**
 * @package api
 * @subpackage ps2
 */
class getwidgetAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 	
			array (
				"display_name" => "getWidget",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"widget_id" => array ("type" => "string", "desc" => "")
						),
					"optional" => array (
						"detailed" => array ("type" => "boolean", "desc" => "") ,
						"uiconf_id" => array ("type" => "integer", "desc" => "")
						)
					),
				"out" => array (
					"widget" => array ("type" => "widget", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_WIDGET_ID  ,
				)
			); 
	}
	
	public function needKuserFromPuser ( )	{		return self::KUSER_DATA_NO_KUSER;	}
	
	protected function ticketType ()	{		return self::REQUIED_TICKET_NONE;	}

	protected function addUserOnDemand ( )	{		return self::CREATE_USER_FALSE;	}

	// we'll allow empty uid here - this is called from just any place in the web with no defined context
	protected function allowEmptyPuser()	{		return true;	}
	
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		// it is very common to expect an updated ui_conf object 
		objectWrapperBase::useCache( false );
		
		$widget_id = $this->getPM ( "widget_id" );
		$detailed = $this->getP ( "detailed" , false );
		$uiconf_id = $this->getP ( "uiconf_id" , $this->getP ( "ui_conf_id" , null ) );

		self::$escape_text = true;

		//$widget = widgetPeer::retrieveByHashedId( $widget_id );
		$widget = widgetPeer::retrieveByPK( $widget_id );
		if ( ! $widget )
		{
			$this->addError ( APIErrors::INVALID_WIDGET_ID , $widget_id );
		}
		else
		{
			// check if this widget is public - if so , create a ks for viewing the related kshow
			if ( $uiconf_id )
				$widget->overrideUiConfId ( $uiconf_id );

			// make sure the validation is done before leaving the action -
			// because the getWidgetHtml might throw an exception - it's better to envke it here rather than tht UI  
			$widget->getWidgetHtml();
			
			// TODO - call
//$result = kSessionUtils::startKSession ( $partner_id , $this->getP ( "secret" ) , $puser_id , $ks , $expiry , $admin , "" , $privileges );
			$level = ( $detailed ? objectWrapperBase::DETAIL_LEVEL_DETAILED : objectWrapperBase::DETAIL_LEVEL_REGULAR );
			$this->addMsg ( "widget" , objectWrapperBase::getWrapperClass( $widget , $level ) );
		}
	}
}
?>