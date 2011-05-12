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
class viewPartnersActivityAction extends kalturaSystemAction
{
	public function execute()
	{
		ini_set("memory_limit","128M");
		$this->forceSystemAuthentication();
		
		$date = strtoupper($this->getP ( "date" ));
		$excel = $this->getP ( "excel" );
		$show_days = $this->getP ( "daily" );
		$activity_type = $this->getP ( "activity_type" );
		$selected_partner_id = $this->getP ( "selected_partner_id" );
		$max_lines = $this->getP ( "max_lines", 100 );
		
		$dateFilter = $date == "ALL" ? "" : " AND DATE_FORMAT(ACTIVITY_DATE,'%y-%m')='$date' ";
		$show_days = $show_days != "" && $dateFilter != "";
		$show_months = !$dateFilter;
		
		$fields_names = array();
		
		if ($activity_type == PartnerActivity::PARTNER_ACTIVITY_KDP)
		{
			$activity = PartnerActivity::PARTNER_ACTIVITY_KDP;
			$activity_desc = "Plays is number of plays, views is number of views";
			$activity_desc = "";
			
			$fields_names[1] = "Plays";
			$fields_names[2] = "Views";
			
			$fields_query[1] = "SUM((SUB_ACTIVITY=".PartnerActivity::PARTNER_SUB_ACTIVITY_KDP_PLAYS.")*AMOUNT)";
			$fields_query[2] = "SUM((SUB_ACTIVITY=".PartnerActivity::PARTNER_SUB_ACTIVITY_KDP_VIEWS.")*AMOUNT)";
			
			$fields_map[PartnerActivity::PARTNER_SUB_ACTIVITY_KDP_PLAYS] = 1;
			$fields_map[PartnerActivity::PARTNER_SUB_ACTIVITY_KDP_VIEWS] = 2;
		}
		else if ($activity_type == PartnerActivity::PARTNER_ACTIVITY_STORAGE)
		{
			$activity = PartnerActivity::PARTNER_ACTIVITY_STORAGE;
			$activity_desc = "Storage is amount of storage in MB, Clips is count of media clips";
						
			$fields_names[1] = "Storage";
			$fields_names[2] = "Clips";
			
			$fields_query[1] = "SUM((SUB_ACTIVITY=".PartnerActivity::PARTNER_SUB_ACTIVITY_STORAGE_SIZE.")*AMOUNT)";
			$fields_query[2] = "SUM((SUB_ACTIVITY=".PartnerActivity::PARTNER_SUB_ACTIVITY_STORAGE_COUNT.")*AMOUNT)";
			
			$fields_map[PartnerActivity::PARTNER_SUB_ACTIVITY_STORAGE_SIZE] = 1;
			$fields_map[PartnerActivity::PARTNER_SUB_ACTIVITY_STORAGE_COUNT] = 2;
		}
		else// if ($activity_type == PartnerActivity::PARTNER_ACTIVITY_TRAFFIC)
		{
			$activity = PartnerActivity::PARTNER_ACTIVITY_TRAFFIC;
			$activity_desc = "Limelight, WWW are traffic in MB";
			
			$fields_names[1] = "Limelight";
			$fields_names[2] = "WWW";
			$fields_names[3] = "Level3";
			$fields_names[4] = "Akamai";
			
			$fields_query[1] = "SUM((SUB_ACTIVITY=".PartnerActivity::PARTNER_SUB_ACTIVITY_LIMELIGHT.")*AMOUNT) / 1024";
			$fields_query[2] = "SUM((SUB_ACTIVITY=".PartnerActivity::PARTNER_SUB_ACTIVITY_WWW.")*AMOUNT) / 1024";
			$fields_query[3] = "SUM((SUB_ACTIVITY=".PartnerActivity::PARTNER_SUB_ACTIVITY_LEVEL3.")*AMOUNT) / 1024";
			$fields_query[4] = "SUM((SUB_ACTIVITY=".PartnerActivity::PARTNER_SUB_ACTIVITY_AKAMAI.")*AMOUNT) / 1024";
			
			$fields_map[PartnerActivity::PARTNER_SUB_ACTIVITY_LIMELIGHT] = 1;
			$fields_map[PartnerActivity::PARTNER_SUB_ACTIVITY_WWW] = 2;
			$fields_map[PartnerActivity::PARTNER_SUB_ACTIVITY_LEVEL3] = 3;
			$fields_map[PartnerActivity::PARTNER_SUB_ACTIVITY_AKAMAI] = 4;
		}
		
		$fields_count = count($fields_names);
		
		while(FALSE !== ob_get_clean());
		
		if ($excel && $date)
			header ( "Content-type: application/vnd.ms-excel");
		
		echo "<HTML><BODY>\n";
		if (!$excel)
		{
			echo "<form>\n";
			echo "<input name='activity_type' type='radio' value='".PartnerActivity::PARTNER_ACTIVITY_TRAFFIC."' ".($activity_type == PartnerActivity::PARTNER_ACTIVITY_TRAFFIC ? "checked" : "").">Traffic&nbsp;";
			echo "<input name='activity_type' type='radio' value='".PartnerActivity::PARTNER_ACTIVITY_KDP."' ".($activity_type == PartnerActivity::PARTNER_ACTIVITY_KDP ? "checked" : "").">Player&nbsp;";
			echo "<input name='activity_type' type='radio' value='".PartnerActivity::PARTNER_ACTIVITY_STORAGE."' ".($activity_type == PartnerActivity::PARTNER_ACTIVITY_STORAGE ? "checked" : "").">Storage&nbsp;";
			echo "Date (YY-MM or ALL): <input name='date' value='$date'>&nbsp\n";
			echo "<input name='excel' type='checkbox'> Excel&nbsp\n";
			echo "<input name='daily' type='checkbox' ".($show_days ? "checked" : "")."> Daily breakdown (only for one month)&nbsp\n";
			echo "Partner Id (or free/paying): <input name='selected_partner_id' value='$selected_partner_id'>&nbsp\n";
			echo "Max Lines: <input name='max_lines' value='$max_lines'>&nbsp\n";
			echo "<input type='submit' style='color:black' name='go' value='Go'>\n";
			echo "</form>\n";
			echo "column description: ID is the partner id. $activity_desc, number in parenthesis is number of days<br/>\n";
			echo "when filtering one month, the left most totals are for the month, when selecting all the totals are for the overall time<br/><br/>\n";
		}

		$oddStyle = "bgcolor='cyan'";
		$evenStyle = "bgcolor='lightblue'";
		
		echo "<TABLE>\n";
		echo "<THEAD>\n";

		$partner_where = "";
		if ($selected_partner_id)
		{
			if ($selected_partner_id == "free")
				$partner_where = " AND T2.PARTNER_PACKAGE=1 ";
			else if ($selected_partner_id == "paying")
				$partner_where = " AND T2.PARTNER_PACKAGE<>1 ";
			else
			{
				$partner_where = $selected_partner_id ? " AND T2.ID='$selected_partner_id' " : "";
			}
		}
			
		$connection = Propel::getConnection();
		
		$fields_sql = "";
    	for($i = 1; $i <= $fields_count; $i++)
	    	$fields_sql .= "{$fields_query[$i]} AS F$i,";
	    	
	    $fields_sql = trim($fields_sql, ",");
	    	
	    $query = "SELECT PARTNER_ID, $fields_sql, SUBSTR(PARTNER_NAME,1,20) AS NAME,".
	    	"ADMIN_EMAIL AS EMAIL,SUBSTR(DESCRIPTION,1,80) AS DESCRIPTION, PARTNER_PACKAGE AS PACKAGE FROM partner_activity AS T1,partner AS T2 WHERE ACTIVITY=$activity AND T1.PARTNER_ID=T2.ID $partner_where $dateFilter".
	    	"GROUP BY PARTNER_ID ORDER BY F1 DESC";
	    
	    $statement = $connection->prepareStatement($query);
	    $resultset = $statement->executeQuery();
	    
		$stats["TOTAL"] = array("PACKAGE" => "", "NAME" => "TOTAL", "EMAIL" => "", "DESCRIPTION" => "", "BREAKDOWN" => array());
    	for($i = 1; $i <= $fields_count; $i++)
	    	$stats["TOTAL"]["F$i"] = 0;
	    	
	    while ($resultset->next())
	    {
	    	$partner_id = $resultset->getInt('PARTNER_ID');
	    	$name = $resultset->getString('NAME');
	    	$email = $resultset->getString('EMAIL');
	    	$description = str_replace(array("\r","\n"), "", $resultset->getString('DESCRIPTION'));
	    	$package = $resultset->getString('PACKAGE');
	    	
	    	$stats[$partner_id] = array("PACKAGE" => $package, "NAME" => $name, "EMAIL" => $email, "DESCRIPTION" => $description, "BREAKDOWN" => array());
	    	
	    	for($i = 1; $i <= $fields_count; $i++)
	    	{
	    		$val = $resultset->getInt("F$i");
		    	$stats[$partner_id]["F$i"] = $val;
	    		$stats["TOTAL"]["F$i"] += $val;
	    	}
	    }
	    
		// count days per each month
		$daysDateFilter = $dateFilter ? " AND DATE_FORMAT(ACTIVITY_DATE,'%y-%m')='$date' " : "";
		$query = "SELECT SUB_ACTIVITY,DATE_FORMAT(ACTIVITY_DATE,'%y-%m') as ACT_MONTH,COUNT(DISTINCT DATE_FORMAT(ACTIVITY_DATE,'%y-%m-%d')) AS ACT_DAYS FROM partner_activity AS T1,partner AS T2  WHERE ACTIVITY=$activity AND T1.PARTNER_ID=T2.ID $partner_where $daysDateFilter GROUP BY DATE_FORMAT(ACTIVITY_DATE,'%y-%m'),SUB_ACTIVITY";
		
	    $statement = $connection->prepareStatement($query);
	    $resultset = $statement->executeQuery();
	    
	    $days_cnt = array();
	    for($i = 1; $i <= $fields_count; $i++)
	    	$days_cnt["ALL"]["F$i"] = 0;
	    
	    while ($resultset->next())
	    {
	    	$month = $resultset->getString("ACT_MONTH");
	    	$days = $resultset->getString("ACT_DAYS");
	    	$sub_activity = $fields_map[$resultset->getInt('SUB_ACTIVITY')];
	    	
	    	$days_cnt[$month]["F$sub_activity"] = $days;
	    	$days_cnt["ALL"]["F$sub_activity"] += $days;
	    }
		
	    echo "<TR><TH $evenStyle>ID</TH><TH $evenStyle>Package</TH>";
	    
    	for($i = 1; $i <= $fields_count; $i++)
    	{
    		$style = $i % 2 ? $evenStyle : $oddStyle;
    		echo "<TH $style>".$fields_names[$i]." (".@$days_cnt[$date]["F$i"].")</TH>";
    	}
    	
	    echo "<TH $oddStyle>Name</TH><TH $evenStyle>Email</TH><TH $oddStyle>Description</TH>";
		
	    // add breakdown
		
	    $query = null;
		$breakdown = array();
	    
	    if ($show_days)
	    {
		    $query = "SELECT PARTNER_ID, DATE_FORMAT(ACTIVITY_DATE,'%m-%d') AS ACT_MONTH, $fields_sql FROM partner_activity AS T1,partner AS T2 ".
		    	"WHERE ACTIVITY=$activity AND T1.PARTNER_ID=T2.ID $partner_where AND DATE_FORMAT(ACTIVITY_DATE,'%y-%m')='$date' GROUP BY PARTNER_ID, ACT_MONTH ORDER BY PARTNER_ID, ACT_MONTH DESC";
	    }
	    else if ($show_months)
		{
		    $query = "SELECT PARTNER_ID, DATE_FORMAT(ACTIVITY_DATE,'%y-%m') AS ACT_MONTH, $fields_sql AS F2 FROM partner_activity AS T1,partner AS T2 ".
		    	"WHERE ACTIVITY=$activity AND T1.PARTNER_ID=T2.ID $partner_where GROUP BY PARTNER_ID, ACT_MONTH ORDER BY PARTNER_ID, ACT_MONTH DESC";
		}
		
		if ($query)
		{
		    $statement = $connection->prepareStatement($query);
		    $resultset = $statement->executeQuery();
		    
		    while ($resultset->next())
		    {
		    	$partner_id = $resultset->getInt('PARTNER_ID');
		    	$sub_date = $resultset->getString("ACT_MONTH");
		    	
		    	$breakdown[$sub_date] = true;
		    	
			    $stats[$partner_id]["BREAKDOWN"][$sub_date] = array();
		    	for($i = 1; $i <= $fields_count; $i++)
		    	{
		    		$val = $resultset->getInt("F$i");
			    	$stats[$partner_id]["BREAKDOWN"][$sub_date]["F$i"] = $val;
			    	@$stats["TOTAL"]["BREAKDOWN"][$sub_date]["F$i"] += $val;
		    	}
		    }
		    
		    krsort($breakdown);
		    
		    foreach($breakdown as $sub_date => $value)
		    {
		    	for($i = 1; $i <= $fields_count; $i++)
		    	{
		    		$style = $i % 2 ? $evenStyle : $oddStyle;
		    		
		    		$days = $show_days ? "" : "(".$days_cnt[$sub_date]["F$i"].")";
		    		echo "<TH $style>".$fields_names[$i].$days."<BR/>$sub_date</TH>";
		    	}
		    }
		}
		    
		echo "</TR>\n";
		echo "</THEAD>\n";
		echo "<TBODY>\n";
		
		$rows = 0;
			
		foreach($stats as $partner_id => $stat)
	    {
	    	if ($max_lines && $rows > $max_lines)
	    		break;
	    		
	    	$rows++;
	    		
	    	$name = @$stat["NAME"];
	    	$email = @$stat["EMAIL"];
	    	$description = @$stat["DESCRIPTION"];
	    	$breakdown_stats = @$stat["BREAKDOWN"];
	    	$pacakge = @$stat["PACKAGE"];

  			echo "<TR><TD $evenStyle>$partner_id</TD><TD $evenStyle>$pacakge</TD>";
  			
	    	for($i = 1; $i <= $fields_count; $i++)
			{
				$style = $i % 2 ? $evenStyle : $oddStyle;
		    	$val = @$stat["F$i"];
				
   				echo "<TD $style>$val</TD>";
			}
			
			echo "<TD $oddStyle>$name</TD><TD $evenStyle>$email</TD><TD $oddStyle>$description</TD>";

   			if (count($breakdown))
		    {
	   			foreach($breakdown as $sub_date => $value)
	   			{
			    	for($i = 1; $i <= $fields_count; $i++)
			    	{
			    		$style = $i % 2 ? $evenStyle : $oddStyle;
	   				
		   				if (array_key_exists($sub_date, $breakdown_stats))
		   				{
		   					$val = $breakdown_stats[$sub_date]["F$i"];
		   				}
		   				else
		   				{
		   					$val = "";
		   				}
	   				
					    echo "<TD $style>$val</TD>";
			    	}
	   			}
		    }
   			
	    	echo "</TR>\n";
	    }
	    
		echo "</TBODY>\n";
		echo "</TABLE>\n";
		echo "</BODY></HTML>\n";
		
	    die;
	}
}

