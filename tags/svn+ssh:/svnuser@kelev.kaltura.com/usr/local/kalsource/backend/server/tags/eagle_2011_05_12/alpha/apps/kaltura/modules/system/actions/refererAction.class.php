<?php
/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
require_once ( "kalturaSystemAction.class.php" );

/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
class refererAction extends kalturaSystemAction
{
	
	public function execute()
	{
		$this->forceSystemAuthentication();

		$start = microtime(true);
		
		$partner_id = $this->getP ( "partner_id" );
		$limit = $this->getP ( "limit" , 20 );
		
		$c = new Criteria();
		$c->add ( WidgetLogPeer::PARTNER_ID , $partner_id );
		$c->addAnd ( WidgetLogPeer::REFERER , null , Criteria::ISNOTNULL );
		$c->addAnd ( WidgetLogPeer::REFERER , "" , Criteria::NOT_EQUAL );
		$c->addAnd(WidgetLogPeer::REFERER, "%kaltura:%", Criteria::NOT_LIKE);
		$c->addAnd(WidgetLogPeer::REFERER, "%localhost%", Criteria::NOT_LIKE);
		$c->addDescendingOrderByColumn ( WidgetLogPeer::CREATED_AT );
		$c->setLimit ( min ( $limit , 50 ) );
		
		$this->widget_list = WidgetLogPeer::doSelect( $c );
		$this->partner_id = $partner_id;
				
	}	
}

?>