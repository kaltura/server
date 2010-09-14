<?php

require_once ( dirname(__FILE__)."/../../partnerservices2/actions/adminloginAction.class.php");

class widgetReportAction extends kalturaAction
{

	public function execute()
	{
		$start = microtime(true);

		$this->go = $this->getP ( "go" );

		$this->ks_str = $this->getP ( "ks_str" );
		$this->partner_id= $this->validateKs ( $this->ks_str )	;
		
		if ( ! $this->ks_str || $this->partner_id === null )
		{
			$this->email = $this->getP ( "email" );
			
			$this->err = "";
			if ( $this->getP ("act") == "login" )
			{
				// admin login
				$admin_login_service = new adminloginAction();
				$_REQUEST["format"] = 6;
				$admin_login_service->	setInputParams ( $_REQUEST );
				
//				var_dump ( $_REQUEST );
				try
				{
					$res = $admin_login_service->internalExecute( null,null,null,null,null);
					if ( $res["error"] )
					{
						$this->err = @$res["error"][0]["desc"]; 
						return sfView::ERROR;
					}
					else
					{
//var_dump ( $res );						
						$this->ks_str = $res["result"]["ks"];
					}
				}
				catch ( Exception $ex )
				{
					$this->err = "Invalid details";
				}
			}
			else
			{
				
				return sfView::ERROR;
			}
		}
			
		$this->from_date = $this->getP ( "from_date"  );
		$this->to_date = $this->getP ( "to_date" ,date("Y-m-d", time() - 2 * 86400) ); // take 2 days back
		$this->days = $this->getP ( "days" , 7 );

		if ( $this->days  )
		{
			$timeStamp = strtotime( $this->to_date );
			$timeStamp -= 24 * 60 * 60 * ( $this->days - 1 ) ; // because it's inclusive-inclusive - reduce one day
			$this->from_date =  date("Y-m-d", $timeStamp);
		}

		if ( $this->go && ( $this->getP ("act") == "report" ))
		{
			$input_filter = new reportsInputFilter ();
			$input_filter->from_date = strtotime( $this->from_date );
			$input_filter->to_date = strtotime( $this->to_date );

			$from_date_id = str_replace ( "-" , "" , $this->from_date );
			$to_date_id = str_replace ( "-" , "" , $this->to_date );

			$debug = $this->getP ( "debug" ) ;
			$map = array
			(
			"partner_id" => $this->partner_id , 
			"from_date_id" => $from_date_id , 
			"to_date_id" => $to_date_id );
				
			try
			{
				list ( $this->query , $this->res , $this->header ) = myReportsMgr::runQuery(
					"system/system_widget_count" , 
					$map ,
					$debug
				);

				if ( $debug )
				{
					var_dump (  $this->query );
				 	die();
				}
			}
			catch ( Exception $ex )
			{
				list ( $this->res , $this->header ) = array ( array ( "Error" ) , array( "error") );  
			}
		}
		else
		{
			$this->query = "";
			 $this->res = null;
		}
		$end= microtime(true);


		$this->bench = $end - $start;
	}

	
	private static function validateKs( $ks_str )
	{
		if ( !$ks_str ) return null;
		// 	1. crack the ks - 
		$ks = kSessionUtils::crackKs ( $ks_str );
		
		// 2. extract partner_id
		$ks_partner_id= $ks->partner_id;

		$partner_id = $ks_partner_id;
		// use the user from the ks if not explicity set 
		$puser_id = $ks->user;
		
		// 4. validate ticket per service for the ticket's partner
		$ticket_type = 2;
		$ks_puser_id = $ks->user;
		$res = kSessionUtils::validateKSession2 ( $ticket_type , $ks_partner_id , $ks_puser_id , $ks_str , $ks );

		if ( 0 >= $res )
		{
			// chaned this to be an exception rather than an error
			return null;
		}
		return $partner_id;
	}
}
?>