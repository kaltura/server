<?php
require_once ( "model/genericObjectWrapper.class.php" );
require_once ( "kalturaSystemAction.class.php" );

class listforumAction extends kalturaSystemAction
{
	public function execute()
	{
		$this->forceSystemAuthentication();

		$page_zise = 40;
		$page = $this->getRequestParameter( "page" , 0 );
		$this->encode = $this->getRequestParameter( "encode" , 0 ) != 0 ;
		
		$id = $this->getRequestParameter( "id" );
		$search_by_id =  ( !empty ( $id) );
		 
		$tags = $this->getRequestParameter( "tags" );
		
		$c = new Criteria();
		$c->addDescendingOrderByColumn( BBPostPeer::ID );
		$c->setLimit( $page_zise );
		$c->setOffset( $page * $page_zise );
		if ( $search_by_id )
		{
			$c->add ( BBPostPeer::ID , $id );
		}
		else
		{
			$like_tags = "%" . $tags . "%";
			
			$accumulated_or_criterion = $c->getNewCriterion( BBPostPeer::TITLE , $like_tags , Criteria::LIKE ) ;
			$accumulated_or_criterion->addOr ( $c->getNewCriterion( BBPostPeer::CONTENT , $like_tags , Criteria::LIKE ) );
			$c->add ( $accumulated_or_criterion );
		}
		
		$this->count = BBPostPeer::doCount ( $c );
		$this->list = BBPostPeer::doSelectJoinAll( $c );
		$this->id = $id;
		$this->tags = $tags;
		$this->page = $page;
		
	}
}