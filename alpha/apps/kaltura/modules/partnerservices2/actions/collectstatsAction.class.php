<?php
require_once ( "myInsertEntryHelper.class.php");
require_once ( "myKshowUtils.class.php");
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

/**
 * After making sure the ticket is a valid admin ticket - the setrvice is allowed and no other validations should be done
 */
class collectstatsAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "collectStats",
				"desc" => "collect statiscits about special events from the client" ,
				"in" => array (
					"mandatory" => array ( 
						"obj_type" => array ("type" => "string", "desc" => ""),
						"obj_id" => array ("type" => "string", "desc" => ""),
						"command" => array ("type" => "string", "desc" => ""),
						"value" => array ("type" => "string", "desc" => ""),
						"extra_info" => array ("type" => "string", "desc" => ""),
						),
					"optional" => array (
						"kshow_id" => array ("type" => "string", "desc" => "")
						)
					),
				"out" => array (
					"deleted_entry" => array ("type" => "entry", "desc" => "")
					),
				"errors" => array (
					 APIErrors::INVALID_ENTRY_ID ,
					 APIErrors::CANNOT_DELETE_ENTRY ,
				)
			); 
	}
	
	protected function ticketType()
	{
		return self::REQUIED_TICKET_REGULAR;
	}

	// ask to fetch the kuser from puser_kuser - so we can tel the difference between a
	public function needKuserFromPuser ( )
	{
		return self::KUSER_DATA_NO_KUSER;
	}

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$obj_type = $this->getPM ( "obj_type" );
		$obj_id = $this->getPM ( "obj_id" );
		$command = $this->getPM ( "command" );
		$value = $this->getP ( "value" );
		$extra_info = $this->getP ( "extra_info" );
		
		if ( $obj_type == "entry" )
		{
			$entry = entryPeer::retrieveByPK( $obj_id );
			if ( $command == "view" )
			{
				PartnerActivity::incrementActivity($partner_id, PartnerActivity::PARTNER_ACTIVITY_KDP, PartnerActivity::PARTNER_SUB_ACTIVITY_KDP_VIEWS);
				myStatisticsMgr::incEntryViews( $entry );
			}
			elseif ( $command == "play" )
			{
				PartnerActivity::incrementActivity($partner_id, PartnerActivity::PARTNER_ACTIVITY_KDP, PartnerActivity::PARTNER_SUB_ACTIVITY_KDP_PLAYS);
				myStatisticsMgr::incEntryPlays( $entry );
			}
		}
		elseif ( $obj_type == "kshow" )
		{
			$kshow = kshowPeer::retrieveByPK( $obj_id );
			if ( $command == "view" )
			{
				PartnerActivity::incrementActivity($partner_id, PartnerActivity::PARTNER_ACTIVITY_KDP, PartnerActivity::PARTNER_SUB_ACTIVITY_KDP_VIEWS);
				myStatisticsMgr::incKshowViews( $kshow );
			}
			elseif ( $command == "play" )
			{
				PartnerActivity::incrementActivity($partner_id, PartnerActivity::PARTNER_ACTIVITY_KDP, PartnerActivity::PARTNER_SUB_ACTIVITY_KDP_PLAYS);
				myStatisticsMgr::incKshowPlays( $kshow );
			}
		}	

		$this->addMsg( "collectedStats" , "$obj_type, $obj_id, $command, $value, $extra_info");
	}
}
?>