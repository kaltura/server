<?php

require_once ( "kalturaSystemAction.class.php" );

class statusAction extends kalturaSystemAction
{
	private $connection;
	
	private function dumpOOConvertErrors($query)
	{
		$headerStyle = "bgcolor='lightgray'";
		$oddStyle = "bgcolor='cyan'";
		$evenStyle = "bgcolor='lightblue'";
		
		$statement = $this->connection->prepareStatement($query);
	    $resultset = $statement->executeQuery();
		
		$has_results = $resultset->getRecordCount() > 0;
		$title = "OOConversion Errors";
		
		echo "<H2>$title ".($has_results ? "" : "- NONE")."</H2>";
		echo $query;
		
		if ($has_results)
		{
			$lines = array();
			$lines[] = array();
			$lines[] = array();
    		
			while ($resultset->next())
			{
				$partner_id = $resultset->getInt("partner_id");
				$entry_id = $resultset->getString("entry_id");
				$date = $resultset->getDate("date", "%Y-%m-%d %H:%M:%S");
				$diff = $resultset->getString("diff");
				$source = "";
				
				$entry = entryPeer::retrieveByPK($entry_id);
				
				if ($entry)
				{
					$source = $entry->getSource();
					
					$batchjob_query = "SELECT description FROM batch_job WHERE job_type=".BatchJob::BATCHJOB_TYPE_IMPORT." and entry_id='$entry_id'";
					$batchjob_statement = $this->connection->prepareStatement($batchjob_query);
				    $batchjob_resultset = $batchjob_statement->executeQuery();
				    $batchjob_description = "NO BATCHJOB";
				    
					while ($batchjob_resultset->next())
					{
						$batchjob_description = $batchjob_resultset->get("description");
						break;
					}
					
					$data_path = myContentStorage::getFSContentRootPath() . $entry->getDataPath();
					$size = filesize($data_path);
					
					$list_type = $size > 0 ? 0 : 1;
					$cnt = count($lines[$list_type]) + 1;
					$lines[$list_type][] = "<TD>$cnt</TD><TD>$partner_id</TD><TD>$entry_id</TD><TD><A target='_blank' href='/index.php/system/investigate?entry_id=$entry_id'>$data_path</A></TD><TD>$size</TD><TD>$source</TD><TD>$date</TD><TD>$diff</TD><TD>$batchjob_description</TD>";
				}
			}
			
			for($j = 0; $j < 2; $j++)
			{
				if (!count($lines[$j]))
					continue;
					
				echo "<TABLE>\n";
				echo "<THEAD>\n";
				echo "<TR $headerStyle><TH>#</TH><TH>PARTNER_ID</TH><TH>ID</TH><TH>PATH</TH><TH>SIZE</TH><TH>SOURCE</TH><TH>LAST DATE</TH><TH>PASSED TIME</TH><TH>IMPORT ERROR</TH></TR>";
					
				echo "</THEAD>\n";
				echo "<TBODY>\n";
			
				$i = 0;
				foreach($lines[$j] as $line)
				{
					$style = ++$i % 2 ? $evenStyle : $oddStyle;
					echo "<TR $style>$line</TR>";
				}
				
				echo "</TBODY>\n";
				echo "</TABLE>\n";
				echo "<BR/>\n";
			}
		}
	}

