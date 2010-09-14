<?php
require_once ( "myStatisticsMgr.class.php");
/**
 * Subclass for representing a row from the 'kshow_kuser' table.
 *
 * 
 *
 * @package lib.model
 */ 
class KshowKuser extends BaseKshowKuser
{
	// different type of subscriptions
	const KSHOW_SUBSCRIPTION_NORMAL = 1;
	
	// differnt types of viewers
	const KSHOWKUSER_VIEWER_USER = 0;
	const KSHOWKUSER_VIEWER_SUBSCRIBER = 1;
	const KSHOWKUSER_VIEWER_PRODUCER = 2;
	
	public function save(PropelPDO $con = null)
	{
		if ( $this->isNew() )
		{
			myStatisticsMgr::addSubscriber( $this );
		}
		
		parent::save( $con );
	}			
}
