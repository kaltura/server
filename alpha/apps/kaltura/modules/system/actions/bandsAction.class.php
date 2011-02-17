<?php
/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
require_once ( "model/genericObjectWrapper.class.php" );
require_once ( "kalturaSystemAction.class.php" );

/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
class bandsAction extends kalturaSystemAction
{
	/**
	 * 
select kshow.id,concat('http://www.kaltura.com/index.php/browse/bands?band_id=',indexed_custom_data_1),concat('http://profile.myspace.com/index.cfm?fuseaction=user.viewpr
ofile&friendID=',indexed_custom_data_1) ,  kuser.screen_name , indexed_custom_data_1  from kshow ,kuser where kshow.partner_id=5 AND kuser.id=kshow.producer_id AND kshow.
id>=10815  order by kshow.id ;
~

	 */
	public function execute()
	{
	//	$this->forceSystemAuthentication();
		
		$from = $this->getRequestParameter( "from" , null );
		$to = $this->getRequestParameter( "to" , null );
		$limit = $this->getRequestParameter( "limit" , 100 );
		$c = new Criteria();
		$c->setLimit( $limit );
		$c->add ( kshowPeer::PARTNER_ID , 5 ); // myspace
		
		$c->addAscendingOrderByColumn( kshowPeer::ID );
		
		if ( !empty ( $from ) )
		{
			$c->addAnd( kshowPeer::ID , $from , Criteria::GREATER_EQUAL );
		}
		if ( ! empty ( $to ) )
		{
			$c->addAnd( kshowPeer::ID , $to , Criteria::LESS_EQUAL );
		}
		
		$this->band_list = kshowPeer::doSelectJoinkuser ( $c );
				
	}
}
?>