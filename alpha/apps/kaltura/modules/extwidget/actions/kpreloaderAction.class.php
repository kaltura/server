<?php

class kpreloaderAction extends sfAction
{
	/**
	 * Will forward to the regular swf player according to the widget_id 
	 */
	public function execute()
	{
		$ui_conf_id = $this->getRequestParameter( "ui_conf_id" );

		$preloader_path = myContentStorage::getFSContentRootPath()."/content" . myContentStorage::getFSUiconfRootPath() . "/preloader_$ui_conf_id.swf";
		
		if (!file_exists($preloader_path))
			$preloader_path = myContentStorage::getFSContentRootPath()."/content" . myContentStorage::getFSUiconfRootPath() . "/preloader_2.swf";
		
		kFile::dumpFile($preloader_path);
	}
}
?>
