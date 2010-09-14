<?php
class myNuconomyUtils
{
	const DATA_FILE_PATH = "/web/nuconomy/nuconomy_openid_list";
	
	// TODO - replace with a function that read one line at a time rather than read all into memory 
	public static function getDataForOpenId ( $open_id  )
	{
		// TODO - this should not happen ! There must be a valid nuconomy file in the future
		if ( ! file_exists( self::DATA_FILE_PATH )) return null;
		
		$content = file_get_contents( self::DATA_FILE_PATH );
		$lines = explode ( "\n" , $content );
		foreach ( $lines as $line )
		{
			$single_data = explode ( "," , $line );
//print_r ( $single_data );			
			// assume the structure is 
			//	openid	projectToken	projectSecret
			if ( $open_id == trim(@$single_data[0] ) )
			{
				return $single_data;
			}
		}
		
		return null;
	}
}
?>