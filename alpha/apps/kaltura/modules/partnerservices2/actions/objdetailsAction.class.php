<?php
require_once ( "myInsertEntryHelper.class.php");
require_once ( "myKshowUtils.class.php");
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

/**
 * This is a utility service that helps describes the fields of our objects - not the data itself
 */
class objdetailsAction extends defPartnerservices2Action
{
	public function describe()
	{
		return array();		
	}
	
	protected function ticketType ()
	{
		return self::REQUIED_TICKET_NONE;
	}

	// ask to fetch the kuser from puser_kuser
	public function needKuserFromPuser ( )
	{
		return self::KUSER_DATA_NO_KUSER;
	}

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$clazz_name = $this->getP ( "clazz" );
		if ( $clazz_name == "kshow" ) $obj = new kshow();
		else if ( $clazz_name == "kuser" ) $obj = new kuser();
		else if ( $clazz_name == "entry" ) $obj = new entry();
		else if ( $clazz_name == "PuserKuser" ) $obj = new PuserKuser();

		$obj = new $clazz_name();

		$detailed = $this->getP ( "detailed" );
		$level = ( $detailed ? objectWrapperBase::DETAIL_LEVEL_DETAILED : objectWrapperBase::DETAIL_LEVEL_REGULAR );
		$wrapper = objectWrapperBase::getWrapperClass( $obj , $level ) ;

		if ( $wrapper )
		{
			$this->addMsg ( "regular" , $wrapper->getRegularFields() );
			$this->addMsg ( "detailed" , $wrapper->getDetailedFields() );
			$this->addMsg ( "objects" , $wrapper->getObjectTypes() );
		}
		else
		{
			$this->addError( "Cannot find object fo type [$clazz_name]");
		}
	}
}
?>