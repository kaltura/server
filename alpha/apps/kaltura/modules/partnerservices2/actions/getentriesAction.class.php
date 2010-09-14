<?php
require_once ( "myInsertEntryHelper.class.php");
require_once ( "myKshowUtils.class.php");
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

class getentriesAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "getEntries",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"entry_ids" => array ("type" => "string", "desc" => ""),
						),
					"optional" => array (
						"separator" => array ("type" => "string", "default" => ",", "desc" => ""),
						"detailed" => array ("type" => "boolean", "desc" => "")
						)
					),
				"out" => array (
					"entries" => array ("type" => "*entry", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_ENTRY_IDS ,
				)
			); 
	}
	
	public function needKuserFromPuser ( )	{		return self::KUSER_DATA_NO_KUSER;	}
	
	protected function getExtraFields ()
	{
		return null;
	}

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;
		

		$entry_ids = str_replace(" ", "", $this->getPM ( "entry_ids" ));
		$detailed = $this->getP ( "detailed" , false );
		$separator = $this->getP ( "separator" , "," );

		$id_arr = explode ( $separator , $entry_ids );
		$limit = 50;
		$id_arr = array_splice( $id_arr , 0 , $limit );

		$entries = entryPeer::retrieveByPKs( $id_arr );
		if ( ! $entries )
		{
			$this->addError ( APIErrors::INVALID_ENTRY_IDS , $entry_ids);
		}
		else
		{
			$level = ( $detailed ? objectWrapperBase::DETAIL_LEVEL_DETAILED : objectWrapperBase::DETAIL_LEVEL_REGULAR );
			$extra_fields = $this->getExtraFields ();

			if ( $extra_fields )
			{
				$this->addMsg ( "entries" , objectWrapperBase::getWrapperClass( $entries , $level , objectWrapperBase::DETAIL_VELOCITY_DEFAULT , 0 , $extra_fields ) );
			}
			else
			{
				$this->addMsg ( "entries" , objectWrapperBase::getWrapperClass( $entries , $level ) );
			}
		}
	}
}
?>