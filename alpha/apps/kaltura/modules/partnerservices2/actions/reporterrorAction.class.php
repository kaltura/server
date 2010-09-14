<?php
require_once ( "myInsertEntryHelper.class.php");
require_once ( "myKshowUtils.class.php");
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

class reporterrorAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "reportError",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array (
						),
					"optional" => array (
						"reporting_obj" 				=> array ("type" => "string", "desc" => ""),
						"error_code" 				=> array ("type" => "string", "desc" => ""),
						"error_description" 				=> array ("type" => "string", "desc" => ""),
						)
					),
				"out" => array (
					),
				"errors" => array (
					APIErrors::DUPLICATE_KSHOW_BY_NAME
				)
			);
	}

	protected function ticketType ()	{		return self::REQUIED_TICKET_NONE;	}

	// check to see if already exists in the system = ask to fetch the puser & the kuser
	// don't ask for  KUSER_DATA_KUSER_DATA - because then we won't tell the difference between a missing kuser and a missing puser_kuser
	public function needKuserFromPuser ( )	{		return self::KUSER_DATA_NO_KUSER;	}

	protected function addUserOnDemand ( )	{		return self::CREATE_USER_FALSE;	}

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		// TODO - store to some error filefile
		$reporting_obj = $this->getP ( "reporting_obj" );
		$error_code = $this->getP ( "error_code" );
		$error_desc = $this->getP ( "error_description" );
	}
}
?>