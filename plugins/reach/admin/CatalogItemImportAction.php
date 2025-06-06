<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class CatalogItemImportAction extends KalturaApplicationPlugin
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
		$csvPath = self::getFile();
		if (!$csvPath)
		{
			$action->view->errMessage = 'File is missing';
			return;
		}

		$client = Infra_ClientHelper::getClient();
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($client);
		try
		{
			$bulkUploadResult = $reachPluginClient->vendorCatalogItem->addFromBulkUpload($csvPath);
			$bulkUploadId = $bulkUploadResult->id;
			if ($bulkUploadId)
			{
				$action->view->bulkUploadId = $bulkUploadId;
			}

			unlink($csvPath);
		}
		catch (Exception $e)
		{
			KalturaLog::err('Error in vendorCatalogItem->addFromBulkUpload ' . $e->getMessage());
			$action->view->errMessage =  $e->getMessage();
		}
	}

	protected function getFile()
	{
		$upload = new Zend_File_Transfer_Adapter_Http();
		$files = $upload->getFileInfo();

		$fileData = null;
		if(isset($files['csvFile']))
		{
			$fileData = self::getFileContent($files['csvFile']);
			$csvPath = tempnam(sys_get_temp_dir(), 'csv');
			file_put_contents ($csvPath, $fileData);
			return $csvPath;
		}

		return null;
	}

	protected static function getFileContent(array $file)
	{
		if ($file['error'] === UPLOAD_ERR_OK)
		{
			return file_get_contents($file['tmp_name']);
		}
		return null;
	}
}
