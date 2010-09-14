<?php
require_once( 'AJAX_getObjectsAction.class.php');

class AJAX_getKusersAction extends AJAX_getObjectsAction
{
	public function getPagerName( ) 		{ return "kuser" ; }
	public function getFiler () 			{ return new kuserFilter() ;	}
	public function getComlumnNames () 		{ return kuser::getColumnNames(); } // alter table kuser add FULLTEXT ( screen_name , full_name , url_list , tags , about_me , network_highschool , network_college ,network_other ) ;
	public function getSearchableColumnName () 		{   return kuser::getSearchableColumnName() ; } //  alter table entry add FULLTEXT ( name , tags );
	public function getFilterPrefix ( ) 	{ return "kuser_filter_" ; }
	public function getPeerMethod ()		{ return NULL ; }
	public function getPeerCountMethod () 	{ return "doCountWithLimit" ; }

	public function modifyCriteria ( Criteria $c )
	{
/*		
		$c->addAnd ( kuserPeer::ID , kuser::MINIMUM_ID_TO_DISPLAY , Criteria::GREATER_THAN );
		$c->addAnd ( kuserPeer::STATUS , kuser::KUSER_STATUS_ACTIVE );

		// always filter out all those partner_ids that are not null  
		$c->addAnd ( kuserPeer::PARTNER_ID, myPartnerUtils::PUBLIC_PARTNER_INDEX , Criteria::LESS_EQUAL );
*/
	}
	
	public function getSortArray ( )
	{
		//screen_name | last_update | views | num_of_media
		$sort_aliases = array (
		 	"screen_name" => "+screen_name" ,
		 	"date" => "-updated_at" , 
			"views" => "-views" ,
			"num_of_media" => "-entries" ,
			"num_of_kalturas" => "-produced_kshows" ,
			"ids" => "+id" );
		return $sort_aliases;			
	}
	public function getDefaultSort ( )
	{
		return "-views";
	}	
	
/*
	public function getTopImpl ()
	{
		return kuserPeer::getTopKusers ();
	}
	*/
}
?>