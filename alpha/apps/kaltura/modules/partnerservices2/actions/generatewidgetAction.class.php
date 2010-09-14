<?php
require_once ( "myInsertEntryHelper.class.php");
require_once ( "myKshowUtils.class.php");
require_once ( "defPartnerservices2Action.class.php");
require_once ( "addkshowAction.class.php");



/**
 * 1. Will create a kshow with name and summary for a specific partner.
 * 2. Will generate widget-html for this kshow.
 */
class generatewidgetAction extends addkshowAction
{
	public function describe()
	{
		return 
			array (); 
	}
	
	// check to see if already exists in the system = ask to fetch the puser & the kuser
	// don't ask for  KUSER_DATA_KUSER_DATA - because then we won't tell the difference between a missing kuser and a missing puser_kuser
	public function needKuserFromPuser ( )
	{
		return self::KUSER_DATA_KUSER_ID_ONLY;
	}

	protected function addUserOnDemand ( )
	{
		return self::CREATE_USER_FROM_PARTNER_SETTINGS;
	}

	protected function ticketType ()
	{
		// validate for all partners that are not kaltura (partner_id=0)
		$partner_id = $this->getP ( "partner_id");
		return ( $partner_id != 0 ? self::REQUIED_TICKET_ADMIN : self::REQUIED_TICKET_NONE );
	}
	/*
	public function execute( $add_extra_debug_data = true )
	{
		// will inject data so the base class will act as it the partner_id is 0
		$this->injectIfEmpty ( array (
			"partner_id" => "0" ,
			"subp_id" => "0" ,
			"uid" => "_00" ));

		return parent::execute();
	}
	*/
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$kshow_id = $this->getP ( "kshow_id");
		$detailed = $this->getP ( "detailed" , false );
		$level = ( $detailed ? objectWrapperBase::DETAIL_LEVEL_DETAILED : objectWrapperBase::DETAIL_LEVEL_REGULAR );

		$widget_size = $this->getP ( "size" );

		$kshow_from_db = null;
		if ( $kshow_id )
		{
			$kshow_from_db = kshowPeer::retrieveByPK( $kshow_id );
		}

		if ( $kshow_from_db )
		{
			$this->addMsg ( "kshow" , objectWrapperBase::getWrapperClass( $kshow_from_db ,  $level  ) );
			$this->addMsg ( "already_exists_objects" , 1 );
			$this->addDebug ( "already_exists_objects" , 1 );
		}
		else
		{
			// no kshow to be found - creae a new one
			parent::executeImpl(  $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser );
		}

		// create widget code for the new kshow
		$kshow = $this->getMsg ( "kshow" );
		$kshow_id = $kshow->id;

		list ($genericWidget, $myspaceWidget) = myKshowUtils::getEmbedPlayerUrl ($kshow_id,null , false , "" );
		$code = array ( "generic_code" => $genericWidget , "myspace_code" => $myspaceWidget );
		$this->addMsg ( "widget_code" , $code );

	}



}
?>