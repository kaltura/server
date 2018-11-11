<?php
class myCsvReport
{
	public static function createReport ( $report_title , $report_text , $headers , 
		$report_type , reportsInputFilter $input_filter , $dimension ,
		$graphs , $total_header , $total_data , $table_header , $table_data , $table_total_count, $csv)
	{
		
		list ( $total_dictionary , $table_dictionary ) = self::buildDictionaries ( $headers );
		
		// store on disk
		$csv = new myCsvWrapper ();
		$csv->addNewLine( $report_title);
		$origTimeZone = date_default_timezone_get ();
        date_default_timezone_set('UTC');
		$csv->addNewLine( $csv->formatDate($input_filter->from_date) , $csv->formatDate($input_filter->to_date)  );
		date_default_timezone_set($origTimeZone);
		$csv->addNewLine( $report_text );
		$csv->addNewLine( "# ------------------------------------" );
		$csv->addNewLine( "" );
		
		if ($graphs) 
		{
			$csv->addNewLine( "# ------------------------------------" );
			$csv->addNewLine( "# Graph" );
			$csv->addNewLine( "# ------------------------------------" );
			$csv->addNewLine( "" );
			
			if ( $dimension )
			{
				$graph = @$graphs[$dimension];
			}
			else
			{
				$graph = $graphs;
			}

					
			foreach ( $graph as $data => $value )
			{
				if ( $report_type == myReportsMgr::REPORT_TYPE_CONTENT_DROPOFF || $report_type == myReportsMgr::REPORT_TYPE_USER_CONTENT_DROPOFF
					|| $report_type == myReportsMgr::REPORT_TYPE_OPERATING_SYSTEM || $report_type == myReportsMgr::REPORT_TYPE_BROWSERS)
				{
					$csv->addNewLine( $data , $value );
				}	
				else if($dimension)
				{	
					$added = false;
					if (strlen($data) == 6) //foramt is yyyymm 
					{ 
						$date = DateTime::createFromFormat("Ym", $data);
						if($date)
						{
							$csv->addNewLine( $date->format("M Y"), $value );
							$added = true;
						}
					} else if (strlen($data) == 10) //format is yyyymmddhh
					{
						$csv->addNewLine( $csv->formatTime( myReportsMgr::formatDateFromDateId( $data ) ), $value );
						$added = true;
					}
					
					if(!$added)
					{
						$csv->addNewLine( $csv->formatDate( myReportsMgr::formatDateFromDateId( $data ) ), $value );
					}		
				}
				
			}
		}
		
		if ($total_data)
		{
			$csv->addNewLine( "" );
			$csv->addNewLine( "# ------------------------------------" );
			$csv->addNewLine( "# Total" );
			$csv->addNewLine( "# ------------------------------------" );
			$csv->addNewLine($total_dictionary/* $total_header */);
			$csv->addNewLine(array_slice($total_data, 0, count($total_dictionary)));
		}
		
		
		if ($table_data)
		{
			$csv->addNewLine( "" );
			$csv->addNewLine( "# ------------------------------------" );
			$csv->addNewLine( "# Table" , "" , "Total Count" , $table_total_count );
			$csv->addNewLine( "# ------------------------------------" );
			$table_headers_count = count($table_dictionary);
			$csv->addNewLine( $table_dictionary /* $table_header */);
			foreach ( $table_data as $row )
			{
				$csv->addNewLine(array_slice($row, 0, $table_headers_count));
			}
		}
		
		return $csv;
	}
	
	public static function appendLines (myCsvWrapper $csv , $table_data)
	{
	        foreach ( $table_data as $row )
		{
			$csv->addNewLine( $row );
		}

		return $csv;
	}


	private static function buildDictionaries ( $headers ) 
	{
		list ( $total_dictionary_str ,$table_dictionary_str ) = explode ( ";" , $headers );
		$total_dictionary = explode ( "," , $total_dictionary_str );
		$table_dictionary = explode ( "," , $table_dictionary_str );
		return array ( $total_dictionary , $table_dictionary ) ;	
	}
	
}
?>
