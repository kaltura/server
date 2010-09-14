<?php
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

/**
 * After making sure the ticket is a valid admin ticket - the setrvice is allowed and no other validations should be done
 */
class deleteuiconfAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "deleteUiconf",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"uiconf_id" => array ("type" => "string", "desc" => "")
						),
					"optional" => array (
						)
					),
				"out" => array (
					"deleted_uiconf" => array ("type" => "uiConf", "desc" => "")
					),
				"errors" => array (
					 APIErrors::INVALID_UI_CONF_ID ,
				)
			); 
	}
	
	protected function getObjectPrefix () { return "uiconf"; }

	protected function getCriteria (  ) { return null; }

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$this->applyPartnerFilterForClass( new uiConfPeer() , $partner_id );
		
		$prefix = $this->getObjectPrefix();
		$uiconf_id_to_delete = $this->getPM ( "{$prefix}_id" );

		$uiconf_to_delete = uiConfPeer::retrieveByPK( $uiconf_id_to_delete );
		if($uiconf_to_delete && !$uiconf_to_delete->isValid())
		{
			$this->addError(APIErrors::INTERNAL_SERVERL_ERROR, "uiConf object [{$uiconf_to_delete->getId()}] is not valid");
			return;
		}
		
		if ( ! $uiconf_to_delete )
		{
			$this->addError( APIErrors::INVALID_UI_CONF_ID , $prefix , $uiconf_id_to_delete );
			return;
		}

		$uiconf_to_delete->setStatus ( uiConf::UI_CONF_STATUS_DELETED );

		$uiconf_to_delete->save();
//		myNotificationMgr::createNotification( notification::NOTIFICATION_TYPE_ENTRY_DELETE , $entry_to_delete );

		$this->addMsg ( "deleted_" . $prefix  , objectWrapperBase::getWrapperClass( $uiconf_to_delete , objectWrapperBase::DETAIL_LEVEL_REGULAR ) );

	}
}
?>