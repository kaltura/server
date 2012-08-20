<?php
if ( $go ) {     

	global $g_excel;
	$g_excel = true;
	$excel = true;
	while(FALSE !== ob_get_clean());
	$content_type ="application/vnd.ms-excel";
//	$content_type ="text/plain";
	// excel
	$filename = "viewPartners_summary_{$from_date}_{$to_date}.csv";

  	header("Content-Disposition: attachment; filename=\"$filename\"");	
	header("Content-type: $content_type");

	header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");;
//    header("Content-Disposition: attachment; filename=\"$filename\"");	
//    header("Content-Transfer-Encoding: binary ");
	
	    foreach ( $header as $col )
	    {
	    	echo "$col,";
	    }
	    
	    echo "\n";
	    
	    foreach ( $res as $line )
	    {
	    	$first = true;
	    	foreach ( $line as $name => $value )
	    	{
	    		if ( !$first )
	    			echo ",";
	    		$first = false;
	    		echo $value;
	    	}
	    	echo "\n";
	    }
	    
	    echo "\n";
    
    die();
} else {
?>
<div style='font-family: verdana; font-size: 12px; text-indent: 50px'>
This page will create an Excel output for the widget report 
<br><br>
<form>
	<input type="hidden" name="act" value="report">
	<input type="hidden" name="ks_str" value="<?php echo $ks_str ?>">
		FROM (YYYY-MM-DD): <input id="from_date" name="from_date" type="text" size=10 value="<?php echo $from_date ?>" >
		TO (YYYY-MM-DD): <input id="to_date" name="to_date" type="text" size=10 value="<?php echo $to_date ?>" > 
		<span style="font-weight:bold;">*INCLUSIVE*</span>
		
		<input type="submit" style='color:black' name="go" value="Go" />

</form>
<br>
</div>
<?php
};
    
?>   