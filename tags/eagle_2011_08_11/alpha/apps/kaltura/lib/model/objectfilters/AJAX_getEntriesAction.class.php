<?php
require_once( 'AJAX_getObjectsAction.class.php');

class AJAX_getEntriesAction extends AJAX_getObjectsAction
{
	private $kuser_id = null;
	
	private $public_only = false;
	
	private $media_type = null;
	
	//private static 
	public function getPagerName( ) 		{ return "entry" ; }
	public function getFiler () 			{ return new entryFilter() ;	}
	public function getComlumnNames () 		{ return entry::getColumnNames() ; } //  alter table entry add FULLTEXT ( name , tags );
	public function getSearchableColumnName () 		{ return entry::getSearchableColumnName(); } //  alter table entry add FULLTEXT ( name , tags );
	public function getFilterPrefix ( ) 	{ return "entry_filter_" ; }
	public function getPeerMethod ()		{ return "doSelect"; } //return "doSelectJoinkuser" ; }
	public function getPeerCountMethod () 	{ return "doCountWithLimit" ; } //"doCountJoinAll" ; } //return "doCountJoinkuser" ; }

	public function setPublicOnly ( $v )
	{
		$this->public_only = $v;
	}

	public function setOnlyForKuser ( $kuser_id )
	{
		$this->kuser_id = $kuser_id;
	}
	
	public function setMediaType ( $media_type )
	{
		$this->media_type = $media_type;
	}
	
	
	public function modifyCriteria ( Criteria $c )
	{
//		entryPeer::setUseCriteriaFilter( false );
		
//		$c->addJoin( entryPeer::KSHOW_ID , kshowPeer::ID , Criteria::LEFT_JOIN);
//		$c->addJoin( entryPeer::KUSER_ID , kuserPeer::ID , Criteria::LEFT_JOIN);

		if ( $this->kuser_id  )
		{
			$c->addAnd ( entryPeer::KUSER_ID ,  $this->kuser_id );
		}
		
		if ( $this->media_type )
		{
			$c->addAnd ( entryPeer::MEDIA_TYPE , $this->media_type );
		}
	}
	
	public function getSortArray ( )
	{
		//rating | date | views
		$sort_aliases = array ( 	
			"rank" => "-rank" , 
			"date" => "-created_at" , 
			"views" => "-views" ,
			"ids" => "+id" );
		return $sort_aliases;			
	}
	public function getDefaultSort ( )
	{
		return "-views" ; //"-created_at";
	}
	
/*	public function getTopImpl ()
	{
		return entryPeer::getTopEntries ();
	}
*/
}
?>