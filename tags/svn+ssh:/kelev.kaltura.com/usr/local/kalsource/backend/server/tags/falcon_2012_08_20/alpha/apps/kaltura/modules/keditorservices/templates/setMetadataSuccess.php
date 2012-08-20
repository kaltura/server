<?php

if ( $debug ) 
{ 
	echo $comments . "<br>";
	echo form_tag( "keditorservices/setMetadata" );
//	echo form_tag( "keditorservices" );
	echo input_hidden_tag( "kshow_id" , $kshow_id );
	echo "Debug:" . checkbox_tag("debug", "true" , $debug );
	echo submit_tag();
	echo "Result<br><textarea name='xml' cols=100 rows=50>"; 

}

echo $xml_content;

if ( $debug ) { echo "</textarea></form>" ; }
?>
