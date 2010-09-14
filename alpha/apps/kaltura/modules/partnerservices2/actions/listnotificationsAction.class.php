<?php
require_once ( "defPartnerservices2Action.class.php");

class listnotificationsAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 	
			array (
				"display_name" => "listNotifications",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"filter" => array ("type" => "notificationFilter", "desc" => "")
						),
					"optional" => array (
						"page_size" => array ("type" => "integer", "default" => 10, "desc" => ""),
						"page" => array ("type" => "boolean", "default" => 1, "desc" => ""),
						)
					),
				"out" => array (
					"count" => array ("type" => "integer", "desc" => ""),
					"page_size" => array ("type" => "integer", "desc" => ""),
					"page" => array ("type" => "integer", "desc" => ""),
					"notifications" => array ("type" => "*notification", "desc" => "")
					),
				"errors" => array (
				)
			); 
	}
	
	protected function ticketType()	{		return self::REQUIED_TICKET_ADMIN;	}
		
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		// TODO -  verify permissions for viewing lists 

//		$detailed = $this->getP ( "detailed" , false );
		$limit = $this->getP ( "page_size" , 10 );
		if ( $limit > 30 ) $limit = 30;
		$page = $this->getP ( "page" , 1 );		
		//$order_by = int( $this->getP ( "order_by" , -1 ) );
		
		$offset = ($page-1)* $limit;

//		kuserPeer::setUseCriteriaFilter( false );

		$c = new Criteria();
				
		// filter		
		$filter = new notificationFilter(  );
		$fields_set = $filter->fillObjectFromRequest( $this->getInputParams() , "filter_" , null );
		$filter->attachToCriteria( $c );
		
		//if ($order_by != -1) kshowPeer::setOrder( $c , $order_by );
		$count = notificationPeer::doCount( $c );

		$offset = ($page-1)* $limit;
		
		$c->setLimit( $limit );
		
		if ( $offset > 0 )
		{
			$c->setOffset( $offset );
		}
				
		$list = notificationPeer::doSelect( $c );
		$level = objectWrapperBase::DETAIL_LEVEL_REGULAR ;

		$this->addMsg ( "count" , $count );
		$this->addMsg ( "page_size" , $limit );
		$this->addMsg ( "page" , $page );

		$wrapper =  objectWrapperBase::getWrapperClass( $list  , $level );
		$this->addMsg ( "notifications" , $wrapper ) ;
	}
}
?>