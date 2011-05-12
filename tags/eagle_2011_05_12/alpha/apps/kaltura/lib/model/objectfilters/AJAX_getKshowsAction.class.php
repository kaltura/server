<?php
require_once( 'AJAX_getObjectsAction.class.php');

class AJAX_getKshowsAction extends AJAX_getObjectsAction
{
	public function getPagerName( ) 		{ return "kshow" ; }
	public function getFiler () 			{ return new kshowFilter() ;	}
	public function getComlumnNames () 		{ return kshow::getColumnNames() ; } // alter table kshow add FULLTEXT ( name , description , tags );
	public function getSearchableColumnName () 		{ return kshow::getSearchableColumnName(); } //  alter table entry add FULLTEXT ( name , tags );
	public function getFilterPrefix ( ) 	{ return "kshow_filter_" ; }
	public function getPeerMethod ()		{ return "doSelectJoinkuser" ; }
	public function getPeerCountMethod () 	{ return "doCountWithLimit" ; } //"doCountJoinkuser" ; }

	public function getOrCriterion( Criteria $c )
	{
		$res = null;
		if ( $this->or_category ) // use the category with OR with tagword-complex-criteria
		{
			if ( $this->category != NULL && $this->category >= 0  )
			{
				$res =  $c->getNewCriterion ( kshowPeer::TYPE , $this->category );
			}
		}
		
		return $res;
	}

	public function modifyCriteria ( Criteria $c )
	{
//		$c->addJoin( kshowPeer::PRODUCER_ID , kuserPeer::ID , Criteria::JOIN);
		$c->addJoin( kshowPeer::PRODUCER_ID , kuserPeer::ID , Criteria::LEFT_JOIN);

		if ( !$this->or_category ) // use the category with AND with tagword-complex-criteria
		{
			if ( $this->category != NULL && $this->category >= 0  )
			{
				$c->add ( kshowPeer::TYPE , $this->category );
			}
		}
/*
		// always filter out all those partner_ids that are not public
		$c->addAnd ( kshowPeer::ID , kshow::MINIMUM_ID_TO_DISPLAY , Criteria::GREATER_THAN );
		$c->addAnd ( kshowPeer::PARTNER_ID, myPartnerUtils::PUBLIC_PARTNER_INDEX , Criteria::LESS_EQUAL );
*/
	}


	public function getSortArray ( )
	{
		//date | rating | views | type
		$sort_aliases = array (
		"date" => "-created_at" ,
		"rank" => "-rank" ,
		"views" => "-views" ,
		"type" => "+type" ,
		"comments" => "-comments" ,
		"ids" => "+id" );
		return $sort_aliases;
	}
	public function getDefaultSort ( )
	{
		return "-created_at";
	}

/*
	public function getTopImpl ()
	{
		return kshowPeer::getTopKshows();
	}
*/
}
?>