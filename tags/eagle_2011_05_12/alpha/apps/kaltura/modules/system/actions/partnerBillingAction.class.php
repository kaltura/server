<?php
/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
require_once ( "kalturaSystemAction.class.php" );

/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
class partnerBillingAction extends kalturaSystemAction
{

	public function execute()
	{
		$this->forceSystemAuthentication();

		$this->partners_stat = array();
		
		$start = microtime(true);
		$this->from_date = $this->getP ( "from_date"  );
		$this->to_date = $this->getP ( "to_date" ,date("Y-m-d", time()) );
		$this->days = $this->getP ( "days" , 7 );

		$this->data = array();
		$totals = array();
		$header = "";
			
		if ( $this->days  )
		{
			$timeStamp = strtotime( $this->to_date );
			$timeStamp -= 24 * 60 * 60 * ( $this->days - 1 ) ; // because it's inclusive-inclusive - reduce one day
			$this->from_date =  date("Y-m-d", $timeStamp);
		}

		if ( $this->getP ( "go" ) )
		{
			
			$input_filter = new reportsInputFilter (); 
			$input_filter->from_date = strtotime( $this->from_date );
			$input_filter->to_date = strtotime( $this->to_date );
	
			list ( $header , $data , $totalCount ) = myReportsMgr::getTable( 
				null , 
				"system/system_generic_partner_billing" , 
				$input_filter ,
				"" , 300 ,
				null  ,  null );
			
			$this->data = $data;
			
			// create total summary line
/*
			$i=0;
			foreach ($this->data[0] as $columns )
			{
				$totals[$i] = $i<1 ? "TOTAL" : 0 ;
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
			*/
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