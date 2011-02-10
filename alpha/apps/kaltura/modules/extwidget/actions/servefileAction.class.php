<?php

class servefileAction extends sfAction
{
	/**
	 * Will forward to the regular swf player according to the widget_id 
	 */
	public function execute()
	{
		requestUtils::handleConditionalGet();
		
		$file_sync_id = $this->getRequestParameter( "id" );
		$hash = $this->getRequestParameter( "hash" );
		$file_name = $this->getRequestParameter( "fileName" );
		if ($file_name) {
			$file_name = base64_decode($file_name);
		}
	
		kDataCenterMgr::serveFileToRemoteDataCenter ( $file_sync_id , $hash, $file_name ); 
		die();
	}
}
?>
