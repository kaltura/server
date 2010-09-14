<?php
require_once ( "defPartnerservices2Action.class.php");

class pingAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "ping",
				"desc" => "Ping test" ,
				"in" => array (
					"mandatory" => array (
						),
					"optional" => array (
						)
					),
				"out" => array (
					"status" => array ("type" => "string", "desc" => ""),
					),
				"errors" => array (
				)
			);
	}

	protected function ticketType ()	{		return self::REQUIED_TICKET_NONE;	}

	protected function addUserOnDemand ( )	{		return self::CREATE_USER_FALSE;	}

	// we'll allow empty uid here - this is called from just any place in the web with now defined context
	protected function allowEmptyPuser()	{		return true;	}

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$this->addMsg ( "status" , "ok" );
	}
}
?>