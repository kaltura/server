<?php

$directories = glob ( dirname(__FILE__) ."/*" , GLOB_ONLYDIR  );

foreach ( $directories as $dir )
{
echo "[$dir]\n";	
	$files = glob ( $dir ."/*" , GLOB_MARK );
	foreach ( $files as $file )
	{
//		echo "[$file]\n";
		$new_file = strtolower ( $file );
		$new_file = str_replace ( "combined" , "" , $new_file );
		$new_file = preg_replace ( "/[ -]/" , "_" , $new_file );
		$new_file = str_replace ( "___" , "_" , $new_file );
		$new_file = str_replace ( "__" , "_" , $new_file );
		$new_file = str_replace ( "_." , "." , $new_file );
//		$new_file = preg_replace ( "/[_]{2-5}*/" , "_" , $new_file ); // remove double '_' characters
		
		if ( $new_file != $file )
		{
//			echo "Reanming [$file]\n$new_file\n";
			echo "$new_file\n";
			rename( $file , $new_file );
		}
	}
}
?>