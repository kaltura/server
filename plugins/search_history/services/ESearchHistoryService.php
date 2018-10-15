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
        if (!$filter)
            $filter = new KalturaESearchHistoryFilter();

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
