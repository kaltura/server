<?php
class StubReports
{
/*
 * 	const REPORT_FLAVOR_GRAPH= 1;
	const REPORT_FLAVOR_TOTAL= 2;
	const REPORT_FLAVOR_TABLE= 3;
 */	
	public static function STUBexecuteQueryByType ( $partner_id , $report_type , $report_flavor , reportsInputFilter $input_filter  ,
			$page_size , $page_index , $order_by )
	{
		if ( $report_type == 1 )
		{
			if ( $report_flavor == 1 )
			{
//SELECT plays.event_date_id,count_plays,distinct_plays,sum_time_viewed 	,avg_time_viewed,count_loads				
				$from = floor( $input_filter->from_date / 86400 );
				$to = ceil( $input_filter->to_date / 86400 ) ;
				
				if ( $to > $from + 1000) $to = $from + 1000;

				$graphs = array ( 
					"count_plays" ,
					"distinct_plays"  ,
					"sum_time_viewed"  ,
					"avg_time_viewed"  ,
					"count_loads"  ,
					);
				
					$last_val = -1;
				$table = array ( );
				for ( $i=$from ; $i<= $to ; $i++)
				{
//echo $i . "<br>";					
					$record = array ( "event_date_id" => $i*86400  ); // first place is the date_id //array ( "event_date_id" => date ( "Ymd" , $i*86400 ) ); // first place is the date_id
					foreach ( $graphs as $graph )
					{
						if ( $last_val < 0 )
							$val = mt_rand ( 0 , 400 );
						else
							$val = $last_val + mt_rand ( -10 , 10 ); 
						if ( $val < 0 ) $val = 0;
						
						$last_val = $val;
						$record[$graph] = $val;
					}
					$table[] = $record;
				}
				
				return $table;
			}
			elseif ( $report_flavor == 2 )
			{
//count_plays,distinct_plays,sum_time_viewed	,avg_time_viewed,count_loads,count_plays/count_loads load_play_ratio				
				$header = array (
					"count_plays" ,
					"distinct_plays" , 
					"sum_time_viewed" , 
					"avg_time_viewed" ,
					"count_loads" ,
					"load_play_ratio" ,
				);

//				$data = array();
				$record = array ( );
				foreach ( $header as $colname )
				{
					$record[$colname] = mt_rand ( 0 , 200 );
				}
				$data[] = $record;
				
				return $data;
			}
			elseif ( $report_flavor == 3 )
			{
				$header = array (
					"entry_id" ,
					"entry_name" , 
					"event_date_id" , 
					"count_plays" ,
					"distinct_plays" ,
					"sum_time_viewed" ,
					"avg_time_viewed" ,
					"count_loads" ,
					"load_play_ratio" , 
				);
				
				$num = count($header);
				if ( $page_size > 100 ) $page_size = 100;
//SELECT plays.entry_id,plays.entry_name,plays.event_date_id,count_plays,distinct_plays,sum_time_viewed	,
// avg_time_viewed,count_loads,count_loads,count_plays/count_loads load_play_ratio
				$data = array();
				for ( $i = 0 ; $i<$page_size ; $i++ )
				{
					$record = array ( );
					foreach ( $header as $colname )
					{
						$record[$colname] = mt_rand ( 0 , 200 );
					}
					$data[] = $record;
				}
				
				return $data ;
			}
		}
//die();		
		
	}
}
?>