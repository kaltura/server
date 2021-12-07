<?php

/*
* @package plugins.KTeams
* @subpackage model
*/
class TeamsDropFolder extends VendorIntegrationDropFolder
{
	const MS_TEAMS_GRAPH_API_URL = 'https://graph.microsoft.com/v1.0';
	
	const IS_INITIALIZED = 'is_initialized';
	
	const USER_FILTER_TAG = 'user_filter_tag';

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
	
	/**
	 * @param string $v
	 */
	public function setIsInitialized($v)
	{
		return self::IS_INITIALIZED;
	}
	
	/**
	 * @return bool
	 */
	public function getIsInitialized()
	{
		return self::IS_INITIALIZED;
	}
	
	/**
	 * @param string $v
	 */
	public function setUserFilterTag($v)
	{
		return self::USER_FILTER_TAG;
	}
	
	/**
	 * @return bool
	 */
	public function getUserFilterTag()
	{
		return self::USER_FILTER_TAG;
	}
}