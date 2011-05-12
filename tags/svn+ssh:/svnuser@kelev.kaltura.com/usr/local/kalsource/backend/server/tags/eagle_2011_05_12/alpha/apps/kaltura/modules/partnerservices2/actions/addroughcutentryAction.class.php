<?php
/**
 * @package api
 * @subpackage ps2
 */
class addroughcutentryAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "addRoughcutEntry",
				"desc" => "Create a new roughcut entry" ,
				"in" => array (
					"mandatory" => array (
						"kshow_id" => array ("type" => "integer"), 
						"entry" => array ("type" => "entry", "desc" => "Entry of type ENTRY_TYPE_SHOW"),
						),
					"optional" => array (
						)
					),
				"out" => array (
					"entry" => array ("type" => "entry", "desc" => "Entry of type ENTRY_TYPE_SHOW")
					),
				"errors" => array (
					APIErrors::INVALID_KSHOW_ID
				)
			); 
	}
	
	protected function addUserOnDemand () { return self::CREATE_USER_FORCE; }
	
	protected function ticketType()			{	return self::REQUIED_TICKET_REGULAR;	} // TODO - and admin ticket

	protected function getObjectPrefix () { return "entry"; }

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$kshow_id = $this->getP ( "kshow_id" , kshow::KSHOW_ID_USE_DEFAULT );

		$entry = null;
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
            if ( $kshow )
            {
            	$kshow_id = $kshow->getId();
       	        $entry = $kshow->getShowEntry(); // use the newly created kshow's roughcut
            }
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
        
		if (!$entry)
		{
			$entry = $kshow->createEntry( entry::ENTRY_MEDIA_TYPE_SHOW , $kshow->getProducerId() , "&auto_edit.jpg" , "" ); 
		}
           
        $obj_wrapper = objectWrapperBase::getWrapperClass( $entry , 0 );
		
		$fields_modified = baseObjectUtils::fillObjectFromMap ( $this->getInputParams() , $entry , $this->getObjectPrefix() . "_" , 
			array ( "name"  , "tags" , "groupId" , "partnerData", "permissions" , "screenName",  "description", "indexedCustomData1") );
        
		$entry->save();
									
		$this->addMsg ( $this->getObjectPrefix() , objectWrapperBase::getWrapperClass( $entry , objectWrapperBase::DETAIL_LEVEL_REGULAR ) );
		$this->addDebug ( "added_fields" , $fields_modified );
	}
}
?>