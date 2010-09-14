<?php
require_once ( "kalturaSystemAction.class.php" );

class dashAction extends kalturaSystemAction
{
	/**
	 * Will give a good view of the batch processes in the system
	 */
	public function execute()
	{
		$this->systemAuthenticated();
		
	}
}

?>