<?php
/**
 * @package api
 * @subpackage ps2
 */
class startsessionAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "startSession",
				"desc" => "Starts new kaltura session." ,
				"in" => array (
					"mandatory" => array (
						"secret" 		=> array ("type" => "string", "desc" => ""),
						),
					"optional" => array (
						"admin" 		=> array ("type" => "string", "desc" => ""),
						"privileges" 	=> array ("type" => "string", "desc" => ""),
						"expiry" 		=> array ("type" => "integer", "default" => "86400", "desc" => "")
						)
					),
				"out" => array (
					"ks" => array ("type" => "string", "desc" => ""),
					"partner_id" => array ("type" => "string", "desc" => ""),
					"subp_id" => array ("type" => "string", "desc" => ""),
					"uid" => array ("type" => "string", "desc" => "")
					),
				"errors" => array (
					APIErrors::START_SESSION_ERROR ,
				)
			);
	}

	protected function ticketType ()	{		return self::REQUIED_TICKET_NONE;	}

	protected function addUserOnDemand ( )
	{
		// TODO - optimize !!
		return self::CREATE_USER_FALSE;
//		return self::CREATE_USER_FROM_PARTNER_SETTINGS;;
	}

	protected function needKuserFromPuser ( )
	{
		return self::KUSER_DATA_NO_KUSER;
	}

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		// make sure the secret fits the one in the partner's table
		$ks = "";
		$expiry = $this->getP ( "expiry" , 86400 );
		$admin = $this->getP ( "admin" , false);
		$privileges = $this->getP ( "privileges" , null );

		$result = kSessionUtils::startKSession ( $partner_id , $this->getPM ( "secret" ) , $puser_id , $ks , $expiry , $admin , "" , $privileges );

		if ( $result >= 0 )
		{
			$this->addMsg ( "ks" , $ks );
			$this->addMsg ( "partner_id" , $partner_id );
			$this->addMsg ( "subp_id" , $subp_id );
			$this->addMsg ( "uid" , $puser_id );
		}
		else
		{
			// TODO - see that there is a good error for when the invalid login count exceed s the max
			$this->addError( APIErrors::START_SESSION_ERROR ,$partner_id );
			$this->addDebug( "error" , $result );
		}

	}
}
?>