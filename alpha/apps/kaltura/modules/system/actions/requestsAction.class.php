<?php

require_once ( "kalturaSystemAction.class.php" );

class requestsAction extends kalturaSystemAction
{
	public function execute()
	{
		ini_set("memory_limit","128M");
		header('Content-Type: text/plain');

		for($i = 1; $i <= 7; $i++)
		{
			if ($i == 4)
				continue;
				
			echo "APACHE$i:\n";
			ob_start();
			passthru("tail -10000 /web/logs/APACHE$i-access_log|head -1");
			passthru("date");
			passthru("tail -10000 /web/logs/APACHE$i-access_log|php /web/kaltura/support_prod/monitor/request_types.php");
			$result = ob_get_contents();
			ob_end_clean();
			echo $result;
		}

		die;
	}
}

?>