	private function dumpConvertErrors($query)
	{
		$headerStyle = "bgcolor='lightgray'";
		$oddStyle = "bgcolor='cyan'";
		$evenStyle = "bgcolor='lightblue'";
		
		$statement = $this->connection->prepareStatement($query);
	    $resultset = $statement->executeQuery();
		
		$has_results = $resultset->getRecordCount() > 0;
		$title = "Conversion Errors";
		
		echo "<H2>$title ".($has_results ? "" : "- NONE")."</H2>";
		echo $query;
		
		if ($has_results)
		{
			$lines = array();
			$lines[] = array();
			$lines[] = array();
    		
			while ($resultset->next())
			{
				$partner_id = $resultset->getInt("partner_id");
				$entry_id = $resultset->getString("id");
				$source = $resultset->getInt("source");
				$date = $resultset->getDate("date", "%Y-%m-%d %H:%M:%S");
				$diff = $resultset->getString("diff");
				
				$entry = entryPeer::retrieveByPK($entry_id);
				
				if ($entry)
				{
					$batchjob_query = "SELECT description FROM batch_job WHERE job_type=".BatchJob::BATCHJOB_TYPE_IMPORT." and entry_id='$entry_id'";
					$batchjob_statement = $this->connection->prepareStatement($batchjob_query);
				    $batchjob_resultset = $batchjob_statement->executeQuery();
				    $batchjob_description = "NO BATCHJOB";
				    
					while ($batchjob_resultset->next())
					{
						$batchjob_description = $batchjob_resultset->get("description");
						break;
					}
					
					$data_path = myContentStorage::getFSContentRootPath() . $entry->getDataPath();
					$data_dir = pathinfo( $data_path, PATHINFO_DIRNAME );
					$archive_file = $archive_path = str_replace( "content/entry" ,  "archive" , $data_dir );
					$archive_files = glob ( $archive_path . "/{$entry_id}.*" );
					$archive_file = "MISSING";
					$size = "";
					
					if (count($archive_files))
					{
						$archive_file = pathinfo( $archive_files[0], PATHINFO_BASENAME );
						$size = filesize($archive_files[0]);
					}
					
					$list_type = $size > 100000 ? 0 : 1;
					$cnt = count($lines[$list_type]) + 1;
					$lines[$list_type][] = "<TD>$cnt</TD><TD>$partner_id</TD><TD>$entry_id</TD><TD><A target='_blank' href='/index.php/system/investigate?entry_id=$entry_id'>$data_path</A></TD><TD><A target='_blank' href='/index.php/system/investigate?entry_id=$entry_id'>$archive_file</A></TD><TD>$size</TD><TD>$source</TD><TD>$date</TD><TD>$diff</TD><TD>$batchjob_description</TD>";
				}
			}
			
			for($j = 0; $j < 2; $j++)
			{
				if (!count($lines[$j]))
					continue;
					
				echo "<TABLE>\n";
				echo "<THEAD>\n";
				echo "<TR $headerStyle><TH>#</TH><TH>PARTNER_ID</TH><TH>ID</TH><TH>PATH</TH><TH>ARCHIVE</TH><TH>SIZE</TH><TH>SOURCE</TH><TH>LAST DATE</TH><TH>PASSED TIME</TH><TH>IMPORT ERROR</TH></TR>";
					
				echo "</THEAD>\n";
				echo "<TBODY>\n";
			
				$i = 0;
				foreach($lines[$j] as $line)
				{
					$style = ++$i % 2 ? $evenStyle : $oddStyle;
					echo "<TR $style>$line</TR>";
				}
				
				echo "</TBODY>\n";
				echo "</TABLE>\n";
				echo "<BR/>\n";
			}
		}
	}
	
	private function dumpQuery($query, $title, $status_names = null)
	{
		if (!$status_names)
			$status_names = array();
		
		$headerStyle = "bgcolor='lightgray'";
		$oddStyle = "bgcolor='cyan'";
		$evenStyle = "bgcolor='lightblue'";
		
		$statement = $this->connection->prepareStatement($query);
	    $resultset = $statement->executeQuery();
		
		$has_results = $resultset->getRecordCount() > 0;
		
		echo "<H2>$title ".($has_results ? "" : "- NONE")."</H2>";
		echo $query;
		
		if ($has_results)
		{
			echo "<TABLE>\n";
			echo "<THEAD>\n";
			echo "<TR $headerStyle><TH>STATUS</TH><TH>COUNT</TH><TH>LAST DATE</TH><TH>PASSED TIME</TH></TR>";
			echo "</THEAD>\n";
			echo "<TBODY>\n";
			
			$i = 0;
			while ($resultset->next())
			{
				$style = ++$i % 2 ? $evenStyle : $oddStyle;
    		
				$status = $resultset->getInt("status");
				$status = @$status_names[$status]. " ($status)";
				$count = $resultset->getInt("count");
				$date = $resultset->getDate("date", "%Y-%m-%d %H:%M:%S");
				$diff = $resultset->getString("diff");
				echo "<TR $style><TD>$status</TD><TD>$count</TD><TD>$date</TD><TD>$diff</TD></TR>";
			}
	    
			echo "</TBODY>\n";
			echo "</TABLE>\n";
		}
	}
	
