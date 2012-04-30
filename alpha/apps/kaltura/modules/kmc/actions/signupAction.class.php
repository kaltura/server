<?php
/**
 * @package    Core
 * @subpackage KMC
 */
require_once ( "kalturaAction.class.php" );

/**
 * @package    Core
 * @subpackage KMC
 */
class signupAction extends kalturaAction
{
	public function execute ( ) 
	{
		$this->redirect("http://corp.kaltura.com/about/signup");
		sfView::SUCCESS;
	}
}
