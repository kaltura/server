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
class changeSettingAction extends kalturaAction
{
	public function execute() 
	{
		$this->title = "Hello World!";
		sfView::SUCCESS;
	}
}