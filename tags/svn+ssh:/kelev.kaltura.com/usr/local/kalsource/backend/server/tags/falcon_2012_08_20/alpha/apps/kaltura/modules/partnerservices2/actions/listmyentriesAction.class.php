<?php
/**
 * @package api
 * @subpackage ps2
 */
require_once 'listentriesAction.class.php';

/**
 * @package api
 * @subpackage ps2
 */
class listmyentriesAction extends listentriesAction
{
	public function describe()
	{
		return 	
			array (
				"display_name" => "listMyEntries",
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
	
	protected function ticketType()
	{
		return self::REQUIED_TICKET_REGULAR;
	}
		
	// for this specific kshow list - the ticket is regular and the filter is for all
	// kshows for the current user only 
	protected function setExtraFilters ( entryFilter &$fields_set )
	{
		$fields_set->set( "_eq_user_id" , $this->puser_id );
		$fields_set->set( "_in_type" , entryType::MEDIA_CLIP . "," . entryType::MIX );
	}
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$this->puser_id = $puser_id;
		if( ! $puser_kuser )
		{
			$this->addMsg ( "count" , 0 );
			$this->addMsg ( "page_size" , 0 );
			$this->addMsg ( "page" , 0 );
			$this->addMsg ( $this->getObjectPrefix() , null ) ;
			$this->addError( APIErrors::INVALID_USER_ID ,  $puser_id );
			return;
		}
		parent::executeImpl( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser );
	}
}
?>