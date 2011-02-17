<?php
require_once ( "kalturaSystemActions.class.php");
/**
 * system actions.
 *
 * @package    Core
 * @subpackage system
 * @deprecated
 */
class systemActions extends kalturaSystemActions
{
  /**
   * Executes index action
   *
   */
	public function executeDefSystem()
	{
		$this->forceSystemAuthentication();
		
	}
	
	
	public function executeEnvironment()
	{
		$this->forceSystemAuthentication();
		
	}
	
	
	public function executeCheckAttachmentImport()
	{
					
	}
	
	public function executeViewReports()
	{
		$this->forceSystemAuthentication();
		
		$c = new Criteria();
		$c->addDescendingOrderByColumn( flagPeer::CREATED_AT);
		$c->addJoin( flagPeer::KUSER_ID, kuserPeer::ID, Criteria::LEFT_JOIN );
		$this->reports = flagPeer::doSelectJoinkuser( $c );
	}

	public function executeDeleteReport()
	{
		$this->forceSystemAuthentication();
		
		$id = $this->getRequestParameter( 'id');
		if ( $id )
		{
			$report =  flagPeer::retrieveByPK( $id );
			$report->delete();
		}
		$this->redirect('system/viewReports');
		
	}
	
	
}
