<?php
/**
 * @package api
 * @subpackage ps2
 */
class getentryroughcutsAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "getEntryRoughcuts",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"entry_id" => array ("type" => "string", "desc" => "")
						),
					"optional" => array (
//						"detailed" => array ("type" => "boolean", "desc" => "") ,
//						"version" => array ("type" => "string" , "desc" => "")
						)
					),
				"out" => array (
					"roughcuts" => array ("type" => "*entry", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_ENTRY_ID,
				)
			); 
	}

	public function needKuserFromPuser ( )	{		return self::KUSER_DATA_NO_KUSER;	}
		
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
		$detailed = false;//$this->getP ( "detailed" , false );
		$version = $this->getP ( "version" , false );
		
		$entry = new entry();
		$entry->setId ( $entry_id );
/*		
		$c = $this->getCriteria(); 
		if ( $c == null )
		{
			$entry = entryPeer::retrieveByPK( $entry_id );
		}
		else
		{
			$c->add ( entryPeer::ID , $entry_id );
			$entry = entryPeer::doSelectOne( $c );
		}
	*/	
		if ( ! $entry )
		{
			$this->addError ( APIErrors::INVALID_ENTRY_ID, $prefix , $entry_id );
		}
		else
		{
			$level = ( $detailed ? objectWrapperBase::DETAIL_LEVEL_DETAILED : objectWrapperBase::DETAIL_LEVEL_REGULAR );
			
			$roughcuts = $entry->getRoughcuts();
			
			$this->addMsg( "count" , count ( $roughcuts ));
			$this->addMsg ( "roughcuts" , objectWrapperBase::getWrapperClass( $roughcuts , $level ) );
			
		}
	}
}
?>