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