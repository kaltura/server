<?php
/**
 * @service searchHistory
 * @package plugins.searchHistory
 * @subpackage api.services
 */
class ESearchHistoryService extends KalturaBaseService 
{

	/**
	 * @action list
	 * @param KalturaESearchHistoryFilter|null $filter
	 * @return KalturaESearchHistoryListResponse
	 * @throws KalturaAPIException
	 */
	public function listAction(KalturaESearchHistoryFilter $filter = null)
	{
		if(!$filter)
		{
			$filter = new KalturaESearchHistoryFilter();
		}

		try
		{
			$response = $filter->getListResponse();
		}
		catch (kESearchHistoryException $e)
		{
			$this->handleSearchHistoryException($e);
		}
		return $response;
	}

	/**
	 * @action delete
	 * @param string $searchTerm
	 * @throws KalturaAPIException
	 */
	public function deleteAction($searchTerm)
	{
		if (is_null($searchTerm) || $searchTerm == '')
		{
			throw new KalturaAPIException(KalturaESearchHistoryErrors::EMPTY_DELETE_SEARCH_TERM_NOT_ALLOWED);
		}

		try
		{
			$historyClient = new kESearchHistoryElasticClient();
			$historyClient->deleteSearchTermForUser($searchTerm);
		}
		catch (kESearchHistoryException $e)
		{
			$this->handleSearchHistoryException($e);
		}
	}

	/**
	 * @action exportToCsv
	 * @param KalturaESearchHistoryFilter $filter A filter used to aggregate the search terms
	 * @return string
	 * @throws KalturaAPIException
	 */
	function exportToCsvAction(KalturaESearchHistoryFilter $filter)
	{
		if(!$filter)
		{
			$filter = new KalturaESearchHistoryFilter();
		}

		$dbFilter = new ESearchHistoryFilter();
		$filter->toObject($dbFilter);

		$kuser = $this->getKuser();
		if(!$kuser || !$kuser->getEmail())
		{
			throw new KalturaAPIException(APIErrors::USER_EMAIL_NOT_FOUND, $kuser);
		}

		$jobData = new kSearchHistoryCsvJobData();
		$jobData->setFilter($dbFilter);
		$jobData->setUserMail($kuser->getEmail());
		$jobData->setUserName($kuser->getPuserId());

		kJobsManager::addExportCsvJob($jobData, $this->getPartnerId(), SearchHistoryPlugin::getExportTypeCoreValue(SearchHistoryExportObjectType::SEARCH_TERM));

		return $kuser->getEmail();
	}

	private function handleSearchHistoryException($exception)
	{
		$code = $exception->getCode();
		$data = $exception->getData();
		switch ($code)
		{
			case kESearchHistoryException::INVALID_USER_ID:
				throw new KalturaAPIException(KalturaESearchHistoryErrors::INVALID_USER_ID);

			default:
				throw new KalturaAPIException(KalturaESearchHistoryErrors::INTERNAL_SERVERL_ERROR);
		}
	}

}
