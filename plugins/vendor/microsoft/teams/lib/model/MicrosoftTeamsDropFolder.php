<?php

/*
* @package plugins.microsoftTeamsDropFolder
* @subpackage model
*/
class MicrosoftTeamsDropFolder extends VendorIntegrationDropFolder
{
	const MS_TEAMS_GRAPH_API_URL = 'https://graph.microsoft.com/v1.0';

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