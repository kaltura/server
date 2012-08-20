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
	$minimum_column_to_display = 1;
    

    foreach ( $header as $col )
    {
    	echo "$col,";
    }
    
    echo "\n";
    
    foreach ( $data as $line )
    {
    	echo implode ( "," , $line );
    	echo "\n";
    }
    
    echo "\n";
    
    die();
} else {
?>
<div style='font-family: verdana; font-size: 12px;'>
This page will create an Excel output for the billing report per partner  
<br><br>
<form>
		FROM (YYYY-MM-DD): <input id="from_date" name="from_date" type="text" size=10 value="<?php echo $from_date ?>" >
		TO (YYYY-MM-DD): <input id="to_date" name="to_date" type="text" size=10 value="<?php echo $to_date ?>" >
		Days: <input id="days" name="days" type="text" size=3 value="<?php echo $days ?>" >
		Format: Rows <input type="radio" name="format" value="row" <?php echo ( $format="row" ? "checked='checked'" : "" ) ?>>
		Columns <input type="radio" name="format" value="col" <?php echo ( $format="col" ? "checked='checked'" : "" ) ?>>
		
		<input type="submit" style='color:black' name="go" value="Go" />

</form>
<br>
<?php
};
    
?>   