<?php
require_once ( "model/genericObjectWrapper.class.php" );
require_once ( "kalturaSystemAction.class.php" );

class showErrorsAction extends kalturaSystemAction
{
	/**
	 * Will display errornous entries
	 */
	public function execute()
	{
		$this->forceSystemAuthentication();		
		
		entryPeer::setUseCriteriaFilter( false );
		
		// find entries with status error
		$c = new Criteria();
		$c->add ( entryPeer::STATUS , entryStatus::ERROR_CONVERTING );
		$this->error_converting = entryPeer::doSelect( $c );
		
		$date_format = 'Y-m-d H:i:s';
		$this->several_minutes_ago  = time() - 5 * 60 ; // 5 minutes ago //mktime(0, 0, 0, date("m"), date("d"),   date("Y"));
		$start_date = date( $date_format , $this->several_minutes_ago );
				
		$c = new Criteria();
		$c->add ( entryPeer::STATUS , array ( entryStatus::IMPORT , entryStatus::PRECONVERT ) , Criteria::IN ) ;
		$c->add ( entryPeer::UPDATED_AT , $start_date , Criteria::LESS_THAN  );
		$this->error_waiting_too_long = entryPeer::doSelect( $c );
		
		$this->start_date = $start_date;
	}
}
?>