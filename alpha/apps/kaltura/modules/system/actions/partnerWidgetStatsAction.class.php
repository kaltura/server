<?php
require_once ( "kalturaSystemAction.class.php" );
class partnerWidgetStatsAction extends kalturaSystemAction
{
	public function execute()
	{
		ini_set( "memory_limit","64M" );
		
		$this->forceSystemAuthentication();
		
		$partner_id = $this->getP('partner_id');
		if (! $partner_id)
			$partner_id = 2217;
		
		//$path = 'C:/tmp/';
		$path = '/web/tmp/';
		$filename = 'AN_'.date('d_m_y_H_i_s').".csv";
		//$sql = "select count(1),widget_id,command from collect_stats where partner_id=".$partner_id." group by widget_id,command order by widget_id,command into outfile '".$path.$filename."'  FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\n';";
		//$sql = "select count(1),widget_id,command from collect_stats where partner_id=".$partner_id." group by widget_id,command order by widget_id,command";
		$sql = "select count(1),widget_id,weekofyear(date) week_of_the_year,command from collect_stats where partner_id = ".$partner_id." group by week_of_the_year,widget_id,command order by week_of_the_year,widget_id";

		if($this->getP('debug') == 'yes')
		{
			//$sql = "select * from partner where id = ". $partner_id ." into outfile '".$path.$filename."'  FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\n';";
			$sql = "select * from partner where id > ". $partner_id ." and id < ".($partner_id+10);
		}
		//echo $sql;
		$con = sfContext::getInstance()->getDatabaseConnection('propel');
		$objRslt = $con->executeQuery($sql, ResultSet::FETCHMODE_NUM);
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"".$filename."\"");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo 'Week of the year,Widget ID,Command,Count'."\n";
		while($objRslt->next())
		{
			echo $objRslt->get(3) .','. $objRslt->get(2) .','. $objRslt->get(4) .','. $objRslt->get(1)."\n";
		}
		exit();
		/*
		if (filesize($path.$filename))
		{
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"".$filename."\"");
			header("Content-Length: ".filesize($path.$filename));
			header("Pragma: no-cache");
			header("Expires: 0");
		
			readfile($path.$filename);
			unlink($path.$filename);
			exit();
		}
		else
		{
			echo "no results";
			unlink($path.$filename);
			exit();
		}
		*/		
	}
}
?>