<?php
/**
 * @package api
 * @subpackage ps2
 */
class getdefaultwidgetAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 	
			array (
				"display_name" => "getDefaultWidget",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						),
					"optional" => array (
						"ui_conf_id" => array ("type" => "integer", "desc" => "")
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
	
	protected function ticketType ()	{		return self::REQUIED_TICKET_ADMIN;	}		// should be done only frombackoffice

	protected function addUserOnDemand ( )	{		return self::CREATE_USER_FALSE;	}

	// we'll allow empty uid here - this is called from just any place in the web with no defined context
	protected function allowEmptyPuser()	{		return true;	}
	
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		self::$escape_text = true;
		$detailed = 1;

		$ui_conf_id = $this->getP ( "ui_conf_id" , null );
		
		$widget = widget::createDefaultWidgetForPartner( $partner_id , $subp_id , $ui_conf_id );
		$level = ( $detailed ? objectWrapperBase::DETAIL_LEVEL_DETAILED : objectWrapperBase::DETAIL_LEVEL_REGULAR );
		$this->addMsg ( "widget" , objectWrapperBase::getWrapperClass( $widget , $level ) );

	}
}
?>