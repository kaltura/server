<?php
/**
 * @package api
 * @subpackage ps2
 */
class listusersAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "getUser",
				"desc" => "",
				"in" => array (
					"optional" => array (
						"page" => array ( "type" => "integer" ),
						"page_size" => array ( "type" => "integer" ),
						"detailed" => array("type" => "boolean", "desc" => "")
						)
					),
				"out" => array (
					"users" => array ("type" => "*PuserKuser", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_USER_ID ,
				)
			); 
	}
	
	protected function ticketType()	{		return self::REQUIED_TICKET_ADMIN;	}
	public function needKuserFromPuser ( )	{		return self::KUSER_DATA_NO_KUSER;	}
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		// the relevant puser_kuser is the one from the user_id NOT the uid (which is the logged in user investigationg
		//$target_puser_kuser = PuserKuserPeer::retrieveByPartnerAndUid($partner_id , null , $user_id , true );
		$page = $this->getP("page");
		$page_size = $this->getP("page_size");
		$detailed = $this->getP ( "detailed" , false );
		
		$c = new Criteria();
		$c->addAnd(kuserPeer::PARTNER_ID, $partner_id, Criteria::EQUAL );
		$c->setLimit($page_size);
		$c->setOffset(($page-1)*$page_size);
		$users = kuserPeer::doSelect($c);

		$extra_fields = array();
		if ($detailed)
		{
			$extra_fields = array ( "country" , "state" , "city"  , "zip" , "urlList" , "networkHighschool" , "networkCollege" , "views" , "fans" , "entries" , "producedKshows" );
		}
		$level = objectWrapperBase::DETAIL_LEVEL_REGULAR;
		
		$this->addMsg ( "count" , count($users) );
		$this->addMsg ( "page" ,  $page );
		$this->addMsg ( "pageSize" , $page_size );
		$this->addMsg ( "users" , objectWrapperBase::getWrapperClass( $users , $level, objectWrapperBase::DETAIL_VELOCITY_DEFAULT , 0 , $extra_fields ) );
	}
}
?>