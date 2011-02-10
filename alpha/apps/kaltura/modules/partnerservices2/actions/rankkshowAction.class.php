<?php
/**
 * After making sure the ticket is a valid admin ticket - the setrvice is allowed and no other validations should be done
 * 
 * @package api
 * @subpackage ps2
 */
class rankkshowAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 	
			array (
				"display_name" => "rankKShow",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"kshow_id" => array ("type" => "string", "desc" => ""),
						"rank" => array ("type" => "integer", "desc" => "")
						),
					"optional" => array ()
					),
				"out" => array (
					"rank" => array ("type" => "array", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_RANK ,
					APIErrors::INVALID_KSHOW_ID , 
					APIErrors::USER_ALREADY_RANKED_KSHOW , 
					
				)
			); 
	}
	
	protected function ticketType()	{		return self::REQUIED_TICKET_REGULAR;	}
	// ask to fetch the kuser from puser_kuser - so we can tel the difference between a 
	public function needKuserFromPuser ( )	{		return self::KUSER_DATA_KUSER_ID_ONLY; 	}
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$kshow_id = $this->getPM ( "kshow_id" );
		$rank = $this->getPM ( "rank" );
		
		$kshow = kshowPeer::retrieveByPK( $kshow_id );
		
		if ( ! $kshow )
		{
			$this->addError( APIErrors::INVALID_KSHOW_ID , $kshow_id  );
			return;		
		}
		
		if ( $rank > entry::MAX_NORMALIZED_RANK || $rank < 0 || ! is_numeric( $rank ))
		{
			$this->addError( APIErrors::INVALID_RANK , $rank );
			return;					
		}

		$kuser_id = $puser_kuser->getKuserId();
		$entry_id = $kshow->getShowEntryId();
		
		$partner = PartnerPeer::retrieveByPK($partner_id);

		if (!$partner->getAllowAnonymousRanking()) 
		{
			// prevent duplicate votes
			$c = new Criteria ();
			$c->add ( kvotePeer::KUSER_ID , $kuser_id);
			$c->add ( kvotePeer::ENTRY_ID , $entry_id);
			$c->add ( kvotePeer::KSHOW_ID , $kshow_id);
			
			$kvote = kvotePeer::doSelectOne( $c );
			if ( $kvote != NULL )
			{
				$this->addError( APIErrors::USER_ALREADY_RANKED_KSHOW , $puser_id  , $kshow_id );
				return;						
			}
		}
		
		$kvote = new kvote();
		$kvote->setKshowId($kshow_id);
		$kvote->setEntryId($entry_id);
		$kvote->setKuserId($kuser_id);
		$kvote->setRank($rank);
		$kvote->save();

		$statistics_results = $kvote->getStatisticsResults();
		$updated_kshow = @$statistics_results["kshow"];
		
		if ( $updated_kshow )
		{
			myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_KSHOW_RANK , $updated_kshow );
			
			$data = array ( "kshow_id" => $kshow_id , 
				"uid" => $puser_id ,
				"rank" => $updated_kshow->getRank() ,
				"votes" => $updated_kshow->getVotes() );
				
			//$this->addMsg ( "kshow" , objectWrapperBase::getWrapperClass( $updated_kshow , objectWrapperBase::DETAIL_LEVEL_DETAILED) );
			$this->addMsg ( "rank" , $data ); 
		}

	}
}
?>