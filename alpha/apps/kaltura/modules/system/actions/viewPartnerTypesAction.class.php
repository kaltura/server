<?php
/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
require_once ( "kalturaSystemAction.class.php" );
require_once ( "viewPartnersAAction.class.php" );

/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
class viewPartnerTypesAction extends kalturaSystemAction
{

	public function execute()
	{
		$this->forceSystemAuthentication();

		$this->partners_stat = array();
		
		$start = microtime(true);
		
		$file_path = dirname ( __FILE__ ) . "/../data/viewPartnersData.txt" ;
		$partner_groups = new partnerGroups ( $file_path );
		$this->partner_group_list = $partner_groups->partner_group_list;
		$group_rest = new partnerGroup();
		$group_rest->setName( "_rest" );
		$this->partner_group_list[]= $group_rest; 
		
		$this->from_date = $this->getP ( "from_date"  );
		$this->to_date = $this->getP ( "to_date" ,date("Y-m-d", time()) );
		$this->days = $this->getP ( "days" , 7 );

		if ( $this->days  )
		{
			$timeStamp = strtotime( $this->to_date );
			$timeStamp -= 24 * 60 * 60 * ( $this->days - 1 ) ; // because it's inclusive-inclusive - reduce one day
			$this->from_date =  date("Y-m-d", $timeStamp);
		}
		
		$input_filter = new reportsInputFilter (); 
		$input_filter->from_date = strtotime( $this->from_date );
		$input_filter->to_date = strtotime( $this->to_date );

		list ( $header , $data , $totalCount ) = myReportsMgr::getTable( 
			null , 
			myReportsMgr::REPORT_TYPE_SYSTEM_GENERIC_PARTNER_TYPE , 
			$input_filter ,
			"" , 300 ,
			null  ,  null );
		
		$this->data = $data;
		
		// create total summary line
		$totals = array();
		$i=0;
		foreach ($this->data[0] as $columns )
		{
			$totals[$i] = $i<3 ? "TOTAL" : 0 ;
			$i++;			
		}
		foreach ( $this->data as $line )
		{
			$i=0;			
			foreach ( $line as $val )
			{
				if ( is_numeric ($val ) )
					@$totals[$i]+=$val;
				$i++;	
			}
		}
		$this->data[] = $totals;
		$this->header = $header;	
					
		$end= microtime(true);

		$this->go = $this->getP ( "go" );
		$this->bench = $end - $start;
		$this->format = $this->getP ( "format" , "row" );
	}		

}
?>