<?php
/**
 * @package Core
 * @subpackage externalWidgets
 */
class servefileAction extends sfAction
{
	/**
	 * Will forward to the regular swf player according to the widget_id 
	 */
	public function execute()
	{
		try
		{
			KExternalErrors::setResponseErrorCode(KExternalErrors::HTTP_STATUS_NOT_FOUND);

			requestUtils::handleConditionalGet();
			
			$file_sync_id = $this->getRequestParameter( "id" );
			$hash = $this->getRequestParameter( "hash" );
			$file_name = $this->getRequestParameter( "fileName" );
			if ($file_name) {
				$file_name = base64_decode($file_name);
			}
			
			$file_sync = FileSyncPeer::retrieveByPk ( $file_sync_id );
			if ( ! $file_sync )
			{
				$current_dc_id = kDataCenterMgr::getCurrentDcId();
				$error = "DC[$current_dc_id]: Cannot find FileSync with id [$file_sync_id]";
				KalturaLog::err($error);
				KExternalErrors::dieError(KExternalErrors::FILE_NOT_FOUND);
			}
			
			kDataCenterMgr::serveFileToRemoteDataCenter ( $file_sync , $hash, $file_name );
			die();
		}
		catch (Exception $e)
		{
			KalturaLog::err("Exception caught during execution with error messsage [" . $e->getMessage() . "]");
			KExternalErrors::dieError(KExternalErrors::INTERNAL_SERVER_ERROR);
		}
			
	}
}
