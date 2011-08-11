<?php
/**
 * @package api
 * @subpackage ps2
 */
class getentryAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "getEntry",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"entry_id" => array ("type" => "string", "desc" => "")
						),
					"optional" => array (
						"detailed" => array ("type" => "boolean", "desc" => "") ,
						"version" => array ("type" => "string" , "desc" => "")
						)
					),
				"out" => array (
					"entry" => array ("type" => "entry", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_ENTRY_ID,
				)
			); 
	}

	public function needKuserFromPuser ( )	{		return self::KUSER_DATA_NO_KUSER;	}
		
	protected function addData ( $entry ) {}
	
	protected function getExtraFields ()
	{
		return null;
	}
	
	protected function getCriteria (  ) { return null; }
	
	protected function getObjectPrefix () { return "entry"; }

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$prefix = $this->getObjectPrefix();
		$entry_id = $this->getPM ( "{$prefix}_id" );
		$detailed = $this->getP ( "detailed" , false );
		$version = $this->getP ( "version" , false );
		
		$c = $this->getCriteria(); 
		if ( $c == null )
		{
			$c = new Criteria();
			$c->add(entryPeer::ID, $entry_id);
			$c->add(entryPeer::MODERATION_STATUS, entry::ENTRY_MODERATION_STATUS_REJECTED, Criteria::NOT_EQUAL);
			$entry = entryPeer::doSelectOne( $c );
		}
		else
		{
			$c->add ( entryPeer::ID , $entry_id );
			$c->add(entryPeer::MODERATION_STATUS, entry::ENTRY_MODERATION_STATUS_REJECTED, Criteria::NOT_EQUAL);
			$entry = entryPeer::doSelectOne( $c );
		}
		
		if ( ! $entry )
		{
			$this->addError ( APIErrors::INVALID_ENTRY_ID, $prefix , $entry_id );
		}
		else
		{
			if ( $entry->getStatus() == entryStatus::IMPORT || $entry->getStatus() == entryStatus::PRECONVERT )
			{
				defPartnerservices2baseAction::disableCache();
			}
			if ( $version ) $entry->setDesiredVersion ( $version );
			
			$extra_fields = $this->getExtraFields ();
			
			$level = ( $detailed ? objectWrapperBase::DETAIL_LEVEL_DETAILED : objectWrapperBase::DETAIL_LEVEL_REGULAR );
			if ( $entry->getType() == entryType::MIX )
				$extra_fields = array ( "allVersionsFormatted" );
			
			if ( $extra_fields )
			{
				$this->addMsg ( "$prefix" , objectWrapperBase::getWrapperClass( $entry , $level , -3 , 0 , $extra_fields ) );
			}
			else
			{
				$this->addMsg ( "$prefix" , objectWrapperBase::getWrapperClass( $entry , $level ) );
			}
			
			$this->addData( $entry );
		}
	}
}
?>