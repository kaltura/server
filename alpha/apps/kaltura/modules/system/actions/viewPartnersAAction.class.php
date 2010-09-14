<?php

require_once ( "kalturaSystemAction.class.php" );

class viewPartnersAAction extends kalturaSystemAction
{
	const MAX_PAGE_SIZE = 20000;
	
	
	public function execute()
	{
		ini_set("memory_limit","256M");
		ini_set("max_execution_time","240");
		$this->forceSystemAuthentication();

		$start = microtime(true);
		
		$this->from_date = $this->getP ( "from_date"  );
		$this->to_date = $this->getP ( "to_date" ,date("Y-m-d", time()) );
		$this->days = $this->getP ( "days" , 7 );

		$page = $this->getP ( "page" , 1 );
		if ( $page < 1 ) $page=1;
		$this->page = $page;
		
			
		if ( $this->days  )
		{
			$timeStamp = strtotime( $this->to_date );
			$timeStamp -= 24 * 60 * 60 * ( $this->days - 1 ) ; // because it's inclusive-inclusive - reduce one day
			$this->from_date =  date("Y-m-d", $timeStamp);
		}
		
		$this->new_first = $this->getP ( "new_first" , null );
		if ( $this->new_first == "false" ) $this->new_first = false;

		$this->package_list = array
		(
			"all" => "1=1",
			"paying" => "dim_partner.partner_package>1",
			"kaltura_signup" => "dim_partner.partner_type_id=1 AND dim_partner.partner_package=1",
			"other" => "dim_partner.partner_type_id=1 AND dim_partner.partner_package=2",
			"wiki" => "dim_partner.partner_type_id=1 AND dim_partner.partner_package=100",
			"wordpress" => "dim_partner.partner_type_id=1 AND dim_partner.partner_package=101",
			"drupal" => "dim_partner.partner_type_id=1 AND dim_partner.partner_package=102",
			"mind_touch" => "dim_partner.partner_type_id=1 AND dim_partner.partner_package=103",
			"moodle" => "dim_partner.partner_type_id=1 AND dim_partner.partner_package=104",
			"kaltura_ce" => "dim_partner.partner_type_id=1 AND dim_partner.partner_package=105",
		);
		

   	    
		$updated_at = null;
		$this->selected_package = $this->getP ( "pkg" , "paying");
		$input_filter = new reportsInputFilter (); 
		if ( $this->selected_package )
		{
			$criteria =  @$this->package_list[$this->selected_package];
			$input_filter->extra_map = array ( "{PARTNER_PACKAGE_CRITERIA}" => $criteria );
		}
		$input_filter->from_date = strtotime( $this->from_date );
		$input_filter->to_date = strtotime( $this->to_date );

		$data = $header = null;
		$this->go = $this->getP ( "go" );
		if ( $this->go )
		{		
			list ( $header , $data , $totalCount ) = myReportsMgr::getTable( 
				null , 
				myReportsMgr::REPORT_TYPE_SYSTEM_GENERIC_PARTNER , 
				$input_filter ,
				self::MAX_PAGE_SIZE , $this->page ,
				null  ,  null );
		}	
			
		if ( $data )
		{
			$this->partners_stat = $data;
			$this->data = $data;
		}

		$this->header = $header;	

		$this->updated_at = $updated_at;
		
		$end= microtime(true);

		$this->bench = $end - $start;
	}

}
?>