<?php
/**
 * @package api
 * @subpackage ps2
 */
class getadmintagsAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "getAdminTags",
				"desc" => "Return a string with all the admin tags ordered in alphabetic order separated by ','",
				"in" => array (
					"mandatory" => array ( 
						),
					"optional" => array (
						)
					),
				"out" => array (
					"adminTags" => array ("type" => "string", "desc" => "")
					),
				"errors" => array (
				)
			); 
	}
	

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$admin_tags = ktagword::getAdminTags( $partner_id );
		$this->addMsg ( "adminTags" , $admin_tags );
	}
}
?>