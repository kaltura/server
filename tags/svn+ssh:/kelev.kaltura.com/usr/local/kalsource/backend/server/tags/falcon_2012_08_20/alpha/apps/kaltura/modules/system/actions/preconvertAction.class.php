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
class preconvertAction extends kalturaSystemAction
{
	/**
	 * Will investigate a single entry
	 */
	public function execute()
	{
		$this->forceSystemAuthentication();

		myDbHelper::$use_alternative_con = null;//myDbHelper::DB_HELPER_CONN_PROPEL2;
		
		$status_list= $this->getP ( "status");
		$mode = $this->getP ( "mode");
		
		if ( $mode ==  "old" )
		{
			$preconvert = glob ( myContentStorage::getFSContentRootPath() . "/content/preconvert/data/*" );
			$preconvert_indicators = glob ( myContentStorage::getFSContentRootPath() . "/content/preconvert/files/*" ); // indicators or inproc
		}
		else
		{
			$preconvert = glob ( myContentStorage::getFSContentRootPath() . "/content/new_preconvert/*" );
			$preconvert_indicators = glob ( myContentStorage::getFSContentRootPath() . "/new_content/preconvert/*.in*" ); // indicators or inproc
		}

		$indicators = array();
		foreach ( $preconvert_indicators as $file )
		{
			$file = pathinfo ( $file , PATHINFO_BASENAME );
			$name =  substr( $file , 0 , strpos ( $file , ".") );
			$indicators[$name]=$name; 
		}
		
		$entry_ids = array();
		
		foreach ( $preconvert as $file )
		{
			$file = pathinfo ( $file , PATHINFO_BASENAME );
			$name =  substr( $file , 0 , strpos ( $file , ".") );
			if ( ! isset ($indicators[$name]))
			{
				$entry_ids[] = $name;  // only those that don't have indicators
			}
			
		}

		$ids_str = "'" . implode ( "','" , $entry_ids ) . "'";
		
		echo "<html><body style='font-family:arial; font-size:12px;'>";
		echo "preconvert files: [" . count ( $preconvert ) ."]<br>";
		echo "preconvert indicators : [" . count ( $preconvert_indicators ) ."] [" . count ( $indicators ) . "]<br>";
		echo "entry_ids: [" . count ( $entry_ids ) ."]<br>";
		
		if ( count($entry_ids ))
		{
			if ( !$status_list ) $status_list = "1"; 
			$connection = Propel::getConnection();
		    $query = "SELECT id,partner_id,status,created_at FROM entry WHERE status IN ($status_list) AND id IN ($ids_str) ORDER BY created_at ASC ";
			
		    echo "query: $query<br>";
		    
			$statement = $connection->prepareStatement($query);
			$resultset = $statement->executeQuery();	
			
			echo "<table cellpadding=2 cellspacing=0 border=1 style='font-family:arial; font-size:12px;'>";
			echo "<tr><td>partner_id</td><td>id</td><td>status</td><td>created_at</td></tr>";
			
			$real_count=0;
			while ($resultset->next())
		    {
		    	echo "<tr>" .
		    		"<td>" . $resultset->getInt('partner_id') . "</td>" .
		    		"<td>" . $resultset->getString ('id') . "</td>" .
		    		"<td>" . $resultset->getInt('status') . "</td>" .
		    		"<td>" . $resultset->get('created_at') . "</td>" .
		    		"</tr>";
		    	$real_count++;
	   	    }	  
	
	   	    echo "</table>";
	   	    echo "count [$real_count]";
	   	    
	   	    $resultset->close();
		}
   	    echo "</body></html>";
   	    
   	    
   	    die();
   	    
	}
}
?>