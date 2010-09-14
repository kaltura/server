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

//sleep ( 10 );		
		kDataCenterMgr::serveFileToRemoteDataCenter ( $file_sync_id , $hash ); 
		die();
	}
}
?>
