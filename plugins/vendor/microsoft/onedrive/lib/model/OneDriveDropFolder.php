<?php

/*
* @package plugins.OneDrive
* @subpackage model
*/
class OneDriveDropFolder extends VendorIntegrationDropFolder
{
	const MS_TEAMS_GRAPH_API_URL = 'https://graph.microsoft.com/v1.0';
	
	const DEFAULT_CATEGORY_IDS = 'default_category_ids';
	
	public function getDefaultCategoryIds ()	{	return $this->getFromCustomData(self::DEFAULT_CATEGORY_IDS);	}
	
	public function setDefaultCategoryIds ($defaultCategoryIds)	{	$this->putInCustomData(self::DEFAULT_CATEGORY_IDS, $defaultCategoryIds);	}

	protected function getRemoteFileTransferMgrType()
	{
		return kFileTransferMgrType::HTTP;
	}

	/**
	 * @inheritDoc
	 */
	public function getImportJobData()
	{
		return new kDropFolderImportJobData();
	}

	/**
	 * @inheritDoc
	 */
	public function getFolderUrl()
	{
		return self::MS_TEAMS_GRAPH_API_URL;
	}
}