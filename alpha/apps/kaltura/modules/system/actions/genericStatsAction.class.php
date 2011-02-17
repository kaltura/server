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
class genericStatsAction extends kalturaSystemAction
{
	public function execute()
	{
		$this->forceSystemAuthentication();

		$start = microtime(true);
		
		$end_date = $this->getP ( "end_date" , null );
		
		$connection = Propel::getConnection("kaltura_stats");

		$this->cookies_7 = $this->getUvForDays  ( $connection , "unique_visitors_cookie" , 7 , $end_date );
		$this->cookies_30 = $this->getUvForDays  ( $connection , "unique_visitors_cookie" , 30 , $end_date );
//		$this->cookies_180 = $this->getUvForDays  ( $connection , "unique_visitors_cookie" , 180 , $end_date );

		$this->ip_7 = $this->getUvForDays  ( $connection , "unique_visitors_ip" , 7 , $end_date );
		$this->ip_30 = $this->getUvForDays  ( $connection , "unique_visitors_ip" , 30 , $end_date );
//		$this->ip_180 = $this->getUvForDays  ( $connection , "unique_visitors_ip" , 180 , $end_date );
		
		$connection->close();
		
		$end = microtime(true);
		$this->time = $end - $start; 
	}
	
	private function getUvForDays ( $connection , $table_name , $days_back , $end_date = null)
	{
		$col = ( $table_name == "unique_visitors_cookie" ? "uv_id" : "ip" );
		
		$end_date_str = $end_date ? 
			"date>=ADDDATE(\"{$end_date}\",-{$days_back}) and date<=\"{$end_date}\"" : 
			"date>=ADDDATE(now(),-{$days_back}) ";
 		
		$query = "select count(distinct({$col})) as cnt ,min(date) as  start_date, max(date) as end_date from $table_name where {$end_date_str};";
		$statement = $connection->prepareStatement($query);
		$resultset = $statement->executeQuery();	
		while ($resultset->next())
		{
			return array ( $resultset->getInt('cnt') , $resultset->getTimestamp ('start_date') , $resultset->getTimestamp ('end_date') );
		}
		
		return 0;
	}
}
?>