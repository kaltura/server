<?php
class myCsvReport
{
	public static function createReport ( $report_title , $report_text , $headers , 
		$report_type , reportsInputFilter $input_filter , $dimension ,
		$graphs , $total_header , $total_data , $table_header , $table_data , $table_total_count )
	{
		
		list ( $total_dictionary , $table_dictionary ) = self::buildDictionaries ( $headers );
		
		// store on disk
		$csv = new myCsvWrapper ();
		$csv->addNewLine( $report_title);
		$csv->addNewLine( $csv->formatDate($input_filter->from_date) , $csv->formatDate($input_filter->to_date ) );
		$csv->addNewLine( $report_text );
		$csv->addNewLine( "# ------------------------------------" );
		$csv->addNewLine( "" );
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
			if ( $report_type == myReportsMgr::REPORT_TYPE_CONTENT_DROPOFF )
			{
				$csv->addNewLine( $data , $value );
			}	
			else
			{
				$csv->addNewLine( $csv->formatDate( myReportsMgr::formatDateFromDateId( $data ) ), $value );		
			}
			
		}
		
		$csv->addNewLine( "" );
		$csv->addNewLine( "# ------------------------------------" );
		$csv->addNewLine( "# Total" );
		$csv->addNewLine( "# ------------------------------------" );		
		$csv->addNewLine( $total_dictionary /* $total_header */);
		$csv->addNewLine( $total_data );
		
		$csv->addNewLine( "" );
		$csv->addNewLine( "# ------------------------------------" );
		$csv->addNewLine( "# Table" , "" , "Total Count" , $table_total_count );
		$csv->addNewLine( "# ------------------------------------" );		
		$csv->addNewLine( $table_dictionary /* $table_header */);
		foreach ( $table_data as $row )
		{
			$csv->addNewLine( $row );
		}
		
		$data = $csv->getData();

		return $data;
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