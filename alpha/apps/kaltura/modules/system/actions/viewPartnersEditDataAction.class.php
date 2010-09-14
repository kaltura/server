<?php

require_once ( "kalturaSystemAction.class.php" );
require_once ( "viewPartnersAction.class.php" );

class viewPartnersEditDataAction extends kalturaSystemAction
{
	public function execute()
	{
		ini_set("memory_limit","128M");
		ini_set("max_execution_time","240");
		$this->forceSystemAuthentication();

		$start = microtime(true);
		
		$data = $this->getP ( "data" );
		$file_path = dirname ( __FILE__ ) . "/../data/viewPartnersData.txt" ;
		if ( $data ) 
		{
			$time_str = strftime( "-%Y-%d-%m_%H-%M-%S" , time() ); 
			kFile::moveFile ( $file_path , $file_path.$time_str );
			file_put_contents( $file_path , $data ); // sync - OK
		}
		else
		{
			$data = file_get_contents( $file_path );
		}
		
		$partner_groups = new partnerGroups ( $file_path );
		$this->partner_group_list = $partner_groups->partner_group_list;

		$this->data = $data;
	}
}
?>