<?php
/**
 * @package api
 * @subpackage ps2
 */
class cloneroughcutAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "cloneRoughcut",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"entry_id" => array ("type" => "string", "desc" => "")
						),
					"optional" => array (
						"detailed" => array ("type" => "boolean", "desc" => "")
						)
					),
				"out" => array (
					"entry" => array ("type" => "entry", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_ENTRY_ID,
					APIErrors::KSHOW_CLONE_FAILED ,
				)
			); 
	}
	
	protected function ticketType ()
	{
		return self::REQUIED_TICKET_ADMIN;
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
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$entry_id = $this->getPM ( "entry_id" );
		$detailed = $this->getP ( "detailed" , false );
		
		$entry = null;
		if ( $entry_id )
		{
			$entry = entryPeer::retrieveByPK( $entry_id );
		}
		
		if ( !$entry)
		{
			$this->addError ( APIErrors::INVALID_ENTRY_ID , $entry_id );
		}
		else
		{
			$kshow_id = $entry->getKshowId();
			$kshow = $entry->getKshow();
		
			if ( ! $kshow )
			{
				$this->addError ( APIErrors::INVALID_KSHOW_ID , $kshow_id );
			}
			else
			{
				$newKshow = myKshowUtils::shalowCloneById( $kshow_id , $puser_kuser->getKuserId() );
				
				if (!$newKshow)
				{
					$this->addError ( APIErrors::KSHOW_CLONE_FAILED , $kshow_id );
				}
				else
				{
					$newEntry = $newKshow->getShowEntry();
					
					$level = ( $detailed ? objectWrapperBase::DETAIL_LEVEL_DETAILED : objectWrapperBase::DETAIL_LEVEL_REGULAR );
					$wrapper = objectWrapperBase::getWrapperClass( $newEntry , $level );
					// TODO - remove this code when cache works properly when saving objects (in their save method)
					$wrapper->removeFromCache( "entry" , $newEntry->getId() );
					$this->addMsg ( "entry" , $wrapper ) ;
				}
			}
		}
	}
}
?>