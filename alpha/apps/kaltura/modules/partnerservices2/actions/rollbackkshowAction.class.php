<?php
require_once ( "myInsertEntryHelper.class.php");
require_once ( "myKshowUtils.class.php");
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

class rollbackkshowAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "rollbackKShow",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"kshow_id" => array ("type" => "string", "desc" => ""),
						"kshow_version" => array ("type" => "integer", "desc" => "")
						),
					"optional" => array (
						)
					),
				"out" => array (
					"kshow" => array ("type" => "kshow", "desc" => "")
					),
				"errors" => array (
					APIErrors::ERROR_KSHOW_ROLLBACK , 
					APIErrors::INVALID_USER_ID , 
					APIErrors::INVALID_KSHOW_ID ,
				)
			); 
	}
	
	// ask to fetch the kuser from puser_kuser
	public function needKuserFromPuser ( )
	{
		return self::KUSER_DATA_KUSER_ID_ONLY;
	}

	// TODO - merge with updatekshow and add the functionality of rollbackVersion
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		if ( ! $puser_kuser )
		{
			$this->addError ( APIErrors::INVALID_USER_ID ,  $puser_id);
			return;
		}

		$kshow_id = $this->getPM ( "kshow_id");
		
		$kshow = kshowPeer::retrieveByPK( $kshow_id );

		// even in case of an error - return the kshow object
		if ( ! $kshow )
		{
			$this->addError ( APIErrors::INVALID_KSHOW_ID , $kshow_id );
			return;
		}
		else
		{
			$desired_version = $this->getPM ( "kshow_version");
			$result = $kshow->rollbackVersion ( $desired_version );
		
			if ( ! $result )
			{
				$this->addError ( APIErrors::ERROR_KSHOW_ROLLBACK , $kshow_id ,$desired_version );
				return ;
			}
		}

		// after calling this method - most probably the state of the kshow has changed in the cache
		$wrapper = objectWrapperBase::getWrapperClass( $kshow , objectWrapperBase::DETAIL_LEVEL_REGULAR ) ;
		$wrapper->removeFromCache( "kshow" , $kshow_id );
		$this->addMsg ( "kshow" , $wrapper );
	}
}
?>