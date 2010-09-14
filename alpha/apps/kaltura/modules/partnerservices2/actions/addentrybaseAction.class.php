<?php
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

abstract class addentrybaseAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "addEntryBase",
				"desc" => "Create a new entry" ,
				"in" => array (
					"mandatory" => array ( 
						"entry" => array ("type" => "entry", "desc" => ""),
						),
					"optional" => array (
						)
					),
				"out" => array (
					"entry" => array ("type" => "entry", "desc" => "")
					),
				"errors" => array (
					APIErrors::NO_FIELDS_SET_FOR_GENERIC_ENTRY ,
					APIErrors::INVALID_KSHOW_ID
				)
			); 
	}
	
	protected function getDetailed()
	{
		return $this->getP ( "detailed" , false );
	}
	
	protected function getObjectPrefix () {  return "entry"; }

	abstract protected function setTypeAndMediaType ( $entry ) ;
	
	protected function validateEntry ( $entry ) {}

	protected function getKshow ( $partner_id, $subp_id , $puser_kuser , $kshow_id , $entry )
	{
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
        return $kshow;	
	}
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$detailed = $this->getDetailed() ; //$this->getP ( "detailed" , false );
		$level = ( $detailed ? objectWrapperBase::DETAIL_LEVEL_DETAILED : objectWrapperBase::DETAIL_LEVEL_REGULAR );
		
		// get the new properties for the kuser from the request
		$entry = new entry();
		
		// this is called for the first time to set the type and media type for fillObjectFromMap
		$this->setTypeAndMediaType ( $entry );
		
		// important to set type before the auto-fill so the setDataContent will work properly
		$entry->setLengthInMsecs( 0 );
		
		$obj_wrapper = objectWrapperBase::getWrapperClass( $entry , 0 );
		
		$field_level = $this->isAdmin() ? 2 : 1;
		$updateable_fields = $obj_wrapper->getUpdateableFields( $field_level );
		
		$fields_modified = baseObjectUtils::fillObjectFromMap ( $this->getInputParams() , $entry , $this->getObjectPrefix() . "_" , $updateable_fields );
		// check that mandatory fields were set
		// TODO
		if ( count ( $fields_modified ) > 0 )
		{
			
			$kshow_id = $this->getP ( "kshow_id" , kshow::KSHOW_ID_USE_DEFAULT );						
			$kshow = $this->getKshow ( $partner_id , $subp_id , $puser_kuser , $kshow_id , $entry );
	        
			// force the type and media type
			// TODO - set the kshow to some default kshow of the partner - maybe extract it from the custom_data of this specific partner
			$entry->setKshowId ( $kshow_id );
			$entry->setStatus( entry::ENTRY_STATUS_READY );
			$entry->setPartnerId( $partner_id );
			$entry->setSubpId( $subp_id );
			$entry->setKuserId($puser_kuser->getKuserId() );

			// this is now called for the second time to force the type and media type
			$this->setTypeAndMediaType ( $entry );

			$this->validateEntry ( $entry );
			
			$entry->save();
										
			$this->addMsg ( $this->getObjectPrefix() , objectWrapperBase::getWrapperClass( $entry , $level ) );
			$this->addDebug ( "added_fields" , $fields_modified );
		}
		else
		{
			$this->addError( APIErrors::NO_FIELDS_SET_FOR_GENERIC_ENTRY , $this->getObjectPrefix() );
		}
	}
}
?>