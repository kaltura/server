<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class CatalogItemImportResultAction extends KalturaApplicationPlugin
{
	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}

	public function doAction(Zend_Controller_Action $action)
	{
		$action->getHelper('layout')->disableLayout();
		$request = $action->getRequest();
		$bulkUploadId = $request->getParam('bulk_upload_id');
		if (!$bulkUploadId)
		{
			$action->view->errMessage = 'Bulk upload id is missing';
			return;
		}

		$client = Infra_ClientHelper::getClient();
		$bulkPluginClient = Kaltura_Client_BulkUpload_Plugin::get($client);
		try
		{
			$bulkUploadResult = $bulkPluginClient->bulk->get($bulkUploadId);
			$action->view->bulkUploadResult = $bulkUploadResult;
		}
		catch (Exception $e)
		{
			KalturaLog::err('Error in bulk->get ' . $e->getMessage());
			$action->view->errMessage =  $e->getMessage();
			return;
		}

		try
		{
			$logFileUrl = $bulkPluginClient->bulk->serveLog($bulkUploadId);
			$action->view->logFileUrl = $logFileUrl;
		}
		catch (Exception $e)
		{
			KalturaLog::err('Error in bulk->serveLog ' . $e->getMessage());
			$action->view->errMessage =  $e->getMessage();
		}
	}
}
