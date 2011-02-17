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
class getPartnerReportAction extends kalturaSystemAction
{
	public function execute()
	{
		ini_set("memory_limit","128M");
		$this->forceSystemAuthentication();
		
		$entry_type = $this->getP ( "entry_type" );
		$partner_id = $this->getP ( "partner_id" );
		
		while(FALSE !== ob_get_clean());
		
		if ($this->getP("go") == "Go" && $partner_id && $entry_type)
		{
			$filename = "partner-$partner_id-$entry_type-". strftime("%Y-%m-%d", time()).".csv";
			header ( "Content-type: application/vnd.ms-excel");
			header ( "Content-Disposition: attachment; filename=\"$filename\"");
		}
		else
		{
			echo "<HTML><BODY>\n";
			echo "<form>\n";
			echo "Partner Id: <input name='partner_id' value='$partner_id'>&nbsp\n";
			echo "<input name='entry_type' type='radio' value='".entryType::MEDIA_CLIP."' ".($entry_type == entryType::MEDIA_CLIP ? "checked" : "").">clips&nbsp;";
			echo "<input name='entry_type' type='radio' value='".entryType::MIX."' ".($entry_type == entryType::MIX ? "checked" : "").">roughcuts&nbsp;";
			echo "<input type='submit' style='color:black' name='go' value='Go'>\n";
			echo "</form>\n";
			
			die;
		}
		
		// gather partner's play and view stats for the last 14 days
		
		$connection = Propel::getConnection();
	    $query = "SELECT ID, KSHOW_ID, NAME from entry where partner_id=$partner_id and type=$entry_type";
		
	    $statement = $connection->prepareStatement($query);
	    $resultset = $statement->executeQuery();
	    
	    $all_entries = array();
	    
	    while ($resultset->next())
	    {
	    	$entry_id = $resultset->getString('ID');
	    	$kshow_id = $resultset->getString('KSHOW_ID');
	    	$name = $resultset->getString('NAME');
	    	
	    	//echo "$entry_id $kshow_id $name<br/>\n";
        	$all_entries[$entry_id] = array("kshow_id" => $kshow_id, "name" => str_replace(',',' ',$name));
	    }
	    
		$query = "SELECT ENTRY_ID, DATE_FORMAT(date,'%y-%m-%d') as DAY,sum(command='play') as PLAYS,sum(command='view') as VIEWS from collect_stats ".
			" where partner_id=$partner_id and date>date_sub(DATE_FORMAT(now(),'%y-%m-%d'), interval 14 day) group by entry_id,day";

	    $statement = $connection->prepareStatement($query);
	    $resultset = $statement->executeQuery();
	    
	    $entries = array();
	    $dates = array();
	    
	    while ($resultset->next())
	    {
	    	$entry_id = $resultset->getString('ENTRY_ID');
	    	$date = $resultset->getString('DAY');
	    	$plays = $resultset->getString('PLAYS');
	    	$views = $resultset->getString('VIEWS');
	    	
	    	//echo "$entry_id $plays $views<br/>\n";
	        if (array_key_exists($entry_id, $all_entries))
	        {
	                if (!array_key_exists($date, $dates))
	                        $dates[$date] = 1;
	
	                if (!array_key_exists($entry_id, $entries))
	                        $entries[$entry_id] = array();
	
	                $stats = &$entries[$entry_id];
	
	                if (!array_key_exists($date, $stats))
	                        $stats[$date] = array(0, 0);
	
	                $stats = &$stats[$date];
	
	                $stats[0] = $plays;
	                $stats[1] = $views;
	        }
	    }
	    
		ksort($dates);
		
		for($i = 0; $i < 2; $i++)
		{
			$s = "entry_id,kshow_id,name,";
			foreach($dates as $date => $val)
			        $s .= "20".$date.",";
		
			echo "$s\n";
		
			foreach($entries as $entry_id => $estats)
			{
		        $s = $entry_id.",".$all_entries[$entry_id]["kshow_id"].",".$all_entries[$entry_id]["name"].",";
		        foreach($dates as $date => $val2)
		        {
		                if (!array_key_exists($date, $estats))
		                        $s .= ",";
		                else
		                        $s .= $estats[$date][$i].",";
		        }
		        echo "$s\n";
			}
		}
		
	    die;
	}
}

?>