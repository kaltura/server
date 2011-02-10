<?php
/**
 * @package api
 * @subpackage ps2
 */
class adddvdentryAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "addDvdEntry",
				"desc" => "Create a new DVD entry: Project, Cover, Menu or Disk" ,
				"in" => array (
					"mandatory" => array ( 
						"dvdEntry" => array ("type" => "entry", "desc" => "Entry of type ENTRY_TYPE_DVD"),
						),
					"optional" => array (
						)
					),
				"out" => array (
					"dvdEntry" => array ("type" => "entry", "desc" => "Entry of type ENTRY_TYPE_DVD")
					),
				"errors" => array (
					APIErrors::NO_FIELDS_SET_FOR_GENERIC_ENTRY ,
					APIErrors::INVALID_KSHOW_ID
				)
			); 
	}
	
	protected function addUserOnDemand () { return self::CREATE_USER_FORCE; }
	
	protected function ticketType()			{	return self::REQUIED_TICKET_REGULAR;	} // TODO - and admin ticket

	protected function getObjectPrefix () { return "dvdEntry"; }

	protected function getTypeAndMediaType ( $entry ) 
	{
		$entry->setType ( entryType::DVD );
//		$entry->setMediaType( entry::ENTRY_MEDIA_TYPE_DVD_PROJECT );		 
	} 
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		// get the new properties for the kuser from the request
		$dvd_entry = new entry();
		
		$this->getTypeAndMediaType ( $dvd_entry );
		// important to set type before the auto-fill so the setDataContent will work properly
		$dvd_entry->setLengthInMsecs( 0 );
		
		$obj_wrapper = objectWrapperBase::getWrapperClass( $dvd_entry , 0 );
		
		$fields_modified = baseObjectUtils::fillObjectFromMap ( $this->getInputParams() , $dvd_entry , $this->getObjectPrefix() . "_" , $obj_wrapper->getUpdateableFields() );
		// check that mandatory fields were set
		// TODO
		$new_widget = null;
		if ( count ( $fields_modified ) > 0 )
		{
			
			$kshow_id = $this->getP ( "kshow_id" , kshow::KSHOW_ID_USE_DEFAULT );						
	       	if ( $kshow_id == kshow::KSHOW_ID_USE_DEFAULT )
	        {
	            // see if the partner has some default kshow to add to
	            $kshow = myPartnerUtils::getDefaultKshow ( $partner_id, $subp_id , $puser_kuser  );
	            if ( $kshow ) $kshow_id = $kshow->getId();
	        }
			elseif ( $kshow_id == kshow::KSHOW_ID_CREATE_NEW )
	        {
	            // if the partner allows - create a new kshow 
	            $kshow = myPartnerUtils::getDefaultKshow ( $partner_id, $subp_id , $puser_kuser , null , true );
	            if ( $kshow ) $kshow_id = $kshow->getId();
	        }   
			else
	        {
	            $kshow = kshowPeer::retrieveByPK( $kshow_id );
	        }
	
	        if ( ! $kshow )
	        {
	            // the partner is attempting to add an entry to some invalid or non-existing kwho
	            $this->addError( APIErrors::INVALID_KSHOW_ID, $kshow_id );
	            return;
	        }
	        
			// force the type and media type
			// TODO - set the kshow to some default kshow of the partner - maybe extract it from the custom_data of this specific partner
			$dvd_entry->setKshowId ( $kshow_id );
			$dvd_entry->setStatus( entryStatus::READY );
			$dvd_entry->setPartnerId( $partner_id );
			$dvd_entry->setSubpId( $subp_id );
			$dvd_entry->setKuserId($puser_kuser->getKuserId() );
						
			$dvd_entry->save();
										
			$this->addMsg ( $this->getObjectPrefix() , objectWrapperBase::getWrapperClass( $dvd_entry , objectWrapperBase::DETAIL_LEVEL_DETAILED) );
			$this->addDebug ( "added_fields" , $fields_modified );
		}
		else
		{
			$this->addError( APIErrors::NO_FIELDS_SET_FOR_GENERIC_ENTRY , $this->getObjectPrefix() );
		}
	}
}
?>