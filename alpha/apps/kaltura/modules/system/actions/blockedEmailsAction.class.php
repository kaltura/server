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
class blockedEmailsAction extends kalturaSystemAction
{
	public function execute()
	{
		$this->forceSystemAuthentication();		
	}
}
?>