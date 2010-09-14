<?php
require_once ( "listmyentriesAction.class.php");


class listmydvdentriesAction extends listmyentriesAction
{
	public function describe()
	{
		return 	
			array (
				"display_name" => "listMyDvdEntries",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"filter" => array ("type" => "entryFilter", "desc" => "")
						),
					"optional" => array (
						"detailed" => array ("type" => "boolean", "desc" => ""),
						"page_size" => array ("type" => "integer", "default" => 10, "desc" => ""),
						"page" => array ("type" => "boolean", "default" => 1, "desc" => ""),
						"use_filter_puser_id" => array ("type" => "boolean", "desc" => ""),
						)
					),
				"out" => array (
					"count" => array ("type" => "integer", "desc" => ""),
					"page_size" => array ("type" => "integer", "desc" => ""),
					"page" => array ("type" => "integer", "desc" => ""),
					"entrys" => array ("type" => "*entry", "desc" => ""),
					"user" => array ("type" => "kuser", "desc" => ""),
					),
				"errors" => array (
				)
			); 
	}
	
	// for this specific kshow list - the ticket is regular and the filter is for all
	// kshows for the current user only 
	protected function setExtraFilters ( entryFilter &$fields_set )
	{
		$fields_set->set( "_eq_user_id" , $this->puser_id );
		$fields_set->set( "_eq_type" , entry::ENTRY_TYPE_DVD );
	}
	
	protected function getObjectPrefix () { return "dvdEntries"; }
	
}
?>