<?php
class testNotificationsAction extends sfAction 
{
	public function execute () 
	{
		$dummy = $this->getRequestParameter( "votes" , 0 );
		$dummy = $dummy %3;
		if ( $dummy == 1 )			
		{
			if ( rand(1,2) == 1 )
				$res = "-1";
			else
				$res = "0";
			
		}
		elseif ( $dummy == 2 )			$res = "-2";
		else $res = "0";
		
		$res = "0";
		
		list ( $not_list  , $signature , $debug ) = myNotificationMgr::splitMultiNotifications ( $_REQUEST );
		
		
$myFile = "/var/log/notifications_log";
$fh = fopen($myFile, 'a') ;
fwrite($fh, microtime(true) . ":\n" );
fwrite($fh, print_r ( $_REQUEST , true ) . "\n"  );
fwrite($fh, "- not_list: ---\n");
fwrite($fh, print_r ( $not_list , true ) . "\n" );
//fwrite($fh, print_r ( $debug , true ) . "\n" );
 
fwrite($fh, $signature  . "\n" );
fwrite($fh, "---------------------------------------\n");
fclose($fh);
		

		return $this->renderText( $res );
	}
}
?>