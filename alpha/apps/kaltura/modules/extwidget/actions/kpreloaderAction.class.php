<?php
/**
 * @package Core
 * @subpackage externalWidgets
 */
class kpreloaderAction extends sfAction
{
	/**
	 * Will forward to the regular swf player according to the widget_id 
	 */
	public function execute()
	{
		$ui_conf_id = $this->getRequestParameter( "ui_conf_id" );
		if(!is_numeric($ui_conf_id))
			throw new Exception("Illegal Input was supplied");

		$preloader_path = myContentStorage::getFSContentRootPath()."/content" . myContentStorage::getFSUiconfRootPath() . "/preloader_$ui_conf_id.swf";
		
		if (!file_exists($preloader_path))
			$preloader_path = myContentStorage::getFSContentRootPath()."/content" . myContentStorage::getFSUiconfRootPath() . "/preloader_2.swf";
		
		kFileUtils::dumpFile($preloader_path);
	}
}