	public function execute()
	{
		ini_set("memory_limit","128M");
		$this->forceSystemAuthentication();
		
		$amount = $this->getP ( "amount" );
		if (!$amount)
			$amount = 1;
			
		$interval = $this->getP ( "interval" );
		if (!$interval)
			$interval = "day";
		
		$partner_id = $this->getP ( "partner_id" );
		if (strlen($partner_id) == 0)
			$partner_id = "ALL";
		
			while(FALSE !== ob_get_clean());
		
		echo "<HTML><BODY>\n";
		echo "<form>\n";
		echo "Last ";
		echo "<input name='amount' type='text' value='$amount'>&nbsp;";
		echo "<input name='interval' type='radio' value='day' ".($interval == "day" ? "checked" : "").">days&nbsp;";
		echo "<input name='interval' type='radio' value='hour' ".($interval == "hour" ? "checked" : "").">hours&nbsp;";
		echo " Filter by partner ";
		echo "<input name='partner_id' type='text' value='$partner_id'>&nbsp;";
		echo "<input type='submit' style='color:black' name='go' value='Go'>\n";
		echo "</form>\n";
		
		$partner_where = ($partner_id == "ALL") ? "" : " AND partner_id='$partner_id' ";

		// use the slave rather than master !
	    $this->connection = Propel::getConnection("propel2");
		
		$date_filter = " ADDDATE(NOW(), INTERVAL -$amount $interval) ";

	    $entry_statuses = array(
			entry::ENTRY_STATUS_ERROR_CONVERTING => "ERROR",
			entry::ENTRY_STATUS_IMPORT => "IMPORT",
			entry::ENTRY_STATUS_PRECONVERT => "PRECONVERT",
			entry::ENTRY_STATUS_READY => "READY",
			entry::ENTRY_STATUS_DELETED => "DELETED",
			entry::ENTRY_STATUS_PENDING => "PENDING",
			entry::ENTRY_STATUS_MODERATE => "MODERATE",
			entry::ENTRY_STATUS_BLOCKED => "BLOCKED"
		);
		
		$query = "select status,count(1) as count,max(created_at) as date,timediff(now(),max(created_at)) as diff from entry where type=1 and created_at>$date_filter $partner_where group by status order by status";
	    
	    $this->dumpQuery($query, "Entries", $entry_statuses);

	    $query = "select status,count(1) as count,max(created_at) as date,timediff(now(),max(created_at)) as diff from entry where type=1 and media_type=1 and created_at>$date_filter $partner_where group by status order by status";
	    
	    $this->dumpQuery($query, "Video Entries", $entry_statuses);
	    
	    $entry_media_types = array(
			entry::ENTRY_MEDIA_TYPE_AUTOMATIC => "Automatic",
			entry::ENTRY_MEDIA_TYPE_ANY => "Any",
			entry::ENTRY_MEDIA_TYPE_VIDEO => "Video",
			entry::ENTRY_MEDIA_TYPE_IMAGE => "Image",
			entry::ENTRY_MEDIA_TYPE_TEXT => "Text",
			entry::ENTRY_MEDIA_TYPE_HTML => "HTML",
			entry::ENTRY_MEDIA_TYPE_AUDIO => "Audio",
			entry::ENTRY_MEDIA_TYPE_SHOW => "Roughcut",
			entry::ENTRY_MEDIA_TYPE_SHOW_XML => "RoughcutXML",
			entry::ENTRY_MEDIA_TYPE_BUBBLES => "Bubbles",
			entry::ENTRY_MEDIA_TYPE_XML => "XML",
			entry::ENTRY_MEDIA_TYPE_DOCUMENT => "Document",
			entry::ENTRY_MEDIA_TYPE_SWF => "SWF"
		);
	    
	    $query = "select media_type as status,count(1) as count,max(created_at) as date,timediff(now(),max(created_at)) as diff from entry where created_at>$date_filter $partner_where group by media_type order by media_type";
	    
	    $this->dumpQuery($query, "Entries by media type", $entry_media_types);

	    $query = "select partner_id,id,source,created_at as date,timediff(now(),created_at) as diff from entry where type=1 and media_type=1 and status=-1 and created_at>$date_filter $partner_where order by int_id desc limit 100";
	    
	    $this->dumpConvertErrors($query);

	    $job_statuses = array(
			BatchJob::BATCHJOB_STATUS_PENDING => "PENDING",
			BatchJob::BATCHJOB_STATUS_QUEUED => "QUEUED", 
			BatchJob::BATCHJOB_STATUS_PROCESSING => "PROCESSING",
			BatchJob::BATCHJOB_STATUS_PROCESSED => "PROCESSED",
			BatchJob::BATCHJOB_STATUS_MOVEFILE => "MOVEFILE",
			BatchJob::BATCHJOB_STATUS_FINISHED => "FINISHED",
			BatchJob::BATCHJOB_STATUS_FAILED => "FAILED",
			BatchJob::BATCHJOB_STATUS_ABORTED => "ABORTED",
		);
		
	    $jobs = array(
	   		BatchJob::BATCHJOB_TYPE_IMPORT => "Import",
	   		BatchJob::BATCHJOB_TYPE_DELETE => "Delete",
	   		BatchJob::BATCHJOB_TYPE_FLATTEN => "Flatten",
	   		BatchJob::BATCHJOB_TYPE_BULKUPLOAD => "Bulk Upload",
	   		BatchJob::BATCHJOB_TYPE_OOCONVERT => "OOConvert"
	   		);
	   		
	   	foreach($jobs as $job_type => $job_name)
	   	{
		    $query = "select status,count(1) as count,max(created_at) as date, timediff(now(),max(created_at)) as diff from batch_job where job_type=$job_type and created_at>$date_filter $partner_where group by status order by status";
		    
		    $this->dumpQuery($query, $job_name, $job_statuses);
		    
			if ($job_type == BatchJob::BATCHJOB_TYPE_OOCONVERT)
			    $this->dumpOOConvertErrors("select partner_id,id,entry_id,created_at as date,timediff(now(),created_at) as diff from batch_job where job_type=$job_type and created_at>$date_filter $partner_where AND status=".BatchJob::BATCHJOB_STATUS_FAILED);
	   	}
	    
	    $notification_statuses = array(
			BatchJob::BATCHJOB_STATUS_PENDING => "PENDING",
			BatchJob::BATCHJOB_STATUS_FINISHED => "SENT",
			BatchJob::BATCHJOB_STATUS_FAILED => "ERROR",
			BatchJob::BATCHJOB_STATUS_RETRY => "SHOULD_RESEND",
//			BatchJob::BATCHJOB_STATUS_FAILED => "ERROR_RESENDING",
//			BatchJob::BATCHJOB_STATUS_FINISHED => "SENT_SYNCH",
		);
		
	   	$query = "select status,count(1) as count,max(created_at) as date, timediff(now(),max(created_at)) as diff from notification where created_at>$date_filter $partner_where group by status order by status";
	    
	    $this->dumpQuery($query, "Notifications", $notification_statuses);
		
	    $mail_statuses = array(
			kMailJobData::MAIL_STATUS_PENDING => "PENDING",
			kMailJobData::MAIL_STATUS_SENT => "SENT",
			kMailJobData::MAIL_STATUS_ERROR => "ERROR"
		);
		
	   	$query = "select status,count(1) as count,max(created_at) as date, timediff(now(),max(created_at)) as diff from mail_job where created_at>$date_filter group by status order by status";
	    
	    $this->dumpQuery($query, "Mail Job", $mail_statuses);
		
	    echo "</BODY></HTML>\n";
		
	    die;
	}
}

?>