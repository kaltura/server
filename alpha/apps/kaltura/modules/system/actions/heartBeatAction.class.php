<?php
require_once ( "kalturaSystemAction.class.php" );

class heartBeatAction extends kalturaSystemAction 
{
	private $_test_array = NULL;
	private $_bottom_line = null;
	
	/**
	 * Will test the health of the system
	 */
	public function execute()
	{
		$this->forceSystemAuthentication();
		
		$this->_bottom_line = "OK";
		$this->_test_array = array ();
		
		$this->mandatory ( "memcache" , self::testMemcache() );
		$this->mandatory ( "converter client" , self::isProcessRunning ( "batchConvertClient.php" ) );
		
		// make accessible for the view
		$this->test_array = $this->_test_array;
		$this->bottom_line = $this->_bottom_line;
		return sfView::SUCCESS;

	}
	
	public function mandatory ( $alias , $test )
	{
		if ( $test )
		{
			$this->_test_array[$alias] = "OK" ;
		}
		else
		{
			$this->_test_array[$alias] = "ERROR" ;
			$this->_bottom_line = "ERROR";
		}
	}
	
	public static function testMemcache ()
	{
		try
		{
			$dummy_cache = new myCache( "dummy" );
			$val_from_cache = 	$dummy_cache->get( "DUMDUM" );
			$new_val_to_store_in_cache = microtime(true);
			$dummy_cache->put ( "DUMDUM" , $new_val_to_store_in_cache );
			$new_val_to_get_in_cache = $dummy_cache->get( "DUMDUM" );
				
			if ( $new_val_to_get_in_cache == $new_val_to_store_in_cache )	return true;
			if ( $new_val_to_get_in_cache == $val_from_cache )
			{
				// memcache running but problem setting data
				// TODO - return a more specific error
				return false;
			}
			
			return false;
						
		}
		catch ( Exception $ex )
		{
			return false;
		}
	}
	
	public static function isProcessRunning ( $proc_name )
	{
		// using ps & grep - see if the process is running 
		$output = array ();
		$return_value = "";
		
		if( PHP_OS == "Darwin" )
		{
			$cmd_line = 'ps auxw |grep ' . $proc_name . ' | grep -v " grep" ';
		}
		else if( PHP_OS == "Linux" )
		{
			$cmd_line = 'ps auxw |grep ' . $proc_name . ' | grep -v " grep" ';
		}
		else
		{
			// TODO - write a windows bat file that executes cygwin with the command line
			$cmd_line = "cygwin_cmd ";
			$cmd_line .= 'ps auxw |grep ' . $proc_name . ' | grep -v " grep" ';
		}
		
		exec ( $cmd_line , $output , $return_value );
		
		if ( count ( $output ) < 1 )
		{
			return false;
		}
		// TODO 
		// maybe want to verify that there are not too many results - something that might indicate that
		// the grep string is a bad one
		return true;
		
	}
}

?>